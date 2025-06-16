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

	/**
	 * Плательщик (ИНН и наименование плательщика)
	 *
	 * @api
	 */
	public function getPayer(): ?string
	{
		return $this->getField('Плательщик');
	}

	/**
	 * ИНН плательщика (строка 12 символов)
	 *
	 * @api
	 */
	public function getPayerInn(): ?string
	{
		return $this->getField('ПлательщикИНН');
	}

	/**
	 * Наименование плательщика
	 *
	 * @api
	 */
	public function getPayerName(): ?string
	{
		return $this->getField('Плательщик1');
	}

	/**
	 * Расчетный счет плательщика (строка 20 символов)
	 *
	 * @api
	 */
	public function getPayerAccount(): ?string
	{
		return $this->getField('Плательщик2');
	}

	/**
	 * Расчетный счет плательщика в его банке, независимо от того, прямые расчеты у этого банка или нет
	 *
	 * @api
	 */
	public function getPayerAccountReal(): ?string
	{
		return $this->getField('ПлательщикСчет');
	}	

	/**
	 * Банк плательщика
	 *
	 * @api
	 */
	public function getPayerBank(): ?string
	{
		return $this->getField('ПлательщикБанк1');
	}

	/**
	 * БИК банка плательщика
	 *
	 * @api
	 */
	public function getPayerBic(): ?string
	{
		return $this->getField('ПлательщикБИК');
	}

	/**
	 * Кор счет банка плательщика (строка 20 символов)
	 *
	 * @api
	 */
	public function getPayerCorrAccount(): ?string
	{
		return $this->getField('ПлательщикКорсчет');
	}

	/**
	 * Получатель (ИНН и наименование плательщика)
	 *
	 * @api
	 */
	public function getRecipient(): ?string
	{
		return $this->getField('Получатель');
	}

	/**
	 * ИНН получателя (строка 12 символов)
	 *
	 * @api
	 */
	public function getRecipientInn(): ?string
	{
		return $this->getField('ПолучательИНН');
	}

	/**
	 * Наименование получателя
	 *
	 * @api
	 */
	public function getRecipientName(): ?string
	{
		return $this->getField('Получатель1');
	}

	/**
	 * Расчетный счет получтеля (строка 20 символов)
	 *
	 * @api
	 */
	public function getRecipientAccount(): ?string
	{
		return $this->getField('Получатель2');
	}

	/**
	 * Расчетный счет получтеля в его банке, независимо от того, прямые расчеты у этого банка или нет
	 *
	 * @api
	 */
	public function getPayerRecipientReal(): ?string
	{
		return $this->getField('ПолучательСчет');
	}

	/**
	 * Банк получтеля
	 *
	 * @api
	 */
	public function getRecipientBank(): ?string
	{
		return $this->getField('ПолучательБанк1');
	}

	/**
	 * БИК банка получтеля
	 *
	 * @api
	 */
	public function getRecipientBic(): ?string
	{
		return $this->getField('ПолучательБИК');
	}

	/**
	 * Кор счет банка получтеля (строка 20 символов)
	 *
	 * @api
	 */
	public function getRecipientCorrAccount(): ?string
	{
		return $this->getField('ПолучательКорсчет');
	}	
}
