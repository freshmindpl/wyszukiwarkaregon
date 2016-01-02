<?php

namespace WyszukiwarkaRegon;

use WyszukiwarkaRegon\Enum\GetValue;
use WyszukiwarkaRegon\Exception\InvalidKeyException;
use WyszukiwarkaRegon\Exception\RegonException;
use WyszukiwarkaRegon\Exception\SearchException;

class Service
{
    /**
     * @var string
     */
    protected $wsdl = '/wsdl/UslugaBIRzewnPubl.xsd';

    /**
     * @var string
     */
    protected $url = "https://wyszukiwarkaregon.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc";

    /**
     * @var string
     */
    protected $urlSandbox = "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc";

    /**
     * @var string
     */
    protected $key = 'aaaaaabbbbbcccccdddd';

    /**
     * @var Transport
     */
    protected $transport;

    /**
     * @var string
     */
    protected $streamContext = null;

    /**
     * @var array
     */
    protected $getValueDictionary = [
        Enum\GetValue::DATA_STATUS,
        Enum\GetValue::ERROR_CODE,
        Enum\GetValue::ERROR_MESSAGE,
        Enum\GetValue::SESSION_STATUS,
        Enum\GetValue::SERVICE_STATUS,
        Enum\GetValue::SERVICE_MESSAGE
    ];

    /**
     * @var string
     */
    protected $sid = null;

    /**
     *
     */
    public function __construct()
    {
        $this->streamContext = stream_context_create();

        $this->transport = new Transport(
            dirname(__DIR__) . $this->wsdl,
            array(
                'soap_version' => SOAP_1_2,
                'exception' => true,
                'stream_context' => $this->streamContext,
                'location' => $this->url
            )
        );
    }

    /**
     * @param \SoapClient $client
     */
    public function setTransport(\SoapClient $client)
    {
        $this->transport = $client;
    }

    /**
     * Set access key
     *
     * @param $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @param string $sid
     */
    public function setSession($sid)
    {
        $this->sid = $sid;

        // Set the HTTP headers.
        stream_context_set_option($this->streamContext, array('http' => array('header' => 'sid: ' . $this->sid)));
    }

    /**
     * Enable sandbox mode
     *
     */
    public function setSandbox()
    {
        $this->transport->__setLocation($this->urlSandbox);
    }

    /**
     * @return string
     */
    public function login()
    {
        $params = [
            'pKluczUzytkownika' => $this->key
        ];

        try {
            $response = $this->transport->__soapCall('Zaloguj', [$params]);
        } catch (\SoapFault $e) {
            throw new RegonException($e->getMessage(), 0, $e);
        }

        if (empty($response->ZalogujResult)) {
            //Invalid key
            throw new InvalidKeyException('Invalid api key', 99);
        }

        $this->setSession($response->ZalogujResult);

        return $response->ZalogujResult;
    }

    /**
     * @return boolean
     */
    public function logout()
    {
        $params = [
            'pIdentyfikatorSesji' => $this->sid
        ];

        try {
            $response = $this->transport->__soapCall('Wyloguj', [$params]);
        } catch (\SoapFault $e) {
            throw new RegonException($e->getMessage(), 0, $e);
        }

        $this->setSession(null);

        return (bool)$response->WylogujResult;
    }

    /**
     * @param $key
     * @throws \Exception
     */
    public function getValue($key)
    {
        if (!in_array($key, $this->getValueDictionary)) {
            throw new RegonException(
                'Unknown getValue key. Supported values are ' . implode(', ', $this->getValueDictionary)
            );
        }

        $params = [
            'pNazwaParametru' => $key
        ];

        try {
            $response = $this->transport->__soapCall('GetValue', [$params]);
        } catch (\SoapFault $e) {
            throw new RegonException($e->getMessage(), 0, $e);
        }

        return $response->GetValueResult;
    }

    /**
     * @param array $condition
     * @return mixed
     */
    public function search(array $condition)
    {
        $params = [
            'pParametryWyszukiwania' => $condition
        ];

        try {
            $response = $this->transport->__soapCall('DaneSzukaj', [$params]);
        } catch (\SoapFault $e) {
            throw new RegonException($e->getMessage(), 0, $e);
        }

        if (empty($response->DaneSzukajResult)) {
            $message = $this->getValue(GetValue::ERROR_MESSAGE);
            $code = $this->getValue(GetValue::ERROR_CODE);

            if ($message || $code) {
                throw new SearchException($message, $code);
            }

            throw new SearchException('', Enum\GetValue::SEARCH_ERROR_SESSION);
        }

        return $this->normalizeResponse($response->DaneSzukajResult);
    }

    /**
     * @param string $regon
     * @param string $name
     * @return array|mixed
     */
    public function report($regon, $name)
    {
        $params = [
            'pRegon' => $regon,
            'pNazwaRaportu' => $name
        ];

        try {
            $response = $this->transport->__soapCall('DanePobierzPelnyRaport', [$params]);
        } catch (\SoapFault $e) {
            throw new RegonException($e->getMessage(), 0, $e);
        }

        if (empty($response->DanePobierzPelnyRaportResult)) {
            $message = $this->getValue(GetValue::ERROR_MESSAGE);
            $code = $this->getValue(GetValue::ERROR_CODE);

            if ($message || $code) {
                throw new SearchException($message, $code);
            }

            throw new SearchException('', Enum\GetValue::SEARCH_ERROR_SESSION);
        }

        return $this->normalizeResponse($response->DanePobierzPelnyRaportResult);
    }

    /**
     * @return mixed
     */
    public function captcha()
    {
        try {
            $response = $this->transport->__soapCall('PobierzCaptcha', []);
        } catch (\SoapFault $e) {
            throw new RegonException($e->getMessage(), 0, $e);
        }

        return $response->PobierzCaptchaResult;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function verify($code)
    {
        $params = [
            'pCaptcha' => (string)$code
        ];

        try {
            $response = $this->transport->__soapCall('SprawdzCaptcha', [$params]);
        } catch (\SoapFault $e) {
            throw new RegonException($e->getMessage(), 0, $e);
        }

        return (bool)$response->SprawdzCaptchaResult;
    }

    /**
     * Normalize response
     *
     * @param $response
     * @return array|mixed
     */
    private function normalizeResponse($response)
    {
        $json = json_decode($response, true);

        if ($json !== null) {
            return $json;
        }

        //Load as XMl
        $xml = @simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);

        if (!$xml) {
            return [];
        }

        $array = $this->xml2array($xml);

        return array_values($array);
    }

    /**
     * Xml to array
     *
     * @param $xml
     * @return array
     */
    private function xml2array($xml)
    {
        $arr = array();
        foreach ($xml as $element) {
            /** @var \SimpleXmlElement $element */
            $tag = $element->getName();
            $e = get_object_vars($element);
            $arr[$tag] = trim($element);
            if (!empty($e)) {
                $arr[$tag] = $element instanceof \SimpleXMLElement ? $this->xml2array($element) : $e;
            }
        }

        return $arr;
    }
}
