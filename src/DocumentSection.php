<?php

namespace Maximal\ClientBankExchange1C;

use DateInvalidTimeZoneException;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Секция документа
 */
final class DocumentSection extends Section
{
	/**
	 * @var non-empty-string
	 */
	protected string $typeName;
	protected DocumentType $type;

	/**
	 * @api
	 */
	public function __construct(string $type)
	{
		parent::__construct('document');
		$this->typeName = $type;
		$this->type = DocumentType::fromName($type);
	}

	/**
	 * Название типа документа
	 *
	 * @api
	 */
	public function getTypeName(): string
	{
		return $this->typeName;
	}

	/**
	 * Тип документа
	 *
	 * @api
	 */
	public function getType(): DocumentType
	{
		return $this->type;
	}

	/**
	 * Номер документа
	 *
	 * @api
	 */
	public function getNumber(): ?string
	{
		return $this->getField('Номер');
	}

	/**
	 * Дата документа
	 *
	 * @throws DateInvalidTimeZoneException
	 *
	 * @api
	 */
	public function getDate(string|DateTimeZone $timezone = 'MSK'): ?DateTimeImmutable
	{
		return $this->getDateField('Дата', $timezone);
	}

	/**
	 * Дата списания
	 *
	 * @throws DateInvalidTimeZoneException
	 *
	 * @api
	 */
	public function getWithdrawalDate(string|DateTimeZone $timezone = 'MSK'): ?DateTimeImmutable
	{
		return $this->getDateField('ДатаСписано', $timezone);
	}

	/**
	 * Сумма с плавающей точкой (рубли, доллары, ...)
	 *
	 * @see getAmountFixed()
	 * Внимание! При работе с деньгами рекомендуется использовать значения
	 * с фиксированной точкой из-за особенностей округления `float`.
	 *
	 * @api
	 */
	public function getAmountFloat(): ?float
	{
		return $this->getCurrencyFloatField('Сумма');
	}

	/**
	 * Сумма с фиксированной точкой (копейки, центы, ...)
	 *
	 * @api
	 */
	public function getAmountFixed(): ?int
	{
		return $this->getCurrencyFixedField('Сумма');
	}

	/**
	 * Назначение платежа
	 *
	 * @api
	 */
	public function getPaymentPurpose(): ?string
	{
		return $this->getField('НазначениеПлатежа');
	}
}
