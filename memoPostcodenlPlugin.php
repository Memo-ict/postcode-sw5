<?php

namespace memoPostcodenlPlugin;

use MemoBeckhuisTemplate\Components\EmotionElements;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class memoPostcodenlPlugin extends Plugin
{
    public function activate(ActivateContext $context)
    {
        parent::activate($context); // TODO: Change the autogenerated stub
        $context->scheduleClearCache(ActivateContext::CACHE_LIST_ALL);
    }

    public function deactivate(DeactivateContext $deactivateContext)
    {
        parent::deactivate($deactivateContext); // TODO: Change the autogenerated stub
        $deactivateContext->scheduleClearCache(DeactivateContext::CACHE_LIST_ALL);
    }

    public function install(InstallContext $context)
    {
        $this->createAddressAttributes();
    }

    public function uninstall(UninstallContext $context)
    {
        if ($context->keepUserData()) {
            return;
        }

        $this->removeAddressAttributes();
    }

    private function createAddressAttributes()
    {
        $service = $this->container->get('shopware_attribute.crud_service');

        $service->update(
            's_user_addresses_attributes',
            'postcodenl_streetname',
            TypeMapping::TYPE_STRING,
            [
                'position' => 1,
                'displayInBackend' => true,
                'label' => 'Postcode.nl Street'
            ]
        );
        $service->update(
            's_user_addresses_attributes',
            'postcodenl_housenumber',
            TypeMapping::TYPE_STRING,
            [
                'position' => 2,
                'displayInBackend' => true,
                'label' => 'Postcode.nl Housenumber'
            ]
        );
        $service->update(
            's_user_addresses_attributes',
            'postcodenl_housenumber_addition',
            TypeMapping::TYPE_STRING,
            [
                'position' => 3,
                'displayInBackend' => true,
                'label' => 'Postcode.nl Housenumber Addition'
            ]
        );
        $service->update(
            's_user_addresses_attributes',
            'postcodenl_zipcode',
            TypeMapping::TYPE_STRING,
            [
                'position' => 4,
                'displayInBackend' => true,
                'label' => 'Postcode.nl Zipcode'
            ]
        );
        $service->update(
            's_user_addresses_attributes',
            'postcodenl_city',
            TypeMapping::TYPE_STRING,
            [
                'position' => 5,
                'displayInBackend' => true,
                'label' => 'Postcode.nl City'
            ]
        );
        $service->update(
            's_user_addresses_attributes',
            'postcodenl_autocomplete_address',
            TypeMapping::TYPE_TEXT,
            [
                'position' => 10,
                'displayInBackend' => true,
                'label' => 'Postcode.nl Autocomplete data'
            ]
        );

        Shopware()->Models()->generateAttributeModels(['s_user_addresses_attributes']);
    }

    private function removeAddressAttributes()
    {
        $service = $this->container->get('shopware_attribute.crud_service');

        $service->delete('s_user_addresses_attributes' ,'postcodenl_streetname');
        $service->delete('s_user_addresses_attributes', 'postcodenl_housenumber');
        $service->delete('s_user_addresses_attributes', 'postcodenl_housenumber_addition');
        $service->delete('s_user_addresses_attributes', 'postcodenl_zipcode');
        $service->delete('s_user_addresses_attributes', 'postcodenl_city');
        $service->delete('s_user_addresses_attributes', 'postcodenl_autocomplete_address');

        Shopware()->Models()->generateAttributeModels(['s_user_addresses_attributes']);
    }
}
