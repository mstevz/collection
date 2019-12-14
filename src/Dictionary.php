<?php

namespace mstevz\collection;

/**
 * Allows the creation of a dictionary giving an easier data manipulation.
 * @author Miguel Esteves <mstevz@mail.com>
 * @license https://github.com/mstevz/collection/blob/master/LICENSE
 */
class Dictionary extends \ArrayObject implements \JsonSerializable {

    /**
    * Property that contains the index position of each associative key.
    * @var array
    */
    private $containerKeys;

    /**
    * Property that contains the collection values, accessible by the associative key.
    * @var array
    */
    private $container;

    /**
    * Property that represents the number of existing items.
    * @var int
    */
    private $count;

    # Constructor
    public function __construct(array $array = []){
        $this->clear();
        $this->fromArray($array);
    }

    /**
    * Stores a new value in container and indexes its associative key.
    * @param string $key
    */
    private function index(string $key, $value){
        $this->container[$key] = $value;
        $this->containerKeys[$this->count++] = $key;
    }

    /**
     * Sets all values to default.
     */
    public function clear() : Collection {
        $this->count = 0;
        $this->container = [];
        $this->containerKeys = [];
        return $this;
    }

    /**
    * Returns the size of the collection.
    * @return int
    */
    public function count() : int {
        return $this->count;
    }

    /**
    * Add's a new value to the collection.
    * @param string $key
    * @param object $value
    * @throws \InvalidArgumentException
    * @throws \UnexpectedValueException
    * @return Collection
    */
    public function add(string $key, $value) : Collection {
        if(is_numeric($key))
            throw new \InvalidArgumentException('Cannot add numeric value as associative key.');

        if($this->offsetExists($key))
            throw new \UnexpectedValueException("Cannot duplicate a key entry: \"{$key}\"");

        $this->index($key, $value);

        return $this;
    }

    /**
    * Removes an existing value.
    * @param string $key
    * @return Collection
    */
    public function remove(string $key) : Collection {
        unset($this->containerKeys[$this->indexOf($key)]);
        $this->containerKeys = array_values($this->containerKeys);
        unset($this->container[$key]);
        $this->count--;

        return $this;
    }

    /**
     * Updates a collection value or adds if doesnt exist.
     * @param  string|int $offset
     * @param  mixed $value
     * @throws \OutOfRangeException
     */
    public function update($key, $value) : bool {
        $result = false;

        try{
            $this->add($key, $value);
            $result = true;
        }
        catch(\InvalidArgumentException $e){ // Tries to update when integer is given.
            $actualKey = $this->keyOf($key);

            if(is_null($actualKey)){
                throw new \OutOfRangeException('Cannot update invalid index.', 0, $e);
            }

            $result = $this->update($actualKey, $value);
        }
        catch(\UnexpectedValueException $e){
            $this->container[$key] = $value;
        }

        return $result;
    }

    /**
    * Searches for the given associative key or index.
    * @param string|int $offset
    * @throws \OutOfBoundsException
    * @return mixed
    */
    public function get($offset){
        if(!$this->offsetExists($offset)){
            throw new \OutOfBoundsException('Invalid offset.');
        }

        if(is_int($offset)){
            $offset = $this->containerKeys[$offset];
        }

        return $this->container[$offset];
    }

    /**
     * Returns entire collection as array.
     * @return array
     */
    public function getAll() : array {
        return $this->container;
    }

    /**
     * Returns all existing offsets name values.
     * @return array
     */
    public function getOffsets() : array {
        return $this->containerKeys;
    }

    /**
    * Returns the index of a given the associative key.
    * @param string $key
    * @return int|null Returns NULL if not found.
    */
    public function indexOf(string $key) : ?int {
        $index = array_search($key, $this->containerKeys);

        return ($index === false) ? null : $index;
    }

    /**
     * Returns the associative key of the given index.
     * @param  int
     * @return string|null Returns NULL if not found.
     */
    public function keyOf(int $index) : ?string {
        return $this->containerKeys[$index] ?? null;
    }

    /**
     * Returns if given offset exists or not.
     * @param  string|int $offset
     * @return bool
     */
    public function offsetExists($offset) : bool {
        $hasKeyIndex = is_int($offset) ? !is_null($this->keyOf($offset)) : false;

        return (isset($this->container[$offset]) || $hasKeyIndex);
    }

    /**
     * Behaviour for when operator [] is used to retrieve any value from the
     * collection.
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->get($offset);
    }

    /**
     * Behaviour for when operator [] is used in as new value allocation.
     * @param string|int $offset
     * @param mixed
     */
    public function offsetSet($offset, $value) {
        $this->update($offset, $value);
    }

    /**
     * Behaviour for when operator [] is used in unset() function.
     * @param string|int $offset
     */
    public function offsetUnset($offset) {
        $this->remove($offset);
    }

    /**
     * Iterates the collection and executes callable object with the current
     * value as argument.
     *
     * Returns any return value from the callable object.
     *
     * @param  callable $callback   Takes offset and value as parameter.
     * @return array                Returned values from callable object.
     */
    public function each(callable $callback) : array {
        $buffer = [];

        foreach($this->container as $offset => $value){
            array_push($buffer, $callback($offset, $value));
        }

        return $buffer;
    }

    /**
     * Search for the desire value with a true/false mapping.
     * @param  callable $callback   Takes offset and value as parameter.
     * @return mixed
     */
    public function find(callable $callback) {
        $result = null;

        foreach($this->container as $offset => $value){
            if($callback($offset, $value)){
                $result = $value;
                break;
            }
        }

        return $result;
    }

    /**
    * Returns the collection values as json string.
    *
    * @return string
    */
    public function toJson() : string {
        return json_encode($this->container);
    }

    /**
    * Fills the collection from json values.
    *
    * @param string $json
    * @param bool $override Choose to replace or add the data.
    * @throws \Exception
    **/
    public function fromJson(string $json) {
        $jsonArray = json_decode($json, true);

        if(json_last_error()){
            throw new \Exception('Could not convert a JSON object to a Collection object due to: \"' . json_last_error_msg() . '\"');
	}

        foreach($jsonArray as $offset => $value){
            $this->update($offset, $value);
        }
    }

    /**
     * Fills the collection from an array.
     * @param  array  $arr
     */
    public function fromArray(array $arr) : bool {

        foreach($arr as $offset => $value){
            $this->add($offset, $value);
        }

        return true;
    }

    /**
     * Converts object into serialized string.
     * @return string
     */
    public function serialize() : string {
       return serialize($this->container);
    }

    /**
     * Converts serialized string into new object.
     * @param  string $string
     */
    public function unserialize($serialized) {
       $this->container = unserialize($serialized);
    }

    /**
    * Allows this to be json serializable.
    * @return array
    */
    public function jsonSerialize() : array {
        return $this->container;
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
