<?php

namespace greeschenko\pay\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
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
    public function actionIndex()
    {
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $item1 = new Item();
        $item1->setName('Ground Coffee 40 oz')
            ->setCurrency($this->module->ppmoney->currency)
            ->setQuantity(1)
            ->setSku("123123") // Similar to `item_number` in Classic API
            ->setPrice(7.5);
        $item2 = new Item();
        $item2->setName('Granola bars')
            ->setCurrency($this->module->ppmoney->currency)
            ->setQuantity(5)
            ->setSku("321321") // Similar to `item_number` in Classic API
            ->setPrice(2);

        $itemList = new ItemList();
        $itemList->setItems(array($item1, $item2));

        $details = new Details();
        $details->setShipping(1.2)
            ->setTax(1.3)
            ->setSubtotal(17.50);

        $amount = new Amount();
        $amount->setCurrency($this->module->ppmoney->currency)
            ->setTotal(20)
            ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment description")
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
            echo '<pre>';
            var_dump('Success scenario');
            print_r(json_decode($payment,true));
        } catch (PayPalConnectionException $e) {
            echo '<pre>';
            var_dump('Failure scenario '.$e);
            echo $e;
        }
    }

    /**
     * return payment status for confirm
     *
     * @return string
     */
    public function actionStatus($id)
    {
        try {
            echo '<pre>';
        } catch (PayPalConnectionException $e) {
            echo '<pre>';
            var_dump('Failure scenario '.$e);
            echo $e;
        }

        return $payment;
    }
}
