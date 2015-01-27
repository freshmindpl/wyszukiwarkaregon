<?php

namespace WyszukiwarkaRegon;


class Client
{
    /**
     * @var Service
     */
    protected $service;

    /**
     * Create new client
     */
    public function __construct()
    {
        $this->service = new Service();
        $this->_initSession();
    }

    /**
     * Check session support
     */
    private function _initSession()
    {
        if (session_status() == PHP_SESSION_DISABLED) {
            throw new \Exception("Session support is disabled!");
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @return Service
     */
    public function get()
    {
        return $this->service;
    }
}