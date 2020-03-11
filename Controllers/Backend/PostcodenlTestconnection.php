<?php

use PostcodeNl\Api\Client;

Class Shopware_Controllers_Backend_PostcodenlTestconnection extends \Shopware_Controllers_Backend_ExtJs
{
    //const EXTERNAL_API_BASE_URL = 'https://api.postcode.eu/account/v1/info';

    public function testAction()
    {
        $config = $this->get('config');

        $apiKey = $config->getByNamespace('memoPostcodenlPlugin' , 'memoPostcodenlKey');
        $apiSecret = $config->getByNamespace('memoPostcodenlPlugin', 'memoPostcodenlSecret');

        if(empty($apiKey) || empty($apiSecret) )
        {
            $this->View()->assign('Error', 'You have not filled in all required fields, please check your input.');
            return;
        }

        try {
            $client = new Client($apiKey, $apiSecret, "Shopware 5 plugin - Memo ICT");

            $result = $client->accountInfo();

            if($result['hasAccess'] == true) {
                $this->View()->assign('response', 'Succes, Your connection works');
            } else {
                $this->View()->assign('response', 'Something went wrong, check plugin log for complete error');
                $this->get('pluginlogger')->addError('There is a error communicating with the Postcode.nl API please contact Postcode.nl servicedesk');
            }
        } catch (\PostcodeNl\Api\Exception\ClientException $e) {
            $this->get('pluginlogger')->addError($e->getMessage());
            $this->View()->assign('response', $e->getMessage());
        }

//            $ch = curl_init();
//
//            curl_setopt($ch, CURLOPT_URL, self::EXTERNAL_API_BASE_URL);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");
//            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//
//            $output = curl_exec($ch);
//
//            curl_close($ch);

        //$return = json_decode($output, true);

//            if ($return['hasAccess'] != false) {
//                $this->View()->assign('response', 'Succes, Your connection works');
//            } else {
//                $this->View()->assign('response', 'Something went wrong, check plugin log for complete error');
//                $this->get('pluginlogger')->addError('There is a error communicating with the Postcode.nl API please contact Postcode.nl servicedesk');
//            }
    }
}
