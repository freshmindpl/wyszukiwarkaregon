<?php

namespace WyszukiwarkaRegon\Tests;

use WyszukiwarkaRegon\Client;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getClient()
    {
        $client = $this->getMockFromWsdl('wsdl/UslugaBIRzewnPubl.xsd', 'UslugaBIRzewnPubl');

        return $client;
    }

    /**
     * @param \stdClass $response
     * @return Client
     */
    protected function createClient(\stdClass $response)
    {
        $client = $this->getClient();

        $client->expects($this->any())
            ->method('__soapCall')
            ->will($this->returnValue($response));

        return new Client([
            'client' => $client
        ]);
    }

    /**
     * @param \SoapFault $fault
     * @return Client
     */
    protected function createFault(\SoapFault $fault)
    {
        $client = $this->getMockFromWsdl('wsdl/UslugaBIRzewnPubl.xsd', 'UslugaBIRzewnPubl');

        $client->expects($this->any())
            ->method('__soapCall')
            ->will($this->throwException($fault));

        return new Client([
            'client' => $client
        ]);
    }
}
