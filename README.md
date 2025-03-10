# Парсер формата обмена 1С
Эта PHP-библиотека разбирает файлы формата 1С для обмена банковской информацией: выписки со счёта, платёжные документы, инкассовые поручения и тому подобные документы.


## Установка
Установка [Композером](https://getcomposer.org/):
```shell
composer require maximal/1c-client-bank-exchange
```


## Пример использования
Базовый пример использования — в файле [example.php](example.php):
```php
require_once __DIR__ . '/vendor/autoload.php';

use Maximal\ClientBankExchange1C\DocumentSection;
use Maximal\ClientBankExchange1C\Exceptions\EncodingException;
use Maximal\ClientBankExchange1C\Exceptions\UnreadableFileException;
use Maximal\ClientBankExchange1C\Parser;

$parser = new Parser();
try {
	$parser->loadFile(__DIR__ . '/Выписка.txt');
	// Или
	//$parser->loadDocument(file_get_contents(__DIR__ . '/Выписка.txt'));
} catch (EncodingException $exception) {
	// Ошибка декодирования файла из исходной кодировки во внутреннюю (UTF-8)
} catch (UnreadableFileException $exception) {
	// Файл недоступен для чтения
}

if ($parser->parse()) {
	// Дата и время создания выписки
	var_dump($parser->getRootSection()->getCreationTime());
	foreach ($parser->getRootSection()->getSections() as $section) {
		if ($section instanceof DocumentSection) {
			// Тип документа (DocumentType, string)
			var_dump($section->getType());
			var_dump($section->getTypeName());
			// Номер документа (string)
			var_dump($section->getNumber());
			// Дата документа (DateTimeImmutable)
			var_dump($section->getDate());
			// Дата списания (DateTimeImmutable)
			var_dump($section->getWithdrawalDate());
			// Назначение платежа (string)
			var_dump($section->getPaymentPurpose());
			// Сумма в копейках (fixed point, int)
			var_dump($section->getAmountFixed());
			// Сумма в рублях (floating point)
			var_dump($section->getAmountFloat());
		}
	}
} else {
	// Ошибка разбора файла
	var_dump($parser->getLineNumber());
	var_dump($parser->getState());
}
```


## Автор
* https://github.com/maximal
* https://maximals.ru/
* https://sijeko.ru/
