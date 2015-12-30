<?php

namespace WyszukiwarkaRegon\Tests;

use WyszukiwarkaRegon\Client;
use WyszukiwarkaRegon\Enum\GetValue;
use WyszukiwarkaRegon\Exception\SearchException;

class SearchTest extends AbstractTest
{
    public function testSearchJsonSuccess()
    {
        $data = array(
            array(
                'Regon' => 1234567890
            )
        );

        $result = new \stdClass();
        $result->DaneSzukajResult = json_encode($data);
        $client = $this->createClient($result);
        $result = $client->search([]);

        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey('Regon', $result[0]);
    }

    public function testSearchXmlSuccess()
    {
        $data = <<<EOD
<root>
  <dane>
    <Regon>12312312312321</Regon>
  </dane>
</root>
EOD;

        $result = new \stdClass();
        $result->DaneSzukajResult = $data;
        $client = $this->createClient($result);
        $result = $client->search([]);

        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey('Regon', $result[0]);
        $this->assertSame('12312312312321', $result[0]['Regon']);
    }

    public function testSearchXmlFailure()
    {
        $data = <<<EOD
  <dane>
    <Regon>12312312312321</Regon>
  </dane>
</root>
EOD;

        $result = new \stdClass();
        $result->DaneSzukajResult = $data;
        $client = $this->createClient($result);
        $response = $client->search([]);
        $this->assertEmpty($response);
    }

    /**
     * @expectedException \WyszukiwarkaRegon\Exception\RegonException
     */
    public function testSearchFault()
    {
        $sopaFault = new \SoapFault("test", "myMessage");
        $client = $this->createFault($sopaFault);
        $client->search([]);
    }

    public function testSearchException()
    {
        $mock = $this->getClient();
        $mock->expects($this->any())
            ->method('__soapCall')
            ->willReturnCallback([$this, 'mockSearchErrorOk']);

        $client = new Client([
            'client' => $mock
        ]);

        try {
            $client->search([]);
        } catch (SearchException $e) {
            $this->assertSame($e->getCode(), 1);
            $this->assertSame($e->getMessage(), 'error message');
        }
    }

    public function testSearchExceptionEmptyGetValue()
    {
        $mock = $this->getClient();
        $mock->expects($this->any())
            ->method('__soapCall')
            ->willReturnCallback([$this, 'mockSearchErrorEmpty']);

        $client = new Client([
            'client' => $mock
        ]);

        try {
            $client->search([]);
        } catch (SearchException $e) {
            $this->assertSame($e->getCode(), 7);
            $this->assertSame($e->getMessage(), '');
        }
    }

    /**
     * @param string $method
     * @param array $params
     * @return \stdClass
     */
    public function mockSearchErrorOk($method, array $params)
    {
        $return = new \stdClass();

        switch ($method) {
            case 'DaneSzukaj':
                $return->DaneSzukajResult = null;
                break;

            case 'GetValue':
                switch ($params[0]['pNazwaParametru']) {
                    case GetValue::ERROR_CODE:
                        $return->GetValueResult = 1;
                        break;
                    case GetValue::ERROR_MESSAGE:
                        $return->GetValueResult = 'error message';
                        break;
                }
                break;
        }

        return $return;
    }

    /**
     * @param string $method
     * @param array $params
     * @return \stdClass
     */
    public function mockSearchErrorEmpty($method, array $params)
    {
        $return = new \stdClass();

        switch ($method) {
            case 'DaneSzukaj':
                $return->DaneSzukajResult = null;
                break;

            case 'GetValue':
                switch ($params[0]['pNazwaParametru']) {
                    case GetValue::ERROR_CODE:
                        $return->GetValueResult = null;
                        break;
                    case GetValue::ERROR_MESSAGE:
                        $return->GetValueResult = null;
                        break;
                }
                break;
        }

        return $return;
    }
}
