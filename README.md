Yii2 Pay
========
yii2 payment aggregator includes a PayPal, Qiwi, YandexMoney, Visa and Mastercard

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist greeschenko/yii2-pay "*"
```

or add

```
"greeschenko/yii2-pay": "*"
```

to the require section of your `composer.json` file.


Usage
-----

add to you app config in module section

```
'modules'=>[
    ...
    'pay'=> [
        'class'=>'greeschenko\pay\Module',
        'components' => [
            'ppmoney'=>[
                'class'=>'greeschenko\pay\components\PPmoney',
                'client_id' => 'YOUR_PAYPAL_CLIENT_ID',
                'client_secret' => 'YOUR_PAYPAL_SECRET',
                'currency' => 'USD',
                'returnUrl' => '/example/action',
                'canselUrl' => '/example/action2',
                'config' => [
                    'mode'                      => 'sandbox', // development (sandbox) or production (live) mode
                    'http.ConnectionTimeOut'    => 30,
                    'http.Retry'                => 1,
                ],
            ],
        ],
    ],
    ...
],
```

generate a payments links

    PayPal

    ```
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
            'shipping' => 10,
            'shippingpers' => false,
            'tax' => 15,
            'taxpers' => true,
        ]);
    ```

check success payment in action

```
    public function actionSomeAction($paymentId)
    {
        ...
        $res = Yii::$app->getModule('pay')->ppmoney->getPaymentStatus($paymentId);
        ...
    }
```
