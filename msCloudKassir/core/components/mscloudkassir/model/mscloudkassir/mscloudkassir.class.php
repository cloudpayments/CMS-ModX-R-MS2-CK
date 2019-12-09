<?php

class msCloudKassir
{
    const RECEIPT_TYPE_INCOME = 'Income';
    const RECEIPT_TYPE_INCOME_RETURN = 'IncomeReturn';

    /** @var modX */
    protected $modx;

    /** @var  resource */
    private $curl;

    /** @var pdoFetch */
    private $pdoTools;

    /*
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $configPrefix = 'ms_cloudkassir_';
        $corePath     = $this->modx->getOption('mscloudkassir.core_path', $config,
            MODX_CORE_PATH . 'components/mscloudkassir/');

        $this->config = array_merge(array(
            'public_id'            => $this->modx->getOption($configPrefix . 'public_id'),
            'secret_key'           => $this->modx->getOption($configPrefix . 'secret_key'),
            'inn'                  => $this->modx->getOption($configPrefix . 'inn', null, ''),
            'vat'                  => $this->modx->getOption($configPrefix . 'vat', null, ''),
            'vat_delivery'         => $this->modx->getOption($configPrefix . 'vat_delivery', null, ''),
            'taxation_system'      => $this->modx->getOption($configPrefix . 'taxation_system', null, ''),
            'status_for_pay_id'    => $this->modx->getOption($configPrefix . 'status_for_pay_id', null, 2),
            'status_for_refund_id' => $this->modx->getOption($configPrefix . 'status_for_refund_id', null, 4),
            'exclude_payment_ids'  => $this->modx->getOption($configPrefix . 'exclude_payment_ids', null, 1),
            'modelPath'            => $corePath . 'model/',
        ), $config);

        $this->config['public_id']  = trim($this->config['public_id']);
        $this->config['secret_key'] = trim($this->config['secret_key']);
        if (!is_array($this->config['status_for_pay_id'])) {
            $this->config['status_for_pay_id'] = explode(',', $this->config['status_for_pay_id']);
            $this->config['status_for_pay_id'] = array_map('trim', $this->config['status_for_pay_id']);
        }
        if (!is_array($this->config['status_for_refund_id'])) {
            $this->config['status_for_refund_id'] = explode(',', $this->config['status_for_refund_id']);
            $this->config['status_for_refund_id'] = array_map('trim', $this->config['status_for_refund_id']);
        }
        if (!is_array($this->config['exclude_payment_ids'])) {
            $this->config['exclude_payment_ids'] = explode(',', $this->config['exclude_payment_ids']);
            $this->config['exclude_payment_ids'] = array_map('trim', $this->config['exclude_payment_ids']);
        }

        $this->modx->addPackage('mscloudkassir', $this->config['modelPath']);
        $this->modx->lexicon->load('mscloudkassir:default');

        $this->pdoTools = $this->modx->getService('pdoFetch');
    }

    /**
     *
     */
    function __destruct()
    {
        if ($this->curl) {
            curl_close($this->curl);
        }
    }

    /**
     * @param msOrder $order
     * @param string  $type
     * @return bool
     */
    public function orderReceipt(msOrder $order, $type)
    {
        if ($this->getOrderProperty($order, 'receipt_' . $type, false)) {
            return true;
        }

        if ($type == self::RECEIPT_TYPE_INCOME_RETURN &&
            !$this->getOrderProperty($order, 'receipt_' . self::RECEIPT_TYPE_INCOME, false)
        ) {
            //If don't generate Income receipt don't generate return receipt
            return true;
        }
        $response = $this->makeRequest('kkt/receipt', array(
            'Inn'             => $this->config['inn'],
            'Type'            => $type,
            'CustomerReceipt' => $this->getOrderReceiptData($order),
            'InvoiceId'       => $order->get('num'),
            'AccountId'       => $order->get('user_id'),
        ));
        if ($response !== false) {
            $this->setOrderProperty($order, 'receipt_' . $type, true);
        }

        return $response;
    }

    /**
     * @param msOrder $order
     * @return mixed
     */
    private function getOrderReceiptData(msOrder $order)
    {
        $profile     = $order->getOne('UserProfile');
        $address     = $order->getOne('Address');
        $receiptData = array(
            'Items'           => array(),
            'taxationSystem'  => $this->config['taxation_system'],
            'calculationPlace'=>'www.'.$_SERVER['SERVER_NAME'],
            'email'           => $profile->get('email'),
            'phone'           => $address->get('phone'),
        );
        $products    = $this->pdoTools->getCollection('msOrderProduct',
            json_encode(array('order_id' => $order->get('id'))), array(
                'leftJoin' => array(
                    'Product' => array(
                        'class' => 'msProduct',
                        'on'    => 'msOrderProduct.product_id = Product.id',
                    ),
                ),
                'select'   => array(
                    'msOrderProduct' => $this->modx->getSelectColumns('msOrderProduct', 'msOrderProduct', '',
                        array('id'),
                        true),
                    'msProduct'      => $this->modx->getSelectColumns('msProduct', 'Product', '', array('content'),
                        true),
                )
            ));
        foreach ($products as $row) {
            $title = !empty($row['name']) ? $row['name'] : $row['pagetitle'];
            $item  = array(
                'label'    => $title,
                'price'    => $row['price'],
                'quantity' => $row['count'],
                'amount'   => $row['cost'],
            );
            if (!empty($this->config['vat'])) {
                $item['vat'] = $this->config['vat'];
            }
            $receiptData['Items'][] = $item;
        }

        if ($order->get('delivery_cost') > 0) {
            $item = array(
                'label'    => $this->modx->lexicon('ms_cloudkassir_order_delivery_name'),
                'price'    => $order->get('delivery_cost'),
                'quantity' => 1,
                'amount'   => $order->get('delivery_cost')
            );

            if (!empty($this->config['vat_delivery'])) {
                $item['vat'] = $this->config['vat_delivery'];
            }
            $receiptData['Items'][] = $item;
        }

        return $receiptData;
    }

    /**
     * @param msOrder    $order
     * @param string     $name
     * @param mixed|null $default
     * @return mixed|null
     */
    private function getOrderProperty(msOrder $order, $name, $default = null)
    {
        $props = $order->get('properties');

        return isset($props['cloudkassir'][$name]) ? $props['cloudkassir'][$name] : $default;
    }

    /**
     * @param msOrder      $order
     * @param string|array $name
     * @param mixed|null   $value
     */
    private function setOrderProperty(msOrder $order, $name, $value = null)
    {
        $newProperties = array();
        if (is_array($name)) {
            $newProperties = $name;
        } else {
            $newProperties[$name] = $value;
        }

        $orderProperties = $order->get('properties');
        if (!is_array($orderProperties)) {
            $orderProperties = array();
        }
        if (!isset($orderProperties['cloudkassir'])) {
            $orderProperties['cloudkassir'] = array();
        }
        $orderProperties['cloudkassir'] = array_merge($orderProperties['cloudkassir'], $newProperties);
        $order->set('properties', $orderProperties);
        $order->save();
    }

    /**
     * @param string $location
     * @param array  $request
     * @return bool|array
     */
    private function makeRequest($location, $request = array())
    {
        if (!$this->curl) {
            $this->curl = curl_init();
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($this->curl, CURLOPT_USERPWD, $this->config['public_id'] . ':' . $this->config['secret_key']);
        }

        curl_setopt($this->curl, CURLOPT_URL, 'https://api.cloudpayments.ru/' . $location);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
            "content-type: application/json"
        ));
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($request));

        $response = curl_exec($this->curl);
        if ($response === false || curl_getinfo($this->curl, CURLINFO_HTTP_CODE) != 200) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR,
                '[miniShop2:CloudKassir] Failed API request.' .
                ' Location: ' . $location .
                ' Request: ' . print_r($request, true) .
                ' HTTP Code: ' . curl_getinfo($this->curl, CURLINFO_HTTP_CODE) .
                ' Error: ' . curl_error($this->curl)
            );

            return false;
        }
        $response = json_decode($response, true);
        if (!isset($response['Success']) || !$response['Success']) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR,
                '[miniShop2:CloudKassir] Failed API request.' .
                ' Location: ' . $location .
                ' Request: ' . print_r($request, true) .
                ' Response: ' . print_r($response, true)
            );

            return false;
        }

        return $response;
    }
}