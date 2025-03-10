<?php

namespace Maximal\ClientBankExchange1C;

use Maximal\ClientBankExchange1C\Exceptions\EncodingException;
use Maximal\ClientBankExchange1C\Exceptions\UnreadableFileException;

/**
 * Парсер формата `1CClientBankExchange`.
 *
 * Пример использования:
 * ```
 * $parser = new Parser();
 * try {
 *     $parser->loadFile(__DIR__ . '/Выписка.txt');
 *     // Или
 *     //$parser->loadDocument(file_get_contents(__DIR__ . '/Выписка.txt'));
 * } catch (EncodingException $exception) {
 *     // Ошибка декодирования файла из исходной кодировки во внутреннюю (UTF-8)
 * } catch (UnreadableFileException $exception) {
 *     // Файл недоступен для чтения
 * }
 * if ($parser->parse()) {
 *     // Дата и время создания выписки
 *     var_dump($parser->getRootSection()->getCreationTime());
 *     foreach ($parser->getRootSection()->getSections() as $section) {
 *         if ($section instanceof DocumentSection) {
 *             // Тип документа (DocumentType, string)
 *             var_dump($section->getType());
 *             var_dump($section->getTypeName());
 *             // Номер документа (string)
 *             var_dump($section->getNumber());
 *             // Дата документа (DateTimeImmutable)
 *             var_dump($section->getDate());
 *             // Дата списания (DateTimeImmutable)
 *             var_dump($section->getWithdrawalDate());
 *             // Назначение платежа (string)
 *             var_dump($section->getPaymentPurpose());
 *             // Сумма в копейках (fixed point, int)
 *             var_dump($section->getAmountFixed());
 *             // Сумма в рублях (floating point)
 *             var_dump($section->getAmountFloat());
 *         }
 *     }
 * } else {
 *     // Ошибка разбора файла
 *     var_dump($parser->getLineNumber());
 *     var_dump($parser->getState());
 * }
 * ```
 */
final class Parser
{
	public const string DEFAULT_FILE_ENCODING = 'CP1251';

	private string $text;

	private ParserState $state = ParserState::Init;
	private int $lineNumber = 0;

	private ?RootSection $rootSection;

	/**
	 * Загрузить документ из текста
	 *
	 * @throws EncodingException
	 *
	 * @api
	 */
	public function loadDocument(string $text, string $encoding = self::DEFAULT_FILE_ENCODING): void
	{
		$converted = iconv($encoding, 'UTF-8', $text);
		if ($converted === false) {
			throw new EncodingException(
				'Failed to convert document text to UTF-8 from ' . $encoding
			);
		}
		$this->text = $converted;
	}

	/**
	 * Загрузить документ из файла
	 *
	 * @throws UnreadableFileException
	 * @throws EncodingException
	 *
	 * @api
	 */
	public function loadFile(string $filename, string $encoding = self::DEFAULT_FILE_ENCODING): void
	{
		if (!is_readable($filename)) {
			throw new UnreadableFileException('Unable to read file: ' . $filename);
		}
		$this->loadDocument(file_get_contents($filename), $encoding);
	}

	/**
	 * Разобрать текст документа на секции
	 *
	 * @api
	 */
	public function parse(): bool
	{
		$lines = preg_split('/(\r\n|\r|\n)/', $this->text);
		$rootSection = null;
		$currentSection = null;
		$currentDocumentSection = null;
		$this->state = ParserState::Init;
		$this->lineNumber = 0;
		foreach ($lines as $line) {
			$this->lineNumber++;
			$line = trim($line);
			if ($line === '') {
				// Пропускаем пустые строки
				continue;
			}

			if ($line === '1CClientBankExchange') {
				// Заголовок: начало файла
				$this->state = ParserState::FileBegin;
				$rootSection = new RootSection();
				$rootSection->clearSections();
				continue;
			}

			if ($this->state === ParserState::Init) {
				// Заголовок файла не найден, ошибка
				$this->state = ParserState::NoHeader;
				break;
			}

			if ($line === 'КонецФайла') {
				// Конец файла
				$this->state = ParserState::FileEnd;
				break;
			}

			if (preg_match('/^СекцияДокумент=(.+)$/ui', $line, $match)) {
				// Начало секции документа
				$this->state = ParserState::DocumentBegin;
				$currentDocumentSection = new DocumentSection($match[1]);
				$currentDocumentSection->clearFields();
				continue;
			}
			if (preg_match('/^КонецДокумента$/ui', $line, $match)) {
				// Конец секции документа
				$this->state = ParserState::DocumentEnd;
				if ($rootSection && $currentDocumentSection) {
					$rootSection->addSection($currentDocumentSection);
				}
				continue;
			}

			if (preg_match('/^Секция([^=]+)$/ui', $line, $match)) {
				// Начало обычной секции
				$this->state = ParserState::SectionBegin;
				$currentSection = new Section($match[1]);
				$currentSection->clearFields();
				continue;
			}
			if (preg_match('/^Конец([^=]+)$/ui', $line)) {
				// Конец обычной секции
				$this->state = ParserState::SectionEnd;
				if ($rootSection && $currentSection) {
					$rootSection->addSection($currentSection);
				}
				continue;
			}

			if (preg_match('/^([^=]+)=(.*)$/u', $line, $match)) {
				// Поле секции
				if ($this->state === ParserState::FileBegin && $rootSection) {
					// Корневая секция
					$rootSection->setField($match[1], $match[2]);
				} elseif ($this->state === ParserState::DocumentBegin && $currentDocumentSection) {
					// Секция документа
					$currentDocumentSection->setField($match[1], $match[2]);
				} elseif ($this->state === ParserState::SectionBegin && $currentSection) {
					// Обычная секция
					$currentSection->setField($match[1], $match[2]);
				}
			}
			//echo $line, PHP_EOL;
		}
		$this->state = $this->state === ParserState::FileEnd
			? ParserState::Success
			: ParserState::NoEndOfFile;
		$this->rootSection = $rootSection;
		return $this->isSuccessful();
	}

	/**
	 * Текущее состояние разбора
	 *
	 * @api
	 */
	public function getState(): ParserState
	{
		return $this->state;
	}

	/**
	 * Текущий номер строки (полезно при нахождении ошибок синтаксиса документа)
	 *
	 * @return int<1,max>
	 *
	 * @api
	 */
	public function getLineNumber(): int
	{
		return $this->lineNumber;
	}

	/**
	 * Был ли успешен последний разбор?
	 *
	 * @see parse()
	 *
	 * @api
	 */
	public function isSuccessful(): bool
	{
		return $this->state === ParserState::Success && $this->rootSection !== null;
	}

	/**
	 * Корневая секция
	 *
	 * @api
	 */
	public function getRootSection(): ?RootSection
	{
		return $this->rootSection;
	}
}
