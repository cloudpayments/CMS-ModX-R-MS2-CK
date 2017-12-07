<?php
/**
 * Settings Russian Lexicon Entries for msCloudKassir
 *
 * @package    mscloudkassir
 * @subpackage lexicon
 */

$_lang['area_ms_cloudkassir_general']        = 'Основные настройки';
$_lang['ms_cloudkassir_order_delivery_name'] = 'Доставка';

$_lang['setting_ms_cloudkassir_public_id']                 = 'Идентификатор сайта';
$_lang['setting_ms_cloudkassir_public_id_desc']            = 'Обязательный идентификатор сайта. Находится в ЛК CloudPayments.';
$_lang['setting_ms_cloudkassir_secret_key']                = 'Секретный ключ';
$_lang['setting_ms_cloudkassir_secret_key_desc']           = 'Обязательный секретный ключ. Находится в ЛК CloudPayments (Пароль для API).';
$_lang['setting_ms_cloudkassir_inn']                       = 'ИНН';
$_lang['setting_ms_cloudkassir_inn_desc']                  = 'ИНН вашей организации или ИП, на который зарегистрирована касса. Используется при формировании онлайн-чека.';
$_lang['setting_ms_cloudkassir_vat']                       = 'Ставка НДС';
$_lang['setting_ms_cloudkassir_vat_desc']                  = 'Возможные значения: 18,10,0,110,118 или пустое значение. Более детальная информация в документации CloudPayments <a target="_blank" href="https://cloudpayments.ru/Docs/Kassa#data-format">https://cloudpayments.ru/Docs/Kassa#data-format</a>';
$_lang['setting_ms_cloudkassir_vat_delivery']              = 'Ставка НДС для доставки';
$_lang['setting_ms_cloudkassir_vat_delivery_desc']         = 'Возможные значения: 18,10,0,110,118 или пустое значение. Более детальная информация в документации CloudPayments <a target="_blank" href="https://cloudpayments.ru/Docs/Kassa#data-format">https://cloudpayments.ru/Docs/Kassa#data-format</a>';
$_lang['setting_ms_cloudkassir_taxation_system']           = 'Система налогооблажения';
$_lang['setting_ms_cloudkassir_taxation_system_desc']      = 'Возможные значения: 0-5. Более детальная информация в документации CloudPayments <a target="_blank" href="https://cloudpayments.ru/Docs/Directory#taxation-system">https://cloudpayments.ru/Docs/Directory#taxation-system</a>';
$_lang['setting_ms_cloudkassir_status_for_pay_id']         = 'Статус заказа для оплаты (приход)';
$_lang['setting_ms_cloudkassir_status_for_pay_id_desc']    = 'Статус заказа при котором будет сформирован онлайн-чек прихода. Можете указать несколько статусов через запятую.';
$_lang['setting_ms_cloudkassir_status_for_refund_id']      = 'Статус заказа для возврата (возврат прихода)';
$_lang['setting_ms_cloudkassir_status_for_refund_id_desc'] = 'Статус заказа при котором будет сформирован онлайн-чек возврата. Можете указать несколько статусов через запятую.';
$_lang['setting_ms_cloudkassir_exclude_payment_ids']       = 'Игнорирумые методы оплаты';
$_lang['setting_ms_cloudkassir_exclude_payment_ids_desc']  = 'Идентификаторы методов оплат, при которых онлайне-чек не будет формироваться';