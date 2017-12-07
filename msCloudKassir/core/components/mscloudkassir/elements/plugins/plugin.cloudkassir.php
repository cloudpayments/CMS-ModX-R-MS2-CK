<?php
/** @var msOrder $order */
if ($modx->event->name != 'msOnChangeOrderStatus') {
    return;
}

$path = $modx->getOption('mscloudkassir.core_path', null, $modx->getOption('core_path') . 'components/mscloudkassir/') . 'model/mscloudkassir/';
/** @var msCloudKassir $cpKassa */
$cpKassa = $modx->getService('mscloudkassir', 'msCloudKassir', $path);

if (!$cpKassa instanceof msCloudKassir) {
    $modx->log(xPDO::LOG_LEVEL_ERROR, '[msCloudKassir] could not load payment class "msCloudKassir".');
    return;
}

//Don't generate check on excluded payments
if (in_array($order->get('payment'), $cpKassa->config['exclude_payment_ids'])) {
    return;
}

if (in_array($order->get('status'), $cpKassa->config['status_for_pay_id'])) {
    $cpKassa->orderReceipt($order, msCloudKassir::RECEIPT_TYPE_INCOME);
} elseif (in_array($order->get('status'), $cpKassa->config['status_for_refund_id'])) {
    $cpKassa->orderReceipt($order, msCloudKassir::RECEIPT_TYPE_INCOME_RETURN);
}