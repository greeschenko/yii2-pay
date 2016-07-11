<?php

namespace greeschenko\pay\components;

use \YandexMoney\API;
use yii\base\Component;
use yii\base\InvalidConfigException;

class YMComponent extends Component
{
    /** @var  int */
    public $client_id;

    /** @var  string */
    public $code;

    /** @var  string */
    public $redirect_uri;

    /** @var null|string */
    public $client_secret = null;

    /** @var  \YandexMoney\API */
    private $api = null;

    public function init()
    {
        if (!$this->client_id) {
            throw new InvalidConfigException("Client_id can't be empty!");
        }

        if (!$this->code) {
            throw new InvalidConfigException("Code can't be empty!");
        }

        if (!$this->redirect_uri) {
            throw new InvalidConfigException("Redirect_uri can't be empty!");
        }

        $access_token = API::getAccessToken($this->client_id,$this->code,$this->redirect_uri, $this->client_secret);
        $this->api = new API($access_token);
    }

    /**
     * @return \YandexMoney\API
     */
    public function getYm()
    {
        return $this->api;
    }
}
