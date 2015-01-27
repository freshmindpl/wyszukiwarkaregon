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
    }

    /**
     * @return Service
     */
    public function get()
    {
        return $this->service;
    }
}