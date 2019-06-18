<?php

namespace MemoPostcodenlPlugin;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;

class MemoPostcodenlPlugin extends Plugin
{
    public function install(InstallContext $context)
    {
        $this->createAttributes();
        $this->createRegex('NL', '/^[1-9][0-9]{3}[\s]?[A-Za-z]{2}$/i');

        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);

    }

    public function uninstall(UninstallContext $context)
    {
        if($context->keepUserData()) {
            return;
        }

        $this->removeAttributes();
        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
    }


    public function createAttributes()
    {
        $service = $this->container->get('shopware_attribute.crud_service');

        $service->update(
            's_core_countries_attributes',
            'zipcodeRegex',
            'string',[
                'label' => 'Regex Pattern',
                'displayInBackend' => false,
            ], null, true
        );

        Shopware()->Models()->generateAttributeModels(['s_core_countries_attributes']);

    }

    public function removeAttributes()
    {
        $service = $this->container->get('shopware_attribute.crud_service');

        $service->delete('s_core_countries_attributes', 'zipcodeRegex');

        Shopware()->Models()->generateAttributeModels(['s_core_countries_attributes']);
    }

    public function createRegex($iso, $regex)
    {
        $queryBuilder = $this->container->get('dbal_connection')->createQueryBuilder();

        $queryBuilder->select(['id'])
            ->from('s_core_countries')
            ->where('countryiso = :iso')
            ->setParameter(':iso' , $iso);

        $countryId = $queryBuilder->execute()->fetch();


        $queryBuilder
            ->insert('s_core_countries_attributes')
            ->values([
                'id' => 'null',
                'countryID' => $countryId['id'],
                'zipcoderegex' => "'".$regex."'"
            ]);
        $queryBuilder->execute();
    }

}