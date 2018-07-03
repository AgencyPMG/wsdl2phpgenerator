<?php

namespace Wsdl2PhpGenerator\Tests\Unit;

use Wsdl2PhpGenerator\Type;
use Wsdl2PhpGenerator\TypeRegistry;

class TypeRegistryTest extends CodeGenerationTestCase
{
    private $types;

    public function testTypesThatAreNotRegisteredReturnFalseFromHas()
    {
        $this->assertFalse($this->types->has('SomeType'));
    }

    public function testTypesThatAreNotRegisteredReturnNullFromGet()
    {
        $this->assertNull($this->types->get('SomeType'));
    }

    public function testTypesThatAreRegisteredReturnTrueFromHas()
    {
        $type = $this->createMock(Type::class);
        $type->expects($this->once())
            ->method('getIdentifier')
            ->willReturn('SomeType');
        $this->types->add($type);

        $this->assertTrue($this->types->has('SomeType'));
    }

    public function testTypesThatAreRegisteredAreReturnsFromGet()
    {
        $type = $this->createMock(Type::class);
        $type->expects($this->once())
            ->method('getIdentifier')
            ->willReturn('SomeType');
        $this->types->add($type);

        $this->assertSame($type, $this->types->get('SomeType'));
    }

    public function testTypesCanBeRemovedFromTheRegistry()
    {
        $t1 = $this->createMock(Type::class);
        $t1->expects($this->once())
            ->method('getIdentifier')
            ->willReturn('SomeType');
        $t2 = $this->createMock(Type::class);
        $t2->expects($this->once())
            ->method('getIdentifier')
            ->willReturn('OtherType');
        $this->types->add($t1);
        $this->types->add($t2);
        $this->assertTrue($this->types->has('SomeType'));
        $this->assertTrue($this->types->has('OtherType'));

        $this->types->remove('SomeType');

        $this->assertFalse($this->types->has('SomeType'));
        $this->assertTrue($this->types->has('OtherType'));
    }

    protected function setUp()
    {
        $this->types = new TypeRegistry();
    }
}
