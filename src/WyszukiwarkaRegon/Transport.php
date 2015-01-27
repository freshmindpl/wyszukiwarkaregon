<?php

namespace WyszukiwarkaRegon;


class Transport
{
    /**
     * @var bool
     */
    protected $is_success = false;

    /**
     * @var boolean
     */
    protected $ssl_verify = true;

    /**
     * @var string
     */
    protected $base_url = null;

    /**
     * @var array
     */
    protected $http_errors = array
    (
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Timeout',
    );

    /**
     * Request state getter
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->is_success;
    }

    /**
     * @param $url
     */
    public function setBaseUrl($url)
    {
        $this->base_url = $url;
    }

    /**
     * @param string $method
     * @param string $request
     * @param array $params
     * @param array $headers - Custom headers
     * @return array
     */
    public function call($method, $request, $params, $headers = [])
    {
        $this->is_success = false;

        if (is_object($params)) {
            $params = (array)$params;
        }

        $params_encoded = json_encode($params);

        $response = $this->pushData($method, $request, $params_encoded, $headers);

        $response = json_decode($response, true);

        if ($response) {
            $this->is_success = true;
        }

        return $response;
    }

    /**
     * @param string $method
     * @param string $method_type
     * @param array $request - JSON
     * @param array $headers - Custom headers
     * @return array
     * @throws \Exception
     */
    protected function pushData($method, $method_type, $request, $headers = [])
    {
        $customHeaders[] = 'Content-type: application/json';
        foreach ($headers as $header => $value) {
            $customHeaders[] = $header . ": " . $value;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->base_url . $method);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method_type));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verify);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (isset($this->http_errors[$http_code])) {
            throw new \Exception('Response Http Error: ' . $this->http_errors[$http_code]);
        }
        if (0 < curl_errno($ch)) {
            throw new \Exception('Unable to connect to ' . $this->base_url . ' Error: ' . curl_error($ch));
        }
        curl_close($ch);

        return $response;
    }
}