<?php

namespace WyszukiwarkaRegon;

/**
 * Class Client
 * @package WyszukiwarkaRegon
 *
 * @method string login() login() login and get session id
 * @method boolean logout() logout() logout current session
 * @method string getValue() getValue(string $key) get param value
 * @method array search() search(array $condition) search the database
 * @method array report() report(string $regon, string $name) get detailed report
 * @method string catpcha() captcha() get captcha image
 * @method boolean verify() verify(string $code) verify captcha code
 */
class Client
{
    /**
     * @var Service
     */
    protected $service;

    /**
     * Create new client
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->service = new Service();

        foreach ($config as $key => $value) {
            if (method_exists($this, 'set' . ucfirst($key))) {
                $this->{'set' . ucfirst($key)}($value);
            }
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        if (!is_callable([$this->service, $name])) {
            trigger_error(
                "Call to undefined method " . __CLASS__ . "::$name()",
                E_USER_ERROR
            );
        }

        return call_user_func_array([$this->service, $name], $arguments);
    }

    /**
     * Set session sid
     *
     * @param string $sid
     * @return $this
     */
    public function setSession($sid)
    {
        $this->service->setSession($sid);

        return $this;
    }

    /**
     * Enable sanbox service
     *
     * @return $this
     */
    public function sandbox()
    {
        $this->service->setSandbox();

        return $this;
    }

    /**
     * Set Api key
     *
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->service->setKey($key);

        return $this;
    }

    /**
     * @param \SoapClient $client
     * @return $this
     */
    public function setClient(\SoapClient $client)
    {
        $this->service->setTransport($client);

        return $this;
    }
}
