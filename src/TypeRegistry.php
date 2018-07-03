<?php declare(strict_types=1);

/**
 * @package Wsdl2PhpGenerator
 */
namespace Wsdl2PhpGenerator;

class TypeRegistry implements \IteratorAggregate
{
    private $typesByIdentifier = [];
    private $typesByPhpIdentifier = [];

    public function has(string $identifier) : bool
    {
        return isset($this->typesByIdentifier[$identifier]);
    }

    public function get(string $identifier) : ?Type
    {
        return $this->typesByIdentifier[$identifier] ?? null;
    }

    public function getByPhpIdentifier(string $phpIdentifier) : ?Type
    {
        return $this->typesByPhpIdentifier[$phpIdentifier] ?? null;
    }

    public function add(Type $type) : void
    {
        $this->typesByIdentifier[$type->getIdentifier()] = $type;
        $this->typesByPhpIdentifier[$type->getPhpIdentifier()] = $type;
    }

    public function remove(string $identifier) : void
    {
        unset($this->typesByIdentifier[$identifier]);
    }

    public function getIterator() : \ArrayIterator
    {
        return new \ArrayIterator($this->typesByIdentifier);
    }
}
