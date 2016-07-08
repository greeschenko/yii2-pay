<?php

namespace greeschenko\pay\components;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

class PPmoney extends Component
{
    const MODE_SANDBOX = 'sandbox';
    const MODE_LIVE    = 'live';

    const LOG_LEVEL_FINE  = 'FINE';
    const LOG_LEVEL_INFO  = 'INFO';
    const LOG_LEVEL_WARN  = 'WARN';
    const LOG_LEVEL_ERROR = 'ERROR';

    public $client_id;
    public $client_secret;
    public $currency;
    public $returnUrl;
    public $canselUrl;
    public $config = [];
    private $apiContext; // paypal's API context

    // override Yii's object init()
    public function init()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential($this->client_id, $this->client_secret)
        );

        $this->apiContext->setConfig(ArrayHelper::merge(
            [
                'mode'                      => self::MODE_SANDBOX, // development (sandbox) or production (live) mode
                'http.ConnectionTimeOut'    => 30,
                'http.Retry'                => 1,
                'log.LogEnabled'            => YII_DEBUG ? 1 : 0,
                'log.FileName'              => Yii::getAlias('@runtime/logs/paypal.log'),
                'log.LogLevel'              => self::LOG_LEVEL_FINE,
                'validation.level'          => 'log',
                /*'cache.enabled'             => 'false'*/
            ],$this->config)
        );

        // Set file name of the log if present
        if (isset($this->config['log.FileName'])
            && isset($this->config['log.LogEnabled'])
            && ((bool)$this->config['log.LogEnabled'] == true)
        ) {
            $logFileName = \Yii::getAlias($this->config['log.FileName']);
            if ($logFileName) {
                if (!file_exists($logFileName)) {
                    if (!touch($logFileName)) {
                        throw new ErrorException('Can\'t create paypal.log file at: ' . $logFileName);
                    }
                }
            }
            $this->config['log.FileName'] = $logFileName;
        }
    }

    public function getContext()
    {
        return $this->apiContext;
    }
}
