<?php

namespace greeschenko\pay\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use yii\helpers\Html;
use PayPal\Api\Payer;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\Payment;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\RedirectUrls;
use PayPal\Exception\PaypalConnectionException;

class PaypalController extends Controller
{
    public $module;

    public function init()
    {
        $this->module = Yii::$app->getModule('pay');
        parent::init();
    }
    /**
     * paypal payment form
     *
     * @return redirect
     */
    public function actionIndex($descr,$items,$shipping=0,$shippingpers=false,$tax=0,$taxpers=false)
    {
        $sum=0;
        $ressum = 0;
        $itemslist = [];
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        foreach (json_decode($items,true) as $one) {
            $item = new Item();
            $item->setName($one['name'])
                ->setCurrency($this->module->ppmoney->currency)
                ->setQuantity($one['quantity'])
                ->setSku($one['sku']) // Similar to `item_number` in Classic API
                ->setPrice($one['price']);

            $itemslist[] = $item;

            $sum = $sum + ($one['price'] * $one['quantity']);
        }

        if ($shippingpers) {
            $shipping = ($shipping * $sum) / 100;
        }

        if ($taxpers) {
            $tax = ($tax * $sum) / 100;
        }

        $ressum = $sum + $shipping + $tax;

        $itemList = new ItemList();
        $itemList->setItems($itemslist);

        $details = new Details();
        if ($shipping > 0) {
            $details->setShipping($shipping);
        }
        if ($tax > 0) {
            $details->setTax($tax);
        }
        $details->setSubtotal($sum);

        $amount = new Amount();
        $amount->setCurrency($this->module->ppmoney->currency)
            ->setTotal($ressum)
            ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription($descr)
            ->setInvoiceNumber(uniqid());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(Url::to($this->module->ppmoney->returnUrl,true))
            ->setCancelUrl(Url::to($this->module->ppmoney->canselUrl,true));

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));

        try {
            $payment->create($this->module->ppmoney->getContext());
            $res = json_decode($payment,true);
            foreach ($res['links'] as $one) {
                if ($one['method'] == 'REDIRECT') {
                    echo $one['href'];
                    echo '</br>';
                    header("Location: {$one['href']}");
                    exit;
                }
            }
        } catch (PayPalConnectionException $e) {
            echo '<pre>';
            var_dump('Failure scenario '.$e);
            echo $e;
        }
    }

    public function actionTest()
    {
        echo Html::a('test pay',[
            '/pay/paypal',
            'descr' => 'Test payment',
            'items' => json_encode([
                [
                    'name' => 'Test item 1',
                    'quantity' => 1,
                    'sku' => '23456',
                    'price' => 34,
                ],
                [
                    'name' => 'Test item 2',
                    'quantity' => 3,
                    'sku' => '23456',
                    'price' => 10,
                ],
            ]),
            'tax' => 15,
            'taxpers' => true,
        ]);
    }
}
