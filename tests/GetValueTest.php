<?php

namespace WyszukiwarkaRegon\Tests;

use WyszukiwarkaRegon\Enum\GetValue;
use WyszukiwarkaRegon\Exception\RegonException;

class GetValueTest extends AbstractTest
{
    public function testGetValueSuccess()
    {
        $result = new \stdClass();
        $result->GetValueResult = 1;
        $client = $this->createClient($result);
        $this->assertSame(1, $client->getValue(GetValue::ERROR_CODE));
    }

    public function testGetValueError()
    {
        $result = new \stdClass();
        $result->GetValueResult = 1;
        $client = $this->createClient($result);

        try {
            $client->getValue('wrong value');
        } catch (RegonException $e) {
            $this->assertContains('Unknown getValue key', $e->getMessage());
        }
    }

    /**
     * @expectedException \WyszukiwarkaRegon\Exception\RegonException
     */
    public function testLoginException()
    {
        $sopaFault = new \SoapFault("test", "myMessage");
        $client = $this->createFault($sopaFault);
        $client->getValue(GetValue::ERROR_CODE);
    }
}
