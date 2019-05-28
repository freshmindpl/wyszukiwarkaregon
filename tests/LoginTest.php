<?php

namespace WyszukiwarkaRegon\Tests;

use WyszukiwarkaRegon\Exception\InvalidKeyException;
use WyszukiwarkaRegon\Exception\RegonException;

class LoginTest extends AbstractTest
{
    public function testLoginSuccess()
    {
        $result = new \stdClass();
        $result->ZalogujResult = 'aaaabbbbccccdddd';
        $client = $this->createClient($result);
        $this->assertSame('aaaabbbbccccdddd', $client->login());
    }

    public function testLoginException()
    {
        $this->expectException(RegonException::class);
        $sopaFault = new \SoapFault("test", "myMessage");
        $client = $this->createFault($sopaFault);
        $client->login();
    }

    public function testLogoutSuccess()
    {
        $result = new \stdClass();
        $result->WylogujResult = true;
        $client = $this->createClient($result);
        $this->assertTrue($client->logout());
    }

    public function testLogoutException()
    {
        $this->expectException(RegonException::class);
        $sopaFault = new \SoapFault("test", "myMessage");
        $client = $this->createFault($sopaFault);
        $client->logout();
    }

    public function testLoginInvalidKeyException()
    {
        $this->expectException(InvalidKeyException::class);
        $result = new \stdClass();
        $result->ZalogujResult = '';
        $client = $this->createClient($result);
        $client->login();
    }
}
