<?php

namespace Okxe\Elasticsearch\Tests;

use Tests\TestCase;

class BodyTraitTest extends TestCase
{
    private $traitMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitMock = $this->getMockForTrait('\Okxe\Elasticsearch\Traits\BodyTrait');
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBodyTraitExistMethods()
    {
        $this->assertContains('getBody', get_class_methods($this->traitMock));
        $this->assertContains('setBody', get_class_methods($this->traitMock));
        $this->assertContains('get', get_class_methods($this->traitMock));
        $this->assertContains('set', get_class_methods($this->traitMock));
    }

    public function testSetBodyMethod()
    {
        $expect = ['value' => 'key'];
        $this->traitMock->setBody($expect);
        $this->assertEquals($expect, $this->traitMock->body);
    }

    public function testGetBodyMethod()
    {
        $expect = ['value' => 'key'];
        $this->traitMock->setBody($expect);
        $this->assertIsArray($this->traitMock->getBody());
        $this->assertEquals($expect, $this->traitMock->getBody());
    }
}
