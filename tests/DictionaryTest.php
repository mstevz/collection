<?php

use \mstevz\collection\Dictionary;
use PHPUnit\Framework\TestCase;

class DictionaryTest extends TestCase {

    private $instance;

    public function getDataProvider(){
        $result = array(
            array(
                array(
                    'randomKey' => array(
                        'some value 1',
                        'some value 2',
                        123,
                    )
                )
            ),
            array(
                array(
                    'randomKey2' => 'value'
                )
            ),
            array(
                array(
                    'randomKey3' => 456
                )
            ),
            array(
                array(
                    'randomKey4' => new class{}
                )
            )
        );

        return $result;
    }

    public function addValueDataProvider(){
        return array(
            array('key1', 'value one'),
            array('key2', 2),
            array('key3', array('one', 2)),
            array('key4', new class{})
        );
    }

    public function invalidArrayDataProvider(){
        $result = array(
            array(
                array(
                    'links' => array(
                        'http://yahoo.com',
                        'http://google.com',
                        'http://facebook.com',
                        'http://twitter.com'
                    )
                )
            ),
            array(
                array(
                    '123' => array(
                        'http://jquery.com',
                        'http://jqueryui.com'
                    )
                )
            )
        );

        return $result;
    }

    protected function setUp() : void {
        $this->instance = new Dictionary();
    }

    /**
     * Tests if variable is instance of  "mstevz\instance"
     * @return [type] [description]
     */
    public function testInstanceOf(){
        $this->assertInstanceOf(Dictionary::class, $this->instance);
    }

    public function testGetAllFunctionReturnsArray(){
        $result = $this->instance->getAll();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * [testAddFunction description]
     * @dataProvider addValueDataProvider
     * @depends testGetAllFunctionReturnsArray
     * @return [type] [description]
     */
    public function testAddFunction(string $key, $value){
        $this->instance->add($key, $value);

        $container = $this->instance->getAll();

        $this->assertArrayHasKey($key, $container);
    }

    /**
     * [testAddFunctionReturnsDictionary description]
     * @depends testAddFunction
     * @return [type] [description]
     */
    public function testAddFunctionReturnsDictionaryInstance(){
        $this->assertInstanceOf(Dictionary::class, $this->instance->add('key', 'value'));
    }

    /**
     * [testFromArrayFunctionReturnsDictionaryInstance description]
     * @dataProvider getDataProvider
     * @param  [type] $array [description]
     * @return [type]        [description]
     */
    public function testFromArrayFunctionReturnsDictionaryInstance($array){
        $this->assertInstanceOf(Dictionary::class, $this->instance->fromArray($array));
    }

    /**
     * [testRemoveFunctionRemovesProvidedKey description]
     * @depends testFromArrayFunctionReturnsDictionaryInstance
     * @depends testGetAllFunctionReturnsArray
     * @depends testCountFunctionReturnsInt
     * @return [type] [description]
     */
    public function testRemoveFunctionRemovesProvidedKey(){
        $this->instance->fromArray(array(
            "keyOne" => "value1",
            "keyTwo" => array(1,2,3),
            "keyThree" => new class{}
        ));

        $beforeDeleteCount = $this->instance->count();
        $this->instance->remove('keyTwo');
        $afterDeleteCount  = $this->instance->count();

        $container = $this->instance->getAll();

        $this->assertArrayNotHasKey('keyTwo', $container);
        $this->assertEquals($beforeDeleteCount-1, $afterDeleteCount);
    }

    /**
     * [testGetFunctionReturnsValue description]
     * @dataProvider addValueDataProvider
     * @depends testAddFunction
     * @return [type] [description]
     */
    public function testGetFunctionReturnsValue(string $key, $value) {
        $this->instance->add($key, $value);
        $result = $this->instance->get($key);
        $this->assertEquals($value, $result);
    }

    /**
     * [testUpdateFunction description]
     * @depends testAddFunction
     * @depends testGetFunctionReturnsValue
     * @return [type] [description]
     */
    public function testUpdateChangesDictionaryContainerValue(){
        $key = 'keyValue';
        $value = 'value';

        $this->instance->add($key, $value);

        $value = 'new value';

        $this->instance->update($key, $value);

        $this->assertEquals($value, $this->instance->get($key));
    }

    /**
     * [testIndexOfReturnsInt description]
     * @dataProvider addValueDataProvider
     * @depends testAddFunction
     * @return [type] [description]
     */
    public function testIndexOfReturnsInt(string $key, $value){
        $this->instance->add($key, $value);
        $index = $this->instance->indexOf($key);
        $this->assertIsInt($index);
    }

    /**
     * [testKeyOfReturnsValue description]
     * @dataProvider addValueDataProvider
     * @depends testIndexOfReturnsInt
     * @param  string $key   [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function testKeyOfReturnsValueOfIndexKey(string $key, $value){
        $this->instance->add($key, $value);
        $index = $this->instance->indexOf($key);
        $keyResultValue = $this->instance->keyOf($index);

        $this->assertEquals($key, $keyResultValue);
    }

    public function testCountFunctionReturnsInt(){
        $result = $this->instance->count();

        $this->assertIsInt($result);
        $this->assertEquals(0, $result);
    }

    /**
     * [testClearFunction description]
     * @dataProvider getDataProvider
     * @depends testFromArrayFunctionReturnsDictionaryInstance
     * @depends testCountFunctionReturnsInt
     * @depends testGetAllFunctionReturnsArray
     *
     */
    public function testClearFunctionClearsValueContainer($array){
        $this->instance->fromArray($array);

        $this->instance->clear();

        $this->assertEquals(0, $this->instance->count());
        $this->assertEmpty($this->instance->getAll());
    }

    /**
     * [testClearFunction description]
     * @dataProvider getDataProvider
     * @depends testFromArrayFunctionReturnsDictionaryInstance
     * @depends testCountFunctionReturnsInt
     * @depends testGetOffsetsReturnsArrayAndContainsKeys
     *
     * @return [type] [description]
     */
    public function testClearFunctionClearsKeyContainer($array){
        $this->instance->fromArray($array);

        $this->instance->clear();
        $this->assertEmpty($this->instance->getOffsets());
    }

    /**
     * [testClearFunction description]
     * @dataProvider getDataProvider
     * @depends testFromArrayFunctionReturnsDictionaryInstance
     * @return [type] [description]
     */
    public function testClearFunctionReturnsInstance($array){
        $this->instance->fromArray($array);
        $result = $this->instance->clear();
        $this->assertInstanceOf(Dictionary::class, $result);
    }

    /**
     * [testGetAllOffsetsReturnsArray description]
     * @param  [type] $key   [description]
     * @param  [type] $value [description]
     * @depends testFromArrayFunctionReturnsDictionaryInstance
     * @depends testAddFunction
     * @dataProvider addValueDataProvider
     *
     * @return [type]        [description]
     */
    public function testGetOffsetsReturnsArrayAndContainsKeys(string $key, $value){
        $this->instance->add($key, $value);

        $keyContainers = $this->instance->getOffsets();

        $this->assertIsArray($keyContainers);
        $this->assertContains($key, $keyContainers);
    }

    /**
     * [testOffsetExistsReturnsBool description]
     * @depends testAddFunction
     * @depends testIndexOfReturnsInt
     * @dataProvider addValueDataProvider
     */
    public function testOffsetExistsFunctionReturnsTrue(string $key, $value){
        $this->instance->add($key, $value);

        $index = $this->instance->indexOf($key);

        $this->assertTrue($this->instance->offsetExists($key));
        $this->assertTrue($this->instance->offsetExists($index));
    }

    /**
     * [testOffsetExistsFunctionReturnsFalse description]
     * @param string $key   [description]
     * @param [type] $value [description]
     * @depends testAddFunction
     * @dataProvider addValueDataProvider
     */
    public function testOffsetExistsFunctionReturnsFalse(string $key, $value){
        $this->instance->add($key, $value);

        // Invalid information for current data provided
        $invalidOffset = 'some key that obviously does not exists';
        $invalidIndexOffset = 2;

        $this->assertFalse($this->instance->offsetExists($invalidOffset));
        $this->assertFalse($this->instance->offsetExists($invalidIndexOffset));
    }

    /**
     * [testEachFunctionReturnsArray description]
     * @param [type] $values [description]
     * @depends testFromArrayFunctionReturnsDictionaryInstance
     * @dataProvider getDataProvider
     */
    public function testEachFunctionReturnsArray($values) {
        $this->instance->fromArray($values);

        $result = $this->instance->each(function($key, $value){
            return $value;
        });

        $this->assertIsArray($result);
    }

    /**
     * [testFindFunctionReturnsValue description]
     * @depends testFromArrayFunctionReturnsDictionaryInstance
     * @dataProvider getDataProvider
     */
    public function testFindFunctionReturnsValue(array $values) {
        $this->instance->fromArray($values);

        $result = $this->instance->find(function($key, $value){
            return $key = 'randomKey2';
        });

        $this->assertNotEmpty($result);
    }

    /**
     * [testFindFunctionReturnsDesiredValue description]
     * @param  string $key   [description]
     * @param  [type] $value [description]
     * @depends testFindFunctionReturnsValue
     * @depends testAddFunction
     * @dataProvider addValueDataProvider
     */
    public function testFindFunctionReturnsDesiredValue(string $key, $value){
        $this->instance->add($key, $value);

        $result = $this->instance->find(function($key, $value){
            return $key = 'key3';
        });

        $this->assertEquals($value, $result);
    }

    /**
     * [testCanIterateInstance description]
     * @param  array  $values [description]
     * @depends testFromArrayFunctionReturnsDictionaryInstance
     * @dataProvider getDataProvider
     * @return [type]         [description]
     */
    public function testCanIterateInstance(array $values){
        $this->instance->fromArray($values);
        $count = 0;

        foreach($this->instance as $key => $value){
            $this->assertIsString($key);
            $count++;
        }

        $this->assertEquals(sizeof($values),$count);
    }


}

?>
