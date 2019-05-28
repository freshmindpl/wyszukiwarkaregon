<?php

namespace WyszukiwarkaRegon\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use WyszukiwarkaRegon\Client;

abstract class AbstractTest extends TestCase
{

    /**
     * @return MockObject
     * @throws \ReflectionException
     */
    protected function getClient()
    {
        $client = $this->getMockFromWsdl('wsdl/UslugaBIRzewnPubl-ver11-prod.wsdl', 'UslugaBIRzewnPubl');

        return $client;
    }

    /**
     * @param stdClass $response
     * @return Client
     * @throws \ReflectionException
     */
    protected function createClient(stdClass $response)
    {
        $client = $this->getClient();

        $client->expects($this->any())
            ->method('__soapCall')
            ->will($this->returnValue($response));

        return new Client([
            'client' => $client,
        ]);
    }

    /**
     * @param \SoapFault $fault
     * @return Client
     * @throws \ReflectionException
     */
    protected function createFault(\SoapFault $fault)
    {
        $client = $this->getMockFromWsdl('wsdl/UslugaBIRzewnPubl.xsd', 'UslugaBIRzewnPubl');

        $client->expects($this->any())
            ->method('__soapCall')
            ->will($this->throwException($fault));

        return new Client([
            'client' => $client,
        ]);
    }
}
