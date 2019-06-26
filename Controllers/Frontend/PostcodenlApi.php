<?php


class Shopware_Controllers_Frontend_PostcodenlApi extends Enlight_Controller_Action
{


    public function indexAction(){

        $this->get('front')->Plugins()->ViewRenderer()->setNoRender();

        $country = $this->request()->getParam('country');
        $zipcode = $this->Request()->getParam('zipcode');
        $number = $this->Request()->getParam('number');
        $addition = $this->Request()->getParam('addition');


        if($this->checkZipcode($country, $zipcode) !== false) {

            $listOfCities = $this->getAddressData(trim($zipcode), $number, $addition);

            $results = [];
            $results['addressData'] = $listOfCities;
        }
        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setBody( json_encode( $results));


    }

    public function checkZipcode($country, $zipcode)
    {
        $queryBuilder = $this->container->get('dbal_connection')->CreateQueryBuilder();

        $queryBuilder->select('zipcoderegex')
            ->from('s_core_countries_attributes')
            ->where('countryID =:countryID')
            ->setParameter(':countryID', $country);


        $regex = $queryBuilder->execute()->fetch();

        return(preg_match($regex['zipcoderegex'], $zipcode));
    }

    public function getAddressData($zipcode, $number, $addition){

        $config = $this->get('config');

        $apiKey = $config->getByNamespace('MemoPostcodenlPlugin', 'memoPostcodenlKey');
        $apiSecret = $config->getByNamespace('MemoPostcodenlPlugin', 'memoPostcodenlSecret');
        $url = 'https://api.postcode.eu/nl/v1/addresses/postcode/'.$zipcode.'/'.$number.'/'.$addition;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MemoPostcodenlPlugin Shopware 5');

        $output = curl_exec($ch);

        curl_close($ch);

        return(json_decode($output));

    }
}