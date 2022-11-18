<?php

namespace ezoterik\googleTagManager;

use Yii;
use yii\base\Application;
use yii\base\BaseObject;
use yii\base\BootstrapInterface;
use yii\base\Event;
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
            $dataLayerItem = [$key => $value];
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

        $dataLayerItems = array_merge($dataLayerItems, $this->_dataLayerForCurrentRequest);

        echo $view->renderFile(__DIR__ . '/views/google-tag-manager.php', [
            'tagManagerId' => $this->tagManagerId,
            'dataLayerItems' => $dataLayerItems,
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
}
