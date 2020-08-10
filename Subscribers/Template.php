<?php

namespace memoPostcodenlPlugin\Subscribers;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\Theme\LessDefinition;
use Shopware_Components_Config as Config;
use Symfony\Component\HttpFoundation\Request;

class Template implements SubscriberInterface
{
    private $pluginDirectory;
    private $config;

    public function __construct($pluginDirectory, Config $config)
    {
        $this->pluginDirectory = $pluginDirectory;
        $this->config = $config;
    }

    public static function getSubscribedEvents()
    {
        return [
            /** Update custom fields before being saved */
//            'Shopware\Models\Customer\Address::prePersist' => 'onBeforeAddressPersist',
//            'Shopware\Models\Customer\Address::preUpdate' => 'onBeforeAddressPersist',

            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatchSecure',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'onCollectJavascript',
            'Theme_Compiler_Collect_Plugin_Less' => 'onCollectLess',
        ];
    }

    public function onBeforeAddressPersist(\Enlight_Event_EventArgs $args)
    {
//        $request = Request::createFromGlobals();
//        $data = ($request->get('address'));
//
//        /** @var \Shopware\Models\Customer\Address $entity */
//        $entity = $args->get('entity');
//
//        $attributes = $entity->getAttribute();
//        $attributes->setPostcodenlAutocompleteSupport($data['autocomplete-address']);
//        $attributes->setPostcodenlStreetname($data['dutch-address_street']);
////        $attributes->setPostcodenlHousenumber($data['dutch-address_housenumber']);
//        $attributes->setPostcodenlHousenumberAddition($data['dutch-address_housenumber-addition']);
////
////        $entity->setAttribute($attributes);
////        $args->set('entity', $entity);
//
//        dump($data);
//        dump($entity);
//        dump($attributes);
//
//        exit;
//        return $args;
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
