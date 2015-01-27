<?php

namespace WyszukiwarkaRegon;


class Client
{
    /**
     * Create new client
     */
    public function __construct()
    {

        $this->_initSession();
    }

    /**
     *
     */
    private function _initSession()
    {
        var_dump(session_status());

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Get captcha image
     */
    public function getCaptcha()
    {

    }

    public function verifyCaptcha($params)
    {

    }

    public function search($params)
    {

    }


}