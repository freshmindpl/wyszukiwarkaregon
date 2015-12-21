<?php

namespace WyszukiwarkaRegon\Tests;

class ExampleTest extends AbstractTest
{
    public function testExample()
    {
        $result = new \stdClass();
        $result->ZalogujResult = '1234';
        $client = $this->createClient($result);
        $this->assertSame('1234', $client->login());
    }
}
