<?php

namespace memoPostcodenlPlugin\Subscribers;

use Shopware\Components\CacheManager;
use Enlight\Event\SubscriberInterface;

class Backend implements SubscriberInterface
{

    private $pluginName;

    private $cacheManager;

    public function __construct($pluginName, CacheManager $cacheManager)
    {
        $this->pluginName = $pluginName;
        $this->cacheManager = $cacheManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Config' => 'onPostDispatchConfig'
        ];
    }

    public function onPostDispatchConfig(\Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Backend_Config $subject */
        $subject = $args->getSubject();
        $request = $subject->Request();

        if($request->isPost() && $request->getParam('name') === $this->pluginName)
        {
            $this->cacheManager->clearByTag(CacheManager::CACHE_TAG_CONFIG);
        }


    }



}