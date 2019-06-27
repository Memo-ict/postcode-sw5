<?php
namespace MemoPostcodenlPlugin\Subscribers;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\Theme\LessDefinition;
use Shopware_Components_Config as Config;
use Doctrine\Common\Collections\ArrayCollection;


class Template implements SubscriberInterface{
    
    private $pluginDirectory;
    private $config;

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Register' => 'onPostDispatchRegister',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatchSecure',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'onCollectJavascript',
            'Theme_Compiler_Collect_Plugin_Less' => 'onCollectLess',
        ];

    }

    public function __construct($pluginName, $pluginDirectory, Config $config)
    {
        $this->pluginDirectory = $pluginDirectory;
        $this->config = $config;
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
            $this->pluginDirectory . '/Resources/Views/frontend/_public/src/js/jquery.postcodenl.js',
        ];
        return new ArrayCollection($jsFiles);
    }

    public function onCollectLess(\Enlight_Event_EventArgs $args)
    {
        $pluginPath = Shopware()->Container()->getParameter('memo_postcodenl_plugin.plugin_dir');

        $less = new LessDefinition(
            [],
            [$pluginPath . '/Resources/Views/frontend/_public/src/less/all.less'],
            $pluginPath . '/Resources/Views/frontend/_public/src/less/'
        );

        return $less;

    }
}