<?php

use PostcodeNl\Api\Client;
use PostcodeNl\Api\Exception\AuthenticationException;
use PostcodeNl\Api\Exception\BadRequestException;
use PostcodeNl\Api\Exception\ClientException;
use PostcodeNl\Api\Exception\CurlException;
use PostcodeNl\Api\Exception\CurlNotLoadedException;
use PostcodeNl\Api\Exception\ForbiddenException;
use PostcodeNl\Api\Exception\InvalidJsonResponseException;
use PostcodeNl\Api\Exception\InvalidPostcodeException;
use PostcodeNl\Api\Exception\InvalidSessionValueException;
use PostcodeNl\Api\Exception\NotFoundException;
use PostcodeNl\Api\Exception\ServerUnavailableException;
use PostcodeNl\Api\Exception\TooManyRequestsException;
use PostcodeNl\Api\Exception\UnexpectedException;

class Shopware_Controllers_Frontend_PostcodenlApi extends Enlight_Controller_Action
{
    private $client;

    public function countrycheckAction()
    {
        if (!$this->validateClient()) {
            return false;
        }

        $id = $this->Request()->getParam('country') ?: null;

        if (empty($id)) {
            return false;
        }

        $queryBuilder = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();
        $queryBuilder->select('c.iso3')
            ->from('s_core_countries', 'c')
            ->where('id = :id')
            ->setParameter(':id', $id);

        try {
            $supportedCountries = $this->client->internationalGetSupportedCountries();
        } catch(\Exception $e) {
            return $this->jsonResponse(['error' => $this->errorResponse($e)]);
        }

        $iso3 = $queryBuilder->execute()->fetch()['iso3'];
        $isSupported = in_array($iso3, array_column($supportedCountries, "iso3"));

        if ($iso3 === "NLD") {
            $config = $this->get('config');
            $useAutocomplete = $config->getByNamespace('memoPostcodenlPlugin', 'memoUseDutchAddressAutocomplete') ?? false;
        } else {
            $useAutocomplete = $isSupported;
        }

        return $this->jsonResponse([
            'key' => $id,
            'iso3' => $iso3,
            'isSupported' => $isSupported,
            'useAutocomplete' => $useAutocomplete,
        ]);
    }

    public function autocompleteAction()
    {
        if (!$this->validateClient()) {
            return false;
        }

        $params = $this->fixParams($this->Request()->getParams());

        $iso3Context = $params[0];
        $term = $params[1];
        $session = $this->Request()->getHeader("X-Autocomplete-Session");

        try {
            return $this->jsonResponse($this->client->internationalAutocomplete($iso3Context, $term, $session));
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => $this->errorResponse($e)]);
        }
    }

    public function addressDetailsAction()
    {
        if (!$this->validateClient()) {
            return false;
        }

        $params = $this->fixParams($this->Request()->getParams());

        $context = $params[0];
        $session = $this->Request()->getHeader("X-Autocomplete-Session");

        try {
            return $this->jsonResponse($this->client->internationalGetDetails($context, $session));
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => $this->errorResponse($e)]);
        }
    }

    public function dutchAddressAction()
    {
        if (!$this->validateClient()) {
            return false;
        }

        $postcode = ($this->Request()->getParam('zipcode'));
        $houseNumber = ($this->Request()->getParam('housenumber'));
        $houseNumberAddition = ($this->Request()->getParam('addition'));

        try {
            if (!preg_match('/\s*([0-9]{4})\s*([a-zA-Z]{2})\s*/', $postcode, $match)) {
                throw new InvalidPostcodeException(sprintf('Postcode `%s` has an invalid format, it should be in the format `1234AB`.', $postcode));
            }

            $postcode = $match[1] . $match[2];

            $response = $this->client->dutchAddressByPostcode($postcode, $houseNumber, $houseNumberAddition);
            return $this->jsonResponse($response);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => $this->errorResponse($e)], 404);
        }
    }

    private function jsonResponse($response, $status = 200)
    {
        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setBody(json_encode($response));
        $this->Response()->setStatusCode($status);
    }

    private function fixParams($paramBag)
    {
        unset($paramBag['module'], $paramBag['controller'], $paramBag['action']);

        $return = [];
        foreach ($paramBag as $k => $v) {
            if (!is_numeric($k) && strlen($k) > 0) {
                $return[] = $k;
            }
            if (strlen($v) > 0) {
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

        if (empty($apiKey) || empty($apiSecret)) {
            $this->get('pluginlogger')->warning('You have not filled in all required fields, please check your input.');
            return false;
        }

        try {
            $this->client = new Client($apiKey, $apiSecret, "Shopware 5 plugin - Memo ICT");

            $result = $this->client->accountInfo();

            if ($result['hasAccess'] == false) {
                $this->get('pluginlogger')->warning('You don\'t have access to the Postcode.nl API');
                return false;
            }
        } catch (\PostcodeNl\Api\Exception\ClientException $e) {
            $this->get('pluginlogger')->addError($e->getMessage());
            return false;
        }
        return true;
    }

    private function errorResponse(\Exception $e) {
        $snippets = Shopware()->Snippets();

        $namespace = $snippets->getNamespace('frontend/postcodenl');

        switch(get_class($e)) {
            case AuthenticationException::class:
            case BadRequestException::class:
            case ClientException::class:
            case CurlException::class:
            case CurlNotLoadedException::class:
            case ForbiddenException::class:
            case InvalidJsonResponseException::class:
            case InvalidSessionValueException::class:
            case ServerUnavailableException::class:
            case TooManyRequestsException::class:
            case UnexpectedException::class:
            default:
                $error = 'errorGeneral';
                break;
            case InvalidPostcodeException::class:
                $error = 'errorPostcode';
                break;
            case NotFoundException::class:
                $error = 'errorNotFound';
                break;
        }

        return $namespace->get($error, $e->getMessage());
    }
}
