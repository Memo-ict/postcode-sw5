<?php

namespace MemoPostcodeEuPlugin\Subscribers;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\Theme\LessDefinition;

class Template implements SubscriberInterface
{
    private $pluginDirectory;

    public function __construct($pluginDirectory)
    {
        $this->pluginDirectory = $pluginDirectory;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatchSecure',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'onCollectJavascript',
            'Theme_Compiler_Collect_Plugin_Less' => 'onCollectLess',
        ];
    }

    public function onPostDispatchSecure(\Enlight_Controller_ActionEventArgs $args)
    {
        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $view = $controller->View();

        $view->addTemplateDir($this->pluginDirectory . '/Resources/Views');
    }

    public function onCollectJavascript(\Enlight_Event_EventArgs $args)
    {
        $jsFiles = [
            $this->pluginDirectory . '/Resources/Views/frontend/_public/src/js/AutocompleteAddress.js',
            $this->pluginDirectory . '/Resources/Views/frontend/_public/src/js/jquery.postcode.eu.js',
        ];
        return new ArrayCollection($jsFiles);
    }

    public function onCollectLess(\Enlight_Event_EventArgs $args)
    {
        $less = new LessDefinition(
            [],
            [$this->pluginDirectory . '/Resources/Views/frontend/_public/src/less/all.less'],
            $this->pluginDirectory . '/Resources/Views/frontend/_public/src/less/'
        );

        return $less;
    }
}
