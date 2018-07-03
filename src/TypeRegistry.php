<?php declare(strict_types=1);

/**
 * @package Wsdl2PhpGenerator
 */
namespace Wsdl2PhpGenerator;

class TypeRegistry implements \IteratorAggregate
{
    private $types = [];

    public function has(string $typename) : bool
    {
        return isset($this->types[$typename]);
    }

    public function get(string $typename) : ?Type
    {
        return $this->types[$typename] ?? null;
    }

    public function add(Type $type) : void
    {
        $this->types[$type->getIdentifier()] = $type;
    }

    public function remove(string $typename) : void
    {
        unset($this->types[$typename]);
    }

    public function getIterator() : \ArrayIterator
    {
        return new \ArrayIterator($this->types);
    }
}
