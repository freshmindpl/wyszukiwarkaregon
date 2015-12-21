<?php

namespace WyszukiwarkaRegon\Tests;

class CaptchaTest extends AbstractTest
{
    public function testGetCaptchaSuccess()
    {
        $base64image = "c2Rmc2QgZmRzZiBkc2YgZHNmZHNmZHNmIHNkZmY=";

        $result = new \stdClass();
        $result->PobierzCaptchaResult = $base64image;
        $client = $this->createClient($result);
        $this->assertSame($base64image, $client->captcha());
    }

    /**
     * @expectedException \WyszukiwarkaRegon\Exception\RegonException
     */
    public function testGetCaptchaException()
    {
        $sopaFault = new \SoapFault("test", "myMessage");
        $client = $this->createFault($sopaFault);
        $client->captcha();
    }

    public function testVerifyCaptchaSuccess()
    {
        $result = new \stdClass();
        $result->SprawdzCaptchaResult = true;
        $client = $this->createClient($result);
        $this->assertTrue($client->verify('aaaaaa'));
    }

    /**
     * @expectedException \WyszukiwarkaRegon\Exception\RegonException
     */
    public function testVerifyCaptchaException()
    {
        $sopaFault = new \SoapFault("test", "myMessage");
        $client = $this->createFault($sopaFault);
        $client->verify('aaaaa');
    }
}
