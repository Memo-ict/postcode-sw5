<?php

use PostcodeNl\Api\Client;
use PostcodeNl\Api\Exception\ClientException;

class Shopware_Controllers_Frontend_PostcodenlApi extends Enlight_Controller_Action
{
    private $client;

    public function indexAction() {
        if(!$this->validateClient()) {
            return false;
        }


//
//        $country = $this->Request()->getParam('country');
//        $zipcode = $this->Request()->getParam('zipcode');
//        $number = $this->Request()->getParam('number');
//        $addition = $this->Request()->getParam('addition');
//
//        if(!empty($zipcode) && !empty($number)) {
//            if (preg_match($regexArray[$country], $zipcode)) {
//                $listOfCities = $this->getAddressData(trim($zipcode), $number, $addition);
//                    $results['addressData'] = $listOfCities;
//            } else {
//                $results = null;
//            }
//        }
//
//        $this->Response()->setHeader('Content-type', 'application/json', true);
//        $this->Response()->setBody( json_encode( $results));

        //$this->jsonResponse($results);
    }

    public function countrycheckAction() {
        if(!$this->validateClient()) {
            return false;
        }

        $id = $this->Request()->getParam('country') ?: null;

        if(empty($id)) {
            return false;
        }

        $queryBuilder = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();
        $queryBuilder->select('c.iso3')
            ->from('s_core_countries', 'c')
            ->where('id = :id')
            ->setParameter(':id', $id);

        $iso3 = $queryBuilder->execute()->fetch()['iso3'];


        $supportedCountries = $this->client->internationalGetSupportedCountries();

        $this->jsonResponse([
            'key' => $id,
            'iso3' => $iso3,
            'isSupported' => in_array($iso3, array_column($supportedCountries, "iso3"))
        ]);
    }

    public function autocompleteAction() {
        if(!$this->validateClient()) {
            return false;
        }

        $params = $this->fixParams($this->Request()->getParams());

        $iso3Context = $params[0];
        $term = $params[1];
        $session = $this->Request()->getHeader("X-Autocomplete-Session");

        return $this->jsonResponse($this->client->internationalAutocomplete($iso3Context, $term, $session));

    }

    public function addressDetailsAction() {
        if(!$this->validateClient()) {
            return false;
        }

        $params = $this->fixParams($this->Request()->getParams());

        $context = $params[0];
        $session = $this->Request()->getHeader("X-Autocomplete-Session");

        return $this->jsonResponse($this->client->internationalGetDetails($context, $session));
    }

    private function jsonResponse($response) {
        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setBody( json_encode( $response));
    }

    private function fixParams($paramBag) {
        unset($paramBag['module'], $paramBag['controller'], $paramBag['action']);

        $return = [];
        foreach($paramBag as $k => $v) {
            if(!is_numeric($k) && strlen($k) > 0) {
                $return[] = $k;
            }
            if(strlen($v) > 0) {
                $return[] = $v;
            }
        }

        return $return;
    }

    private function validateClient()
    {
        $this->get('front')->Plugins()->ViewRenderer()->setNoRender();

        $config = $this->get('config');

        $apiKey = $config->getByNamespace('MemoPostcodenlPlugin', 'memoPostcodenlKey');
        $apiSecret = $config->getByNamespace('MemoPostcodenlPlugin', 'memoPostcodenlSecret');

        if(empty($apiKey) || empty($apiSecret) )
        {
            $this->get('pluginlogger')->warning('You have not filled in all required fields, please check your input.');
            return false;
        }

        try {
            $this->client = new Client($apiKey, $apiSecret, "Shopware 5 plugin - Memo ICT");

            $result = $this->client->accountInfo();

            if($result['hasAccess'] == false) {
                $this->get('pluginlogger')->warning('You don\'t have access to the Postcode.nl API');
                return false;
            }
        } catch (\PostcodeNl\Api\Exception\ClientException $e) {
            $this->get('pluginlogger')->addError($e->getMessage());
            return false;
        }
        return true;
    }
//
//    public function getAddressData($zipcode, $number, $addition){
//
//        $config = $this->get('config');
//
//        $apiKey = $config->getByNamespace('MemoPostcodenlPlugin', 'memoPostcodenlKey');
//        $apiSecret = $config->getByNamespace('MemoPostcodenlPlugin', 'memoPostcodenlSecret');
//        $url = 'https://api.postcode.eu/nl/v1/addresses/postcode/'.$zipcode.'/'.$number.'/'.$addition;
//
//        $ch = curl_init();
//
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");
//        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//        curl_setopt($ch, CURLOPT_USERAGENT, 'MemoPostcodenlPlugin Shopware 5');
//
//        $output = curl_exec($ch);
//
//        curl_close($ch);
//
//        return(json_decode($output));
//
//    }
//
//    public function getApiInfo()
//    {
//        $config = $this->get('config');
//
//        $apiKey = $config->getByNamespace('MemoPostcodenlPlugin', 'memoPostcodenlKey');
//        $apiSecret = $config->getByNamespace('MemoPostcodenlPlugin', 'memoPostcodenlSecret');
//        $url = "https://api.postcode.eu/account/v1/info";
//
//        $ch = curl_init();
//
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");
//        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//        $output = curl_exec($ch);
//        curl_close ($ch);
//
//        $return = json_decode($output, true);
//
//        return($return);
//    }
//
//    public function getRegex()
//    {
//        $return =[];
//        foreach($this->getApiInfo()['countries'] as $key => $value){
//            $queryBuilder = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();
//            $queryBuilder->select('c.id','a.zipcoderegex')
//                ->from('s_core_countries', 'c')
//                ->innerJoin('c' , 's_core_countries_attributes', 'a', 'c.id = a.countryID')
//                ->where('iso3 = :country')
//                ->setParameter(':country', $value);
//
//            $arr[] = $queryBuilder->execute()->fetchAll(\PDO::FETCH_KEY_PAIR);
//        }
//
//        foreach($arr as $value) {
//            $return += $value;
//        }
//
//        return($return);
//    }
}
