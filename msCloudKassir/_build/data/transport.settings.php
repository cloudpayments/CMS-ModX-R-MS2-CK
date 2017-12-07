<?php
/**
 * Loads system settings into build
 *
 * @package    mscloudkassir
 * @subpackage build
 */
$settings = array();

$tmp = array(
    'ms_cloudkassir_public_id'            => array(
        'value' => '',
        'xtype' => 'textfield',
    ),
    'ms_cloudkassir_secret_key'           => array(
        'value' => '',
        'xtype' => 'text-password',
    ),
    'ms_cloudkassir_inn'                  => array(
        'value' => '',
        'xtype' => 'textfield',
    ),
    'ms_cloudkassir_vat'                  => array(
        'value' => '',
        'xtype' => 'textfield',
    ),
    'ms_cloudkassir_vat_delivery'         => array(
        'value' => '',
        'xtype' => 'textfield',
    ),
    'ms_cloudkassir_taxation_system'      => array(
        'value' => '0',
        'xtype' => 'textfield',
    ),
    'ms_cloudkassir_status_for_pay_id'    => array(
        'value' => 2,
        'xtype' => 'textfield',
    ),
    'ms_cloudkassir_status_for_refund_id' => array(
        'value' => 4,
        'xtype' => 'textfield',
    ),
    'ms_cloudkassir_exclude_payment_ids'          => array(
        'value' => '',
        'xtype' => 'textfield',
    ),
);

foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key'       => $k
            ,
            'namespace' => PKG_NAME_LOWER
            ,
            'area'      => 'ms_cloudkassir_general'
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}

return $settings;