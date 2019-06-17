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

    public function onPostDispatchRegister(\Enlight_Controller_ActionEventArgs $args)
    {
        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $view = $controller->View();


        $view->addTemplateDir($this->pluginDirectory . '/Resources/Views');
        // array with template variable

        if($this->getApiInfo()['hasAccess'] == false){
            Shopware()->Container()->get('pluginlogger')->warning('You don\'t have acces to the Postcode.nl API');
            return;
        }

        $view->assign('Postcodenl', [
            'active' => true,
            'allowed' => $this->checkCountries(),
        ]);

    }

    public function onCollectJavascript(\Enlight_Event_EventArgs $args)
    {
        $jsFiles = [
            $this->pluginDirectory . '/Resources/Views/frontend/_public/src/js/jquery.postcodenl.js',
        ];
        return new ArrayCollection($jsFiles);
    }

    public function getApiInfo()
    {
        $apiKey = $this->config->getByNamespace('MemoPostcodenlPlugin', 'memoPostcodenlKey');
        $apiSecret = $this->config->getByNamespace('MemoPostcodenlPlugin', 'memoPostcodenlSecret');
        $url = "https://api.postcode.eu/account/v1/info";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        $return = json_decode($output, true);

        return($return);
    }

    public function checkCountries()

    {
        $return =[];
        foreach($this->getApiInfo()['countries'] as $key => $value){
            $queryBuilder = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();
            $queryBuilder->select('id', 'iso3')
                ->from('s_core_countries')
                ->where('iso3 = :country')
                ->setParameter(':country', $value);

            $arr[] = $queryBuilder->execute()->fetchAll(\PDO::FETCH_KEY_PAIR);
        }

        foreach($arr as $value) {
            $return += $value;
        }

        return($return);
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