<?php

use \mstevz\collection\ArrayList;
use PHPUnit\Framework\TestCase;

class ArrayListTest extends TestCase {

    private $instance;

    protected function setUp() : void {
        $this->instance = new ArrayList();
    }

    public function getArrayTypesProvider(){
        return array(
            array(
                '*',
                'bool',
                'boolean',
                'int',
                'integer',
                'double',
                'array',
                'object',
                'resource',
            )
        );
    }

    public function getArrayTypesExamplesProvider(){
        return array(
                array('*', 'exampleSomething'),
                array('boolean', 0),
                array('boolean', '0'),
                array('boolean', '1'),
                array('boolean', true),
                array('integer', 42),
                array('integer', '42'),
                array('double', 123.45),
                array('double', '123.45'),
                array('string', 'hello world'),
                array('array', array('hello', 'world')),
                array('object', new class{}),
                array('resource', imagecreate(2,2)),
        );
    }

    public function getArrayTypesExampleWithoutCastProvider(){
        return array(
                array('*', 'exampleSomething'),
                array('boolean', true),
                array('integer', 42),
                array('double', 1.234),
                array('string', 'hello world'),
                array('array', array('hello', 'world')),
                array('object', new class{}),
                array('resource', imagecreate(2,2)),
        );
    }

    public function getIncorrectArrayTypeExampleProvider(){
        return array(
                array('integer', array('hello')),
                array('double', true),
                array('array', new class{}),
                array('object', 'something'),
                array('resource', 2),
        );
    }

    /**
     * [testAddFunction description]
     * @dataProvider getArrayTypesProvider
     */
    public function testCanRestrictTypeOfArrayItemContent(string $type) {
        $list = new ArrayList($type);
        $this->assertInstanceOf(ArrayList::class, $list);
    }

    /**
     * [testThrowsExceptionIfTypeOfArrayItemIsInvalid description]
     */
    public function testThrowsExceptionIfTypeOfArrayItemIsInvalid() {
        $this->expectException(\InvalidArgumentException::class);

        $arr = new ArrayList('invalidTypeExample');
    }

    /**
     * [testAddFunctionWithCasting description]
     * @dataProvider getArrayTypesExamplesProvider
     * @depends testCanRestrictTypeOfArrayItemContent
     */
    public function testAddFunctionWithCasting(string $type, $value){
        $list = new ArrayList($type);
        $list->add($value);
        $this->assertTrue(true);
    }

    /**
     * [testAddFunctionWithoutCasting description]
     * @dataProvider getArrayTypesExampleWithoutCastProvider
     * @depends testCanRestrictTypeOfArrayItemContent
     */
    public function testAddFunctionWithoutCasting(string $type, $value){
        $list = new ArrayList($type, false);
        $list->add($value);
        $this->assertTrue(true);
    }

    /**
     * [testAddFunctionWithCastingException description]
     * @dataProvider getIncorrectArrayTypeExampleProvider
     * @depends testCanRestrictTypeOfArrayItemContent
     */
    public function testAddFunctionWithCastingCanThrowException(string $type, $value){
        $this->expectException(\Exception::class);

        $list = new ArrayList($type);
        $list->add($value);
    }

    /**
     * [testAddFunctionWithCastingException description]
     * @dataProvider getIncorrectArrayTypeExampleProvider
     * @depends testCanRestrictTypeOfArrayItemContent
     */
    public function testAddFunctionWithoutCastingCanThrowException(string $type, $value){
        $this->expectException(\Exception::class);

        $list = new ArrayList($type, false);
        $list->add($value);
    }
}

?>
