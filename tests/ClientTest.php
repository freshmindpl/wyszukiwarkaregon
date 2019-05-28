<?php

namespace WyszukiwarkaRegon\Tests;

use PHPUnit\Framework\Error\Error;

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

    public function testClientCallBadMethod()
    {
        $this->expectException(\PHPUnit\Framework\Error\Error::class);
        $client = $this->createClient(new \stdClass());
        /** @noinspection PhpUndefinedMethodInspection */
        $client->nonexistent();
    }
}
