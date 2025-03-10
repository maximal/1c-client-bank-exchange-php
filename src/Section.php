<?php

namespace Maximal\ClientBankExchange1C;

use DateInvalidTimeZoneException;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Секция
 */
class Section
{
	/**
	 * @var non-empty-string
	 */
	protected string $name;

	/**
	 * @var array<non-empty-string,string>
	 */
	protected array $fields = [];

	/**
	 * @api
	 */
	public function __construct(string $name)
	{
		$this->name = $name;
	}

	/**
	 * @api
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @api
	 */
	public function getField(string $key): ?string
	{
		return $this->fields[$key] ?? null;
	}

	/**
	 * @api
	 */
	public function setField(string $key, string $value): void
	{
		$this->fields[$key] = $value;
	}

	/**
	 * @api
	 */
	public function clearFields(): void
	{
		$this->fields = [];
	}

	/**
	 * @throws DateInvalidTimeZoneException
	 */
	public function getDateField(
		string $field,
		string|DateTimeZone $timezone = 'MSK',
		string $dateFormat = 'd.m.Y',
	): ?DateTimeImmutable {
		$date = $this->getField($field);
		if (!$date) {
			return null;
		}
		$result = DateTimeImmutable::createFromFormat(
			$dateFormat . ' H:i:s',
			$date . ' 00:00:00',
			$timezone instanceof DateTimeZone ? $timezone : new DateTimeZone($timezone),
		);
		return $result ?: null;
	}

	/**
	 * @throws DateInvalidTimeZoneException
	 * @api
	 */
	public function getDateTimeField(
		string $field,
		string|DateTimeZone $timezone = 'MSK',
		string $dateTimeFormat = 'd.m.Y H:i:s',
	): ?DateTimeImmutable {
		$value = $this->getField($field);
		if (!$value) {
			return null;
		}
		$result = DateTimeImmutable::createFromFormat(
			$dateTimeFormat,
			$value,
			$timezone instanceof DateTimeZone ? $timezone : new DateTimeZone($timezone),
		);
		return $result ?: null;
	}

	/**
	 * @throws DateInvalidTimeZoneException
	 * @api
	 */
	public function getDateTimeFields(
		string $dateField,
		string $timeField,
		string|DateTimeZone $timezone = 'MSK',
		string $dateFormat = 'd.m.Y',
		string $timeFormat = 'H:i:s',
	): ?DateTimeImmutable {
		$dateValue = $this->getField($dateField);
		$timeValue = $this->getField($timeField);
		if (!$dateValue || !$timeValue) {
			return null;
		}
		$result = DateTimeImmutable::createFromFormat(
			$dateFormat . ' ' . $timeFormat,
			$dateValue . ' ' . $timeValue,
			$timezone instanceof DateTimeZone ? $timezone : new DateTimeZone($timezone),
		);
		return $result ?: null;
	}

	/**
	 * @api
	 */
	public function getFloatField(string $field): ?float
	{
		$value = $this->getField($field);
		if (!$value) {
			return null;
		}
		return (float)$value;
	}

	/**
	 * @api
	 */
	public function getIntField(string $field): ?int
	{
		$value = $this->getField($field);
		if (!$value) {
			return null;
		}
		return (int)$value;
	}

	/**
	 * Валюта с плавающей точкой (рубли, доллары, ...)
	 *
	 * @see getCurrencyFixedField()
	 * Внимание! При работе с деньгами рекомендуется использовать значения
	 * с фиксированной точкой из-за особенностей округления `float`.
	 *
	 * @api
	 */
	public function getCurrencyFloatField(string $field): ?float
	{
		return $this->getFloatField($field);
	}


	/**
	 * Валюта с фиксированной точкой (копейки, центы, ...)
	 *
	 * @api
	 */
	public function getCurrencyFixedField(string $field): ?int
	{
		$value = $this->getField($field);
		if (!$value) {
			// Нет значения
			return null;
		}
		$parts = preg_split('/[.,]/', $value, 2);
		if (count($parts) === 1) {
			// '55666' → 55_666_00
			return 100 * (int)$parts[0];
		}
		if ($parts[1] === '') {
			// '55666.' → 55_666_00
			return 100 * (int)$parts[0];
		}
		if (strlen($parts[1]) === 1) {
			// '55666.7' → 55_666_70
			return 100 * (int)$parts[0] + 10 * (int)$parts[1];
		}
		// '55666.78xx' → 55_666_78
		return
			100 * (int)$parts[0] +
			(isset($parts[1]) ? (int)substr($parts[1], 0, 2) : 0);
	}
}
