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

    public function getMockArrayList(){
        return array(
            array(
                'string',
                array(
                    "john doe",
                    "asta",
                    "luffy",
                    "son goku",
                    "nami"
                )
            )
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
    public function testCanAddWithCasting(string $type, $value){
        $list = new ArrayList($type);
        $list->add($value);
        $this->assertTrue(true);
    }

    /**
     * [testAddFunctionWithoutCasting description]
     * @dataProvider getArrayTypesExampleWithoutCastProvider
     * @depends testCanRestrictTypeOfArrayItemContent
     */
    public function testCanAddWithoutCasting(string $type, $value){
        $list = new ArrayList($type, false);
        $list->add($value);
        $this->assertTrue(true);
    }

    /**
     * [testAddFunctionWithCastingException description]
     * @dataProvider getIncorrectArrayTypeExampleProvider
     * @depends testCanRestrictTypeOfArrayItemContent
     */
    public function testCanThrowExceptionWithCastingWhenAdding(string $type, $value){
        $this->expectException(\Exception::class);

        $list = new ArrayList($type);
        $list->add($value);
    }

    /**
     * [testAddFunctionWithCastingException description]
     * @dataProvider getIncorrectArrayTypeExampleProvider
     * @depends testCanRestrictTypeOfArrayItemContent
     */
    public function testCanThrowExceptionWithoutCastingWhenAdding(string $type, $value){
        $this->expectException(\Exception::class);

        $list = new ArrayList($type, false);
        $list->add($value);
    }

    /**
     * [testCanGetLength description]
     * @depends testCanRestrictTypeOfArrayItemContent
     * @return [type] [description]
     */
    public function testCanGetLength() {
        $list = new ArrayList();
        $this->assertEquals(0, $list->getLength());
        $list->add('john');
        $this->assertEquals(1, $list->getLength());
    }

    /**
     * [testCanRemoveFirst description]
     * @dataProvider getMockArrayList
     * @param  string $type   [description]
     * @param  [type] $values [description]
     * @return [type]         [description]
     */
    public function testCanRemoveFirst(string $type, $values){
        $list = new ArrayList($type);
        $needle = 'myFirst';

        $list->add($needle);

        foreach($values as $value){
            $list->add($value);
        }

        $previousLength = $list->getLength();

        $this->assertEquals($needle, $list->removeFirst());
        $this->assertEquals($previousLength - 1, $list->getLength());

    }

    /**
     * [testCanRemoveLast description]
     * @dataProvider getMockArrayList
     * @param  string $type   [description]
     * @param  [type] $values [description]
     * @return [type]         [description]
     */
    public function testCanRemoveLast(string $type, $values){
        $list = new ArrayList($type);
        $needle = 'myLast';

        foreach($values as $value){
            $list->add($value);
        }

        $list->add($needle);

        $previousLength = $list->getLength();

        $this->assertEquals($needle, $list->removeLast());
        $this->assertEquals($previousLength - 1, $list->getLength());
    }

    /**
     * [testCanRemoveByValue description]
     * @dataProvider getMockArrayList
     * @param  string $type   [description]
     * @param  [type] $values [description]
     * @return [type]         [description]
     */
    public function testCanRemoveByValue(string $type, $values){
        $list = new ArrayList($type);

        foreach($values as $value){
            $list->add($value);
        }

        $this->assertTrue($list->removeByValue('son goku'));
    }

    /**
     * [testCanThrowExceptionWhenRemovingByValue description]
     * @dataProvider getMockArrayList
     * @param  string $type   [description]
     * @param  [type] $values [description]
     * @return [type]         [description]
     */
    public function testCanThrowExceptionWhenRemovingByValue(string $type, $values){
        $this->expectException(\Exception::class);
        $list = new ArrayList($type);

        foreach($values as $value){
            $list->add($value);
        }

        $this->assertTrue($list->removeByValue('this value clearly does not exist qahw1290837hqowiduhaw07d'));
    }



}

?>
