<?php

namespace Maximal\ClientBankExchange1C;

enum ParserState: string
{
	/** Начальное состояние */
	case Init = 'init';

	// Состояния процесса разбора
	case FileBegin = 'file_begin';
	case FileEnd = 'file_end';
	case DocumentBegin = 'document_begin';
	case DocumentEnd = 'document_end';
	case SectionBegin = 'section_begin';
	case SectionEnd = 'section_end';

	/** Успешный разбор */
	case Success = 'success';

	// Ошибочные состояния
	/** Не найден заголовок файла */
	case NoHeader = 'no_header';
	/** Не найден маркер конца файла */
	case NoEndOfFile = 'no_end_of_file';
	/** Прочие ошибки */
	case GeneralFail = 'general_fail';
}
