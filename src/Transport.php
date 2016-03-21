<?php

namespace WyszukiwarkaRegon;

class Transport extends \SoapClient
{
    public $location;

    public function __soapCall(
        $function_name,
        $arguments,
        $options = null,
        $input_headers = null,
        &$output_headers = null
    ) {
        $header[] = new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', $this->location, 0);
        $header[] = new \SoapHeader(
            'http://www.w3.org/2005/08/addressing',
            'Action',
            $this->getAction($function_name),
            0
        );

        $this->__setSoapHeaders(null);
        $this->__setSoapHeaders($header);

        return parent::__soapCall(
            $function_name,
            $arguments,
            $options,
            $input_headers,
            $output_headers
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        $location = $this->location;
        $response = parent::__doRequest($request, $location, $action, $version);
        if (!$response) {
            return $response;
        }
        $matches = array();
        preg_match('/<s:Envelope.*<\/s:Envelope>/s', $response, $matches);

        return $matches[0];
    }

    /**
     * @param string $function_name
     * @return string
     */
    private function getAction($function_name)
    {
        switch ($function_name) {

            case 'GetValue':
            case 'PobierzCaptcha':
            case 'SprawdzCaptcha':
                $prefix = 'http://CIS/BIR/2014/07/IUslugaBIR';
                break;

            default:
                $prefix = 'http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl';
        }

        return $prefix . '/' . $function_name;
    }
}
