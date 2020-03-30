<?php

namespace mstevz\collection;

use \ArrayObject;
use \Psr\Container\ContainerInterface as IContainer;

/**
 *
 * @author Miguel Esteves <dev.mstevz@mail.com>
 * @license https://github.com/mstevz/collection/blob/master/LICENSE
 */
class ArrayList extends ArrayObject implements IContainer {

    /**
     * Item values.
     * @var array
     */
    protected $container;

    /**
     * Size of the container.
     * @var int
     */
    private $length;

    /**
     * Value type of the items at container.
     * @var string
     */
    private $typeOfList;

    /**
     * If list is type restricted or not.
     * @var bool
     */
    private $isValueTypeRestricted;

    /**
     * Option for casting values.
     * @var bool
     */
    private $isCastAllowed;


    public function __construct(string $type = '*', $castAllowed = true){
        $this->clear();

        if(!$this->isValidType($type))
            throw new \InvalidArgumentException("Cannot provide \"{$type}\" as type");

        $this->typeOfList = $type;

        // Define if List should be value type mixed or not.
        $this->isValueTypeRestricted = ($type != '*');
        $this->isCastAllowed = $castAllowed;
    }

    /**
     * Fixes the index numbers of the items.
     * This is useful when removing an offset at middle.
     */
    private function rebaseIndexes() {
        $this->container = array_values($this->container);
    }

    private function convertToBool($value) : bool {
        /*
        Currently converting strings as default.

        'false' will return true.

         */
        return (bool)$value;
    }

    private function convertToInt($value) : int {
        if (gettype($value) == 'array') {
            throw new \Exception("Cannot convert type 'array' to 'int'");
        }

        return (int)$value;
    }

    private function convertToDouble($value) : float {
        if(gettype($value) == 'boolean') {
            throw new \Exception("Cannot convert type 'boolean' to 'double'");
        }
        return (float)$value;
    }

    private function convertToString($value) : string {
        return (string)$value;
    }

    /**
     * [attemptCast description]
     * @param  [type] $value [description]
     * @throws \InvalidArgumentException
     * @return [type]        [description]
     */
    private function attemptCast($value) {
        switch($this->typeOfList) {
            case 'boolean':
                $value = $this->convertToBool($value);
                break;
            case 'integer':
                $value = $this->convertToInt($value);
                break;
            case 'double':
                $value = $this->convertToDouble($value);
                break;
            case 'string':
                $value = $this->convertToString($value);
                break;
            default:
                throw new \InvalidArgumentException("Cannot cast this value to \"{$this->typeOfList}\"");
        }

        return $value;
    }

    /**
     * Checks weather the provided type is php valid.
     * @param  string $type
     * @return bool
     */
    protected function isValidType(string $type) : bool {

        $isValidType = null;

        switch($type) {
            case '*':
            case 'boolean':
            case 'integer':
            case 'double':
            case 'string':
            case 'array':
            case 'object':
            case 'resource':
                $isValidType = true;
                break;
            default:
                $isValidType = false;
                break;
        }

        return $isValidType;
    }

    /**
     * Clears the ArrayList
     */
    public function clear(){
        $this->container = array();
        $this->length = 0;
    }

    public function getLength() : int {
        return $this->length;
    }

    /**
     * Adds a new value
     * @param  mixed    $value
     * @throws \Exception
     * @return ArrayList
     */
    public function add($value) : ArrayList {

        if($this->isValueTypeRestricted){
            if($this->typeOfList != gettype($value)){

                if(!$this->isCastAllowed){
                    throw new \Exception("Invalid value provided. Cannot add type of \"" . gettype($value) . "\" to a ArrayList of type \"{$this->typeOfList}\". NOTE: Type Casting is turned off.");
                }

                $value = $this->attemptCast($value);
            }
        }

        array_push($this->container, $value);
        $this->length++;

        return $this;
    }

    /**
     * Attempts to retrieve the requested offset.
     * @param  int    $offset [description]
     * @throws \OutOfBoundsException
     * @return mixed
     */
    public function get($offset){

        if(!is_int($offset)){
            throw new \InvalidArgumentException('Argument "$offset" must be of type an integer.');
        }

        if(!isset($this->container[$offset])){
            throw new \OutOfBoundsException();
        }

        return $this->container[$offset];
    }

    /**
     * Attempts to remove the requested offset.
     * @param  int    $offset
     * @throws \OutOfBoundsException
     */
    public function remove($offset) : bool {

        if(!is_int($offset)){
            throw new \InvalidArgumentException('Argument "$offset" must be of type an integer.');
        }

        if(!($offset >= 0 && $offset < $this->length)){
            throw new \OutOfBoundsException();
        }

        unset($this->container[$offset]);

        $this->length--;
        $this->rebaseIndexes();
        return true;
    }

    /**
     * Removes the first index item.
     */
    public function removeFirst(){

        if($this->length > 0){
            $value = array_shift($this->container);
            $this->length--;
        }

        //ksort($this->container);
        return $value;
    }

    /**
     * Removes the last index item.
     */
    public function removeLast() {

        if($this->length > 0){
            $value = array_pop($this->container);
            $this->length--;
        }

        //ksort($this->container);
        return $value;
    }

    /**
     * Removes index item by value.
     * @param  [type] $value [description]
     * @return bool          [description]
     */
    public function removeByValue($value) : bool {
        $offset = $this->search($value);

        if($offset === false){
            throw new \Exception('Cant remove unexisting value');
        }

        return $this->remove($offset);
    }

    /**
     * Provides a filtered array by use of a closure.
     * @param  callable $callback     [description]
     * @param  boolean  $applyChanges [description]
     * @return array                  [description]
     */
    public function filter(callable $callback, bool $applyChanges = false) : array {

        $filteredArray = array_filter($this->container, $callback);

        if($applyChanges){
            $this->container = $filteredArray;
        }

        return $filteredArray;

    }

    /**
     * Maps the list items with provided closure.
     * @param  callable $callback     [description]
     * @param  boolean  $applyChanges [description]
     * @return array                  [description]
     */
    public function map(callable $callback, bool $applyChanges = false) : array {

        $result = array_map($callback, $this->container);

        if($applyChanges){
            $this->container = $result;
        }

        return $result;
    }

    /**
     * Searches for value in container.
     * @param  [type] $needle [description]
     * @return int    Returns the offset of the searched value.
     */
    public function search($needle) {
        return array_search($needle, $this->container);
    }

    public function has($offset) {
        return $this->offsetExists($offset);
    }
    /**
     * [offsetExists description]
     * @param  [type] $offset [description]
     * @return bool           [description]
     */
    public function offsetExists ( $offset ) : bool {
        return ($offset >= 0 && $offset < $this->length);
    }

    /**
     * [offsetGet description]
     * @param [type] $offset [description]
     */
    public function offsetGet ( $offset ) {
        return $this->get($offset);
    }

    /**
     * [offsetSet description]
     * @param [type] $offset [description]
     * @param [type] $value  [description]
     */
    public function offsetSet ( $offset , $value ) {
        throw new \NotImplementedException();
    }

    /**
     * [offsetUnset description]
     * @param [type] $offset [description]
     */
    public function offsetUnset ( $offset ) {
        $this->remove($offset);
    }

    /**
    * Allows this to be iteratable.
    * @return ArrayIterator
    */
    public function getIterator() : \ArrayIterator {
        return new \ArrayIterator($this->container);
    }

}

?>
