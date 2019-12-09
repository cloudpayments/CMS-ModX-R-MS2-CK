<?php
/**
 * Settings English Lexicon Entries for msCloudKassir
 *
 * @package    mscloudkassir
 * @subpackage lexicon
 */

$_lang['area_ms_cloudkassir_general']        = 'General';
$_lang['ms_cloudkassir_order_delivery_name'] = 'Delivery';

$_lang['setting_ms_cloudkassir_public_id']                  = 'Site ID';
$_lang['setting_ms_cloudkassir_public_id_desc']             = 'Required site identifier. Located in the CloudPayments.';
$_lang['setting_ms_cloudkassir_secret_key']                 = 'Secret key';
$_lang['setting_ms_cloudkassir_secret_key_desc']            = 'Required secret key. Located in the CloudPayments.';
$_lang['setting_ms_cloudkassir_inn']                        = 'Inn';
$_lang['setting_ms_cloudkassir_inn_desc']                   = 'Inn your organisation for generate online check.';
$_lang['setting_ms_cloudkassir_vat']                        = 'Vat';
$_lang['setting_ms_cloudkassir_vat_desc']                   = 'Available values: 20,10,0,110,120 or empty. More details in documentation CloudPayments <a target="_blank" href="https://cloudpayments.ru/Docs/Kassa#data-format">https://cloudpayments.ru/Docs/Kassa#data-format</a>';
$_lang['setting_ms_cloudkassir_vat_delivery']               = 'Vat for delivery';
$_lang['setting_ms_cloudkassir_vat_delivery_desc']          = 'Available values: 20,10,0,110,120 or empty. More details in documentation CloudPayments <a target="_blank" href="https://cloudpayments.ru/Docs/Kassa#data-format">https://cloudpayments.ru/Docs/Kassa#data-format</a>';
$_lang['setting_ms_cloudkassir_taxation_system']            = 'Taxation system';
$_lang['setting_ms_cloudkassir_taxation_system_desc']       = 'Available values: 0-5. More details in documentation CloudPayments <a target="_blank" href="https://cloudpayments.ru/Docs/Directory#taxation-system">https://cloudpayments.ru/Docs/Directory#taxation-system</a>';
$_lang['setting_ms_cloudkassir_status_for_pay_id']          = 'Pay order status';
$_lang['setting_ms_cloudkassir_status_for_pay_id_desc']     = 'Order status at which will be generate online check for receipt. You can specify multiple statuses delimited by comma.';
$_lang['setting_ms_cloudkassir_status_for_refunds_id']      = 'Refund order status';
$_lang['setting_ms_cloudkassir_status_for_refunds_id_desc'] = 'Order status at which will be generate online check for refund. You can specify multiple statuses delimited by comma.';
$_lang['setting_ms_cloudkassir_exclude_payment_ids']        = 'Ignored payment methods';
$_lang['setting_ms_cloudkassir_exclude_payment_ids_desc']   = 'List of payment method ids on which don\'t generate online check.';