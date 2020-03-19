<?php

namespace memoPostcodenlPlugin;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
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
}
