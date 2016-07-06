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

add to you app config in components section

```
'components' => [
    ...
    'ppmoney'=>[
        'class'=>'greeschenko\pay\components\PPmoney',
        'client_id' => 'YOUR-CLIENT-ID-FROM-PAYPAL',
        'client_secret' => 'YOUR-CLIENT-SECRET-FROM-PAYPAL',
    ],
    ...
]
```


