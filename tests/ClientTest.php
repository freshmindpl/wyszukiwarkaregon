<?php

namespace WyszukiwarkaRegon\Tests;

class ClientTest extends AbstractTest
{
    public function testClientConstructor()
    {
        $client = $this->createClient(new \stdClass());
        $this->assertSame(get_class($client), 'WyszukiwarkaRegon\\Client');
    }

    public function testClientSetSession()
    {
        $client = $this->createClient(new \stdClass());
        $client = $client->setSession('1234567890');
        $this->assertSame(get_class($client), 'WyszukiwarkaRegon\\Client');
    }

    public function testClientSetKey()
    {
        $client = $this->createClient(new \stdClass());
        $client = $client->setKey('1234567890');
        $this->assertSame(get_class($client), 'WyszukiwarkaRegon\\Client');
    }

    public function testClientSetSandbox()
    {
        $client = $this->createClient(new \stdClass());
        $client = $client->sandbox();
        $this->assertSame(get_class($client), 'WyszukiwarkaRegon\\Client');
    }
    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testClientCallBadMethod()
    {
        $client = $this->createClient(new \stdClass());
        /** @noinspection PhpUndefinedMethodInspection */
        $client->nonexistent();
    }
}
