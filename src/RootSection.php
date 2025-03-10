<?php

namespace Maximal\ClientBankExchange1C;

use DateInvalidTimeZoneException;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Корневая секция (содержит в себе список остальных секций)
 */
final class RootSection extends Section
{
	/**
	 * @var list<Section>
	 */
	private array $sections = [];

	public function __construct()
	{
		parent::__construct('root');
	}

	public function addSection(Section $section): void
	{
		$this->sections[] = $section;
	}

	/**
	 * @return list<Section>
	 */
	public function getSections(): array
	{
		return $this->sections;
	}

	public function clearSections(): void
	{
		$this->sections = [];
	}

	/**
	 * Версия формата
	 *
	 * @api
	 */
	public function getFormatVersion(): ?string
	{
		return $this->getField('ВерсияФормата');
	}

	/**
	 * Кодировка файла
	 *
	 * @api
	 */
	public function getEncoding(): ?string
	{
		return $this->getField('Кодировка');
	}

	/**
	 * Отправитель файла
	 *
	 * @api
	 */
	public function getSender(): ?string
	{
		return $this->getField('Отправитель');
	}

	/**
	 * Получатель файла
	 *
	 * @api
	 */
	public function getReceiver(): ?string
	{
		return $this->getField('Получатель');
	}

	/**
	 * Время создания файла
	 *
	 * @throws DateInvalidTimeZoneException
	 * @api
	 */
	public function getCreationTime(string|DateTimeZone $timezone = 'MSK'): ?DateTimeImmutable
	{
		return $this->getDateTimeFields('ДатаСоздания', 'ВремяСоздания', $timezone);
	}

	/**
	 * Дата начала (например, выписки)
	 *
	 * @throws DateInvalidTimeZoneException
	 *
	 * @api
	 */
	public function getStartDate(string|DateTimeZone $timezone = 'MSK'): ?DateTimeImmutable
	{
		return $this->getDateField('ДатаНачала', $timezone);
	}

	/**
	 * Дата окончания (например, выписки)
	 *
	 * @throws DateInvalidTimeZoneException
	 *
	 * @api
	 */
	public function getEndDate(string|DateTimeZone $timezone = 'MSK'): ?DateTimeImmutable
	{
		return $this->getDateField('ДатаКонца', $timezone);
	}

	/**
	 * Расчётный счёт
	 *
	 * @api
	 */
	public function getAccount(): ?string
	{
		return $this->getField('РасчСчет') ?? $this->getField('РасчСчёт');
	}
}
