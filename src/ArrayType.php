<?php

/**
 * @package Generator
 */
namespace Wsdl2PhpGenerator;

use \Exception;
use Wsdl2PhpGenerator\PhpSource\PhpDocComment;
use Wsdl2PhpGenerator\PhpSource\PhpDocElementFactory;
use Wsdl2PhpGenerator\PhpSource\PhpFunction;

/**
 * ArrayType
 *
 * @package Wsdl2PhpGenerator
 */
class ArrayType extends ComplexType
{
    /**
     * Field with array
     *
     * @var Variable
     */
    protected $field;

    /**
     * Type of array elements
     *
     * @var string
     */
    protected $arrayOf;

    /**
     * Implements the loading of the class object
     *
     * @throws Exception if the class is already generated(not null)
     */
    protected function generateClass()
    {
        parent::generateClass();

        // If it is child type, fallback to ComplexType. Can check this only when all
        // types are loaded. See Generator->loadTypes();
        if ($this->getBaseTypeClass() === null) {
            $this->implementArrayInterfaces();
        }
    }

    protected function implementArrayAccess()
    {
        $this->class->addImplementation('\\ArrayAccess');
        $description = 'ArrayAccess implementation';

        $offsetExistsDock = new PhpDocComment();
        $offsetExistsDock->setDescription($description);
        $offsetExistsDock->addParam(PhpDocElementFactory::getParam('mixed', 'offset', 'An offset to check for'));
        $offsetExistsDock->setReturn(PhpDocElementFactory::getReturn('boolean', 'true on success or false on failure'));
        $offsetExists = new PhpFunction(
            'public',
            'offsetExists',
            $this->buildParametersString(
                array(
                    'offset' => 'mixed'
                ),
                false,
                false
            ),
            '  return isset($this->' . $this->field->getName() . '[$offset]);',
            $offsetExistsDock
        );
        $this->class->addFunction($offsetExists);

        $offsetGetDock = new PhpDocComment();
        $offsetGetDock->setDescription($description);
        $offsetGetDock->addParam(PhpDocElementFactory::getParam('mixed', 'offset', 'The offset to retrieve'));
        $offsetGetDock->setReturn(PhpDocElementFactory::getReturn($this->arrayOf, ''));
        $offsetGet = new PhpFunction(
            'public',
            'offsetGet',
            $this->buildParametersString(
                array(
                    'offset' => 'mixed'
                ),
                false,
                false
            ),
            '  return $this->' . $this->field->getName() . '[$offset];',
            $offsetGetDock
        );
        $this->class->addFunction($offsetGet);

        $offsetSetDock = new PhpDocComment();
        $offsetSetDock->setDescription($description);
        $offsetSetDock->addParam(PhpDocElementFactory::getParam('mixed', 'offset', 'The offset to assign the value to'));
        $offsetSetDock->addParam(PhpDocElementFactory::getParam($this->arrayOf, 'value', 'The value to set'));
        $offsetSetDock->setReturn(PhpDocElementFactory::getReturn('void', ''));
        $offsetSet = new PhpFunction(
            'public',
            'offsetSet',
            $this->buildParametersString(
                array(
                    'offset' => 'mixed',
                    'value' => $this->arrayOf
                ),
                false,
                false
            ),
            '  if (!isset($offset)) {' . PHP_EOL .
            '    $this->' . $this->field->getName() . '[] = $value;' . PHP_EOL .
            '  } else {' . PHP_EOL .
            '    $this->' . $this->field->getName() . '[$offset] = $value;' . PHP_EOL .
            '  }',
            $offsetSetDock
        );
        $this->class->addFunction($offsetSet);

        $offsetUnsetDock = new PhpDocComment();
        $offsetUnsetDock->setDescription($description);
        $offsetUnsetDock->addParam(PhpDocElementFactory::getParam('mixed', 'offset', 'The offset to unset'));
        $offsetUnsetDock->setReturn(PhpDocElementFactory::getReturn('void', ''));
        $offsetUnset = new PhpFunction(
            'public',
            'offsetUnset',
            $this->buildParametersString(
                array(
                    'offset' => 'mixed',
                ),
                false,
                false
            ),
            '  unset($this->' . $this->field->getName() . '[$offset]);',
            $offsetUnsetDock
        );
        $this->class->addFunction($offsetUnset);
    }

    protected function implementIterator()
    {
        $this->class->addImplementation('\\IteratorAggregate');

        $docblock = new PhpDocComment();
        $docblock->setDescription('Traversable Implementation');
        $docblock->setReturn(PhpDocElementFactory::getReturn($this->arrayOf.'[]', 'Return an iterator of the elements'));
        $iter = new PhpFunction(
            'public',
            'getIterator',
            $this->buildParametersString(array(), false, false),
            sprintf(
                '    return new \ArrayIterator($this->%s);',
                $this->field->getName()
            ),
            $docblock
        );
        $this->class->addFunction($iter);
    }

    protected function implementCountable()
    {
        $this->class->addImplementation('\\Countable');
        $description = 'Countable implementation';

        $countDock = new PhpDocComment();
        $countDock->setDescription($description);
        $countDock->setReturn(PhpDocElementFactory::getReturn($this->arrayOf, 'Return count of elements'));
        $count = new PhpFunction(
            'public',
            'count',
            $this->buildParametersString(
                array(),
                false,
                false
            ),
            '  return count($this->' . $this->field->getName() . ');',
            $countDock
        );
        $this->class->addFunction($count);
    }

    protected function implementExchangeArray()
    {
        $doc = new PhpDocComment();
        $doc->setDescription('Change the current array with another');
        $doc->setReturn(PhpDocElementFactory::getReturn($this->arrayOf.'[]|null', 'The previous array if present'));
        $ex = new PhpFunction(
            'public',
            'exchangeArray',
            $this->buildParametersString([
                $this->field->getName() => 'array',
            ], true, false),
            implode(PHP_EOL, [
                sprintf('    $prev = $this->%s;', $this->field->getName()),
                sprintf('    $this->%1$s = $%1$s;', $this->field->getName()),
                '    return $prev;'
            ]),
            $doc
        );

        $this->class->addFunction($ex);
    }

    protected function implementArrayInterfaces()
    {
        $members = array_values($this->members);
        $this->field = $members[0];
        $this->arrayOf = substr($this->field->getType(), 0, -2);

        $this->implementArrayAccess();
        $this->implementIterator();
        $this->implementCountable();
        $this->implementExchangeArray();
    }
}
