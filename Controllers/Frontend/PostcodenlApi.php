<?php


class Shopware_Controllers_Frontend_PostcodenlApi extends Enlight_Controller_Action
{


    public function indexAction(){

        $this->get('front')->Plugins()->ViewRenderer()->setNoRender();
        $this->get('plugins')->Controller()->Json()->setPadding();


        $zipcode = $this->Request()->getParam('zipcode');
        $number = $this->Request()->getParam('number');
        $addition = $this->Request()->getParam('addition');

        //$results = [$zipcode, $number, $country, $city];
        if(!empty($number) && !empty($zipcode)) {
            $listOfCities = $this->getAddressData(trim($zipcode), $number,$addition);

            $results = [];
            $results['addressData'] = $listOfCities;
        } else {
            $results = false;
        }

        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setBody( json_encode( $results));

    }

    public function getAddressData($zipcode, $number, $addition){

        $config = $this->get('config');

        $apiKey = $config->getByNamespace('MemoPostcodenlPlugin', 'memoPostcodenlKey');
        $apiSecret = $config->getByNamespace('MemoPostcodenlPlugin', 'memoPostcodenlSecret');
        $url = 'https://api.postcode.eu/nl/v1/addresses/postcode/'.$zipcode."/".$number."/".$addition;


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $output = curl_exec($ch);
        curl_close($ch);

        return(json_decode($output));
    }
}