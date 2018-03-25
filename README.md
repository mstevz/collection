# Collection
---

## <div align="center">_Description_</div>
Allows the creation of a collection giving an easier data manipulation.

---

## <div align="center">_LICENSE_</div>
This repository licensed under: [MIT License](https://github.com/mstevz/collection/blob/master/LICENSE).

If you are to use any content in this repository, please read the [license](https://github.com/mstevz/collection/blob/master/LICENSE) and let me know.
Thank you.

---

## <div align="center">_Requirements_</div>
1. Requires PHP Version: `^7.0.0`.

---

## <div align="center">_Bugs & Issues & Question & Suggestions_</div>
If you:
1. Experience any difficulty
2. Have any question
3. Have any suggestion

Please let me know by posting on: [Github Collection Issues](https://github.com/mstevz/collection/issues)

---

## <div align="center">How do I use it?</div>
The usage is very simple and straightfoward, lets see:

##### _Declaration_
```php
$friendsAge = new Collection();
```

##### _Adding_
```php
// By Array Operator.
$friendsAge["joey"] = 23;
$friendsAge["franklin"] = 28;
$friendsAge["andrew"] = 26;

// By Function. You can use chaining.
$friendsAge->add("joey", 23)
           ->add("franklin", 18)
           ->add("andrew", 26);

```

##### _Removing_
```php
$friendsAge->remove("joey")
           ->remove("andrew");

// or

unset($friendsAge["franklin"]);
```

##### _Searching_
```php
// By array operator
$friendsAge["franklin"];

// By key
$friendsAge->get("franklin");

// By index
$friendsAge->get(1);

// Need your key index?
$friendsAge->indexOf("franklin");

// Wanna make sure offset exists?
$friendsAge->offsetExists("franklin");

// Get all values as array.
$arr = $friendsAge->getAll();

// Get all offset values.
$offsets = $friendsAge->getOffsets();

// Get the number of elements in your collection.
$size = $friendsAge->count();
```

##### _Iterating_
```php
// Use a callback!
$myReturnedValuesArray = $friendsAge->each(function($key, $value){
    return "{$key} is {$value} old!";
});

// You can also use this way!
foreach($friedsAge as $friend => $age){
    echo "{$friend} is {$age} old!";
}
```

##### _Serialize_
```php
// Serialize
$value = $friendsAge->serialize();

// Unserialize
$friendsAge->unserialize($value);

// To json
$json = $friendsAge->toJson();

// From json
// Accepts second value as boolean to override existing values or add as new.
$friendsAge->fromJson($json, true);
```
---
