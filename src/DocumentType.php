<?php

namespace Maximal\ClientBankExchange1C;

/**
 * Тип секции документа
 */
enum DocumentType: string
{
	/** Банковский ордер */
	case BankOrder = 'bank_order';

	/** Платёжное поручение */
	case PaymentOrder = 'payment_order';

	/** Инкассовое поручение */
	case CollectionOrder = 'collection_order';

	/** Платёжное требование */
	case PaymentClaim = 'payment_claim';

	/** Прочее */
	case Other = 'other';

	public static function fromName(string $name): self
	{
		return match (mb_strtolower($name)) {
			'банковский ордер', 'bank_order' => self::BankOrder,
			'платёжное поручение', 'платежное поручение', 'payment_order' => self::PaymentOrder,
			'инкассовое поручение', 'collection_order' => self::CollectionOrder,
			'платежное требование', 'платёжное требование', 'payment_claim' => self::PaymentClaim,
			default => self::Other,
		};
	}
}
