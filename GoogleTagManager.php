<?php

namespace ezoterik\googleTagManager;

use Yii;
use yii\base\Application;
use yii\base\BaseObject;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

class GoogleTagManager extends BaseObject implements BootstrapInterface
{
    /** @var string|null */
    public $tagManagerId = null;

    /** @var array */
    protected $_dataLayerForCurrentRequest = [];

    /** @var string */
    public $sessionKey = 'google-tag-manager-data-layer';

    /** @var bool */
    public $isIgnorePostRequest = true;

    /** @var bool */
    public $isInitInHead = false;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app->getRequest()->getIsAjax() || (!$app->getRequest()->getIsGet() && !$app->getRequest()->getIsPost())) {
            return;
        }

        if ($this->isIgnorePostRequest && $app->getRequest()->getIsPost()) {
            return;
        }

        //Delay attaching event handler to the view component after it is fully configured
        $app->on(Application::EVENT_BEFORE_REQUEST, function () use ($app) {
            $app->getView()->on(View::EVENT_BEGIN_BODY, [$this, 'renderCode']);
        });
    }

    /**
     * @param string|null $key
     * @param string|object $value
     */
    public function dataLayerPushItemDelay($key, $value)
    {
        $session = Yii::$app->getSession();

        $dataLayerItems = $session->get($this->sessionKey, []);

        if ($key === null) {
            $dataLayerItem = $value;
        } else {
            $dataLayerItem = [$key => $value];
        }

        $dataLayerItems[] = $dataLayerItem;

        $session->set($this->sessionKey, $dataLayerItems);
    }

    /**
     * @param string|null $key
     * @param string|object $value
     */
    public function dataLayerPushItem($key, $value)
    {
        if ($key === null) {
            $dataLayerItem = $value;
        } else {
            $dataLayerItem = (object)[$key => $value];
        }

        $this->_dataLayerForCurrentRequest[] = $dataLayerItem;
    }

    /**
     * Rendering JavaScript code
     *
     * @param Event $event
     */
    public function renderCode(Event $event)
    {
        /* @var $view View */
        $view = $event->sender;

        $dataLayerItems = [];

        //If the session has data for dataLayer, then displays them and remove from the session
        $session = Yii::$app->getSession();
        if ($session->has($this->sessionKey)) {
            $dataLayerItems = $session->get($this->sessionKey, []);
            //Remove data from a session
            $session->remove($this->sessionKey);
        }

        $tagManagerId = $this->getTagManagerId();

        $dataLayerItems = array_merge($dataLayerItems, $this->_dataLayerForCurrentRequest);

        if ($tagManagerId === '') {
            return;
        }

        $scriptInit = "(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','" . Html::encode($tagManagerId) . "');";

        if ($dataLayerItems) {
            $scriptInit .= "\nwindow.dataLayer = window.dataLayer || [];";

            foreach ($dataLayerItems as $dataLayerItem) {
                $scriptInit .= "\n" . 'dataLayer.push(' . Json::encode($dataLayerItem) . ');';
            }
        }

        if ($this->isInitInHead) {
            $view->registerJs($scriptInit, View::POS_HEAD);
            $scriptInit = '';
        }

        echo $view->renderFile(__DIR__ . '/views/google-tag-manager.php', [
            'tagManagerId' => $tagManagerId,
            'dataLayerItems' => $dataLayerItems,
            'scriptInit' => $scriptInit,
        ]);
    }

    /**
     * Returns a code for triggering on a client side.
     * For example: "dataLayer.push(....);"
     *
     * @param array $variables
     *
     * @return string
     */
    public static function getClientDataLayerPush(array $variables)
    {
        if (count($variables) == 0) {
            return '';
        }

        return 'dataLayer.push(' . Json::encode($variables) . ');';
    }

    /**
     * @return string
     */
    private function getTagManagerId()
    {
        $tagManagerId = trim($this->tagManagerId);
        if ($tagManagerId === '') {
            return '';
        }

        if (stripos($tagManagerId, 'GTM-') === 0) {
            return $tagManagerId;
        }

        //Adding a GTM prefix
        return 'GTM-' . $tagManagerId;
    }
}
