<?php

namespace WyszukiwarkaRegon\Tests;

class LoginTest extends AbstractTest
{
    public function testLoginSuccess()
    {
        $result = new \stdClass();
        $result->ZalogujResult = 'aaaabbbbccccdddd';
        $client = $this->createClient($result);
        $this->assertSame('aaaabbbbccccdddd', $client->login());
    }

    /**
     * @expectedException \WyszukiwarkaRegon\Exception\RegonException
     */
    public function testLoginException()
    {
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

    /**
     * @expectedException \WyszukiwarkaRegon\Exception\RegonException
     */
    public function testLogoutException()
    {
        $sopaFault = new \SoapFault("test", "myMessage");
        $client = $this->createFault($sopaFault);
        $client->logout();
    }

    /**
     * @expectedException \WyszukiwarkaRegon\Exception\InvalidKeyException
     */
    public function testLoginInvalidKeyException()
    {
        $result = new \stdClass();
        $result->ZalogujResult = '';
        $client = $this->createClient($result);
        $client->login();
    }
}
