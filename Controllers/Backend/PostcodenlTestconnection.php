<?php

use PostcodeNl\Api\Client;

Class Shopware_Controllers_Backend_PostcodenlTestconnection extends \Shopware_Controllers_Backend_ExtJs
{
    public function testAction()
    {
        $config = $this->get('config');

        $apiKey = $config->getByNamespace('memoPostcodenlPlugin' , 'memoPostcodenlKey');
        $apiSecret = $config->getByNamespace('memoPostcodenlPlugin', 'memoPostcodenlSecret');

        if(empty($apiKey) || empty($apiSecret) )
        {
            $this->View()
                ->assign('responseType', 'Error')
                ->assign('response', 'You have not filled in all required fields, please check your input and save the form before testing the connection.');
            return;
        }

        try {
            $client = new Client($apiKey, $apiSecret, "Shopware 5 plugin - Memo ICT");

            $result = $client->accountInfo();

            if($result['hasAccess'] == true) {
                $this->View()
                    ->assign('responseType', 'Success')
                    ->assign('response', 'Succes, Your connection works');
            } else {
                $this->View()
                    ->assign('responseType', 'Error')
                    ->assign('response', 'Something went wrong, check plugin log for complete error');
                $this->get('pluginlogger')->addError('There is an error communicating with the Postcode.nl API, please contact Postcode.nl servicedesk');
            }
        } catch (\PostcodeNl\Api\Exception\ClientException $e) {
            $this->get('pluginlogger')->addError($e->getMessage());
            $this->View()
                ->assign('responseType', 'Error')
                ->assign('response', $e->getMessage());
        }
    }
}
