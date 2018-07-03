<?php

namespace Wsdl2PhpGenerator\Tests\Unit;

use Wsdl2PhpGenerator\Config;
use Wsdl2PhpGenerator\ComplexType;
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
        $this->types->add($this->createType('SomeType'));

        $this->assertTrue($this->types->has('SomeType'));
    }

    public function testTypesThatAreRegisteredAreReturnsFromGet()
    {
        $type = $this->createType('SomeType');
        $this->types->add($type);

        $this->assertSame($type, $this->types->get('SomeType'));
    }

    public function testUnregisteredTypesReturnNullFromGetByPhpIdentifier()
    {
        $this->assertNull($this->types->getByPhpIdentifier('SomeType'));
    }

    public function testRegisterTypesReturnTypeObjectFromGetByPhpIdentifier()
    {
        $type = $this->createType('SomeType');
        $this->types->add($type);

        $this->assertSame($type, $this->types->getByPhpIdentifier('SomeType'));
    }

    public function testTypesCanBeRemovedFromTheRegistry()
    {
        $this->types->add($this->createType('SomeType'));
        $this->types->add($this->createType('OtherType'));
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

    protected function createType(string $name) : Type
    {
        return new ComplexType(new Config([
            'inputFile' => __FILE__,
            'outputDir' => __DIR__,
        ]), $name);
    }
}
