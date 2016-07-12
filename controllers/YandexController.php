<?php

namespace greeschenko\pay\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Html;

class YandexController extends Controller
{
    public $module;

    public function init()
    {
        $this->module = Yii::$app->getModule('pay');
        parent::init();
    }

    public function actionIndex($amount_due,$comment,$message,$label)
    {
        $ym = $this->module->ymmoney;

        $requestPayment = $ym->requestPayment([
            'pattern_id' => 'p2p',
            'to' => $ym->to,
            'amount_due' => $amount_due,
            'comment' => $comment,
            'message' => $message,
            'label' => $label,
            'test_payment' => $ym->istest,
        ]);

        $processPayment = $ym->processPayment([
            "request_id" => $request_payment->request_id,
            'ext_auth_success_uri' => $ym->ext_auth_success_uri,
            'ext_auth_fail_uri' => $ym->ext_auth_fail_uri,
            'test_payment' => $ym->istest,
        ]);

        return $processPayment;
    }

    public function actionTest()
    {
        echo Html::a('test yandex money pay',[
            '/pay/yandex',
            'amount_due' => '1000',
            'comment' => 'Test YandexMoney payment',
            'message' => 'Test YandexMoney payment',
            'label' => '11111',
        ]);
    }
}
