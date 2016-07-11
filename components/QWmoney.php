<?php

namespace greeschenko\pay\components;

use FintechFab\QiwiSdk\Curl;
use FintechFab\QiwiSdk\Gateway;

use yii\base\Component;

class QWmoney extends Component
{
    public $config = [
        'gateUrl'  => 'url-to-qiwi-gate',
        'provider' => [
            'id'       => 'your-qiwi-gate-id',          //логин в системе QIWI
            'password' => 'your-qiwi-gate-password',    //пароль в системе QIWI
            'key'      => 'your-qiwi-gate-key',         //ключ для подписи в QIWI
            ],
    ];

    protected $curl;
    protected $gate;

    public function init()
    {
        Gateway::setConfig($this->config);
        $this->curl = new Curl();
        parent::init();
    }

    /**
     * create new qiwi bill
     *
     * @return void
     */
    public function create($itemid,$userphone,$sum,$comment,$lifetime=60*60*24)
    {
        $gate = new Gateway($this->curl);
        $bill = $gate->createBill(
            $itemid,       // номер заказа (счета) в вашей системе
            $userphone, // номер кошелька киви (моб. тел. плательщика)
            $sum,     // сумма счета
            $comment,  // комментрий к счету
            $lifetime   // на сутки
        );

        if (!$bill) {
            return $gate->getError();
        }

        return $bill;
    }

    /**
     * cansel bill
     *
     * @return void
     */
    public function cansel($id)
    {
        $gate = new Gateway($this->curl);
        $billCanceled = $gate->cancelBill($id);

        return $billCreated;
    }

    /**
     * check payment status
     *
     * @return void
     */
    public function check($id)
    {
        $gate = new Gateway($this->curl);
        $statusChecked = $gate->doRequestBillStatus($id);
        if ($statusChecked) {
            $status = $gate->getValueBillStatus();
            switch ($status) {
                case 'payable': // ожидает оплаты
                case 'paid': // оплачен
                case 'canceled': // отменен
                case 'expired': // отменен, просрочен
            }

            return $status;
        }

        return false;
    }
}
