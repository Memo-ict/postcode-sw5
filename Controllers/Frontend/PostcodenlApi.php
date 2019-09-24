<?php


class Shopware_Controllers_Frontend_PostcodenlApi extends Enlight_Controller_Action
{

    public function indexAction(){

        $this->get('front')->Plugins()->ViewRenderer()->setNoRender();

        $regexArray = $this->getRegex();
        $hasAccess = $this->getApiInfo()['hasAccess'];
        $results = [];

        if($hasAccess == false){
            $this->get('pluginlogger')->warning('You don\'t have access to the Postcode.nl API');
            return;
        }

        $country = $this->Request()->getParam('country');
        $zipcode = $this->Request()->getParam('zipcode');
        $number = $this->Request()->getParam('number');
        $addition = $this->Request()->getParam('addition');

        if(!empty($zipcode) && !empty($number)) {
            if (preg_match($regexArray[$country], $zipcode)) {
                $listOfCities = $this->getAddressData(trim($zipcode), $number, $addition);
                    $results['addressData'] = $listOfCities;
            } else {
                $results = null;
            }
        }

        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setBody( json_encode( $results));


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

    public function getApiInfo()
    {
        $config = $this->get('config');

        $apiKey = $config->getByNamespace('MemoPostcodenlPlugin', 'memoPostcodenlKey');
        $apiSecret = $config->getByNamespace('MemoPostcodenlPlugin', 'memoPostcodenlSecret');
        $url = "https://api.postcode.eu/account/v1/info";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $output = curl_exec($ch);
        curl_close ($ch);

        $return = json_decode($output, true);

        return($return);
    }

    public function getRegex()
    {
        $return =[];
        foreach($this->getApiInfo()['countries'] as $key => $value){
            $queryBuilder = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();
            $queryBuilder->select('c.id','a.zipcoderegex')
                ->from('s_core_countries', 'c')
                ->innerJoin('c' , 's_core_countries_attributes', 'a', 'c.id = a.countryID')
                ->where('iso3 = :country')
                ->setParameter(':country', $value);

            $arr[] = $queryBuilder->execute()->fetchAll(\PDO::FETCH_KEY_PAIR);
        }

        foreach($arr as $value) {
            $return += $value;
        }

        return($return);
    }
}