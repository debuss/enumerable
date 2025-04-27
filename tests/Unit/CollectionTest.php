<?php

use Collection\Collection;
use Tests\Entity\Person;

covers(Collection::class);

beforeEach(function () {
    $this->collection = new Collection([1, 2, 3]);
});

it('throws InvalidArgumentException on unconsistent items', function () {
    new Collection([1, 2, '3']);
})->throws(InvalidArgumentException::class, 'Collection items must be of the same type. Expected "integer", got "string" at index 2');

test('it can be created from an array', function () {
    $collection = Collection::fromArray([1, 2, 3]);
    expect($collection->count())->toBe(3)
        ->and($collection->contains(1))->toBeTrue()
        ->and($collection->contains(2))->toBeTrue()
        ->and($collection->contains(3))->toBeTrue()
        ->and($collection->contains(4))->toBeFalse();
});

test('it can be created from a JSON string', function () {
    $collection = Collection::fromJson('[1, 2, 3]');
    expect($collection->count())->toBe(3)
        ->and($collection->contains(1))->toBeTrue()
        ->and($collection->contains(2))->toBeTrue()
        ->and($collection->contains(3))->toBeTrue()
        ->and($collection->contains(4))->toBeFalse();
});

test('it can be created from a range', function () {
    $collection = Collection::fromRange(1, 5);
    expect($collection->count())->toBe(5)
        ->and($collection->contains(1))->toBeTrue()
        ->and($collection->contains(2))->toBeTrue()
        ->and($collection->contains(3))->toBeTrue()
        ->and($collection->contains(4))->toBeTrue()
        ->and($collection->contains(5))->toBeTrue()
        ->and($collection->contains(6))->toBeFalse();
});

test('it can be created from an empty collection', function () {
    $collection = Collection::empty();
    expect($collection->count())->toBe(0);
});

test('it can be created from a repeated element', function () {
    $collection = Collection::repeat('Hello, World!', 5);
    expect($collection->count())->toBe(5)
        ->and($collection->contains('Hello, World!'))->toBeTrue()
        ->and($collection->any(fn ($item) => $item != 'Hello, World!'))->toBeFalse();
});

test('it can return iterator', function () {
    expect($this->collection->getIterator())->toBeInstanceOf(ArrayIterator::class)
        ->and($this->collection->getIterator()->count())->toBe(3);
});

test('it can count the number of items', function () {
    expect($this->collection->count())->toBe(3);
});

test('all() is an integer', function () {
    expect($this->collection->all(fn ($item) => is_int($item)))->toBeTrue();
});

test('all() is not an integer', function () {
    expect($this->collection->all(fn ($item) => is_string($item)))->toBeFalse();
});

test('any() find same string', function () {
    expect(Collection::fromArray(['airplane', 'car'])->any(fn ($item) => $item == 'car'))->toBeTrue();
});

test('any() does not find same string', function () {
    expect(Collection::fromArray(['airplane', 'car'])->any(fn ($item) => $item == 'train'))->toBeFalse();
});

test('average() throws exception on empty collection', function () {
    $collection = Collection::empty();
    $collection->average();
})->throws(RuntimeException::class, 'Cannot calculate average of an empty collection');

test('average() throws exception on non numeric collection', function () {
    $collection = Collection::fromArray(['foo', 'bar']);
    $collection->average();
})->throws(RuntimeException::class, 'Cannot calculate average of non-numeric values');

test('average() does average the collection', function () {
    expect($this->collection->average())->toBe(2.0);
});

test('append() set the item at the end', function () {
    $this->collection = $this->collection->append(4);
    expect($this->collection->count())->toBe(4)
        ->and($this->collection->contains(4))->toBeTrue()
        ->and($this->collection->last())->toBe(4);
});

test('prepend() set the item at the beginning', function () {
    $this->collection = $this->collection->prepend(0);
    expect($this->collection->count())->toBe(4)
        ->and($this->collection->contains(0))->toBeTrue()
        ->and($this->collection->first())->toBe(0);
});

test('chunk() splits the collection into chunks', function () {
    $chunks = $this->collection->chunk(2);
    expect($chunks->count())->toBe(2)
        ->and($chunks->first()->count())->toBe(2)
        ->and($chunks->last()->count())->toBe(1);
});

test('concat() merge with another collection', function () {
    $this->collection = $this->collection->concat(Collection::fromArray([4, 5]));
    expect($this->collection->count())->toBe(5)
        ->and($this->collection->contains(4))->toBeTrue()
        ->and($this->collection->contains(5))->toBeTrue();
});

test('contains() returns true if the item is in the collection, false otherwise', function () {
    expect($this->collection->contains(1))->toBeTrue()
        ->and($this->collection->contains(4))->toBeFalse();
});

test('contains() with a callable', function () {
    expect($this->collection->contains(fn ($item) => $item === 2))->toBeTrue()
        ->and($this->collection->contains(fn ($item) => $item === 4))->toBeFalse();
});

test('countBy() counts the number of items that match the condition', function () {
    $count = $this->collection->countBy(fn ($item) => $item > 1);
    expect($count)->toBe(2);
});

test('distinct() removes duplicated items', function () {
    $this->collection = $this->collection->concat(Collection::fromArray([1, 2, 3, 4, 5, 5]));
    expect($this->collection->distinct()->count())->toBe(5)
        ->and($this->collection->distinct()->contains(5))->toBeTrue()
        ->and($this->collection->distinct()->contains(6))->toBeFalse();
});

test('distinctBy() with a callable', function () {
    $this->collection = $this->collection->concat(Collection::fromArray([1, 2, 3, 4, 5, 5]));
    expect($this->collection->distinctBy(fn ($item) => $item % 2)->count())->toBe(2)
        ->and($this->collection->distinctBy(fn ($item) => $item % 2)->contains(1))->toBeTrue()
        ->and($this->collection->distinctBy(fn ($item) => $item % 2)->contains(2))->toBeTrue()
        ->and($this->collection->distinctBy(fn ($item) => $item % 2)->contains(3))->toBeFalse()
        ->and($this->collection->distinctBy(fn ($item) => $item % 2)->contains(4))->toBeFalse()
        ->and($this->collection->distinctBy(fn ($item) => $item % 2)->contains(5))->toBeFalse();
});

test('itemAt() throws OutOfBoundException', function () {
    $this->collection->itemAt(5);
})->throws(OutOfBoundsException::class, 'Index 5 is out of bounds for collection of size 3');

test('itemAt() return item', function () {
    expect($this->collection->itemAt(0))->toBe(1)
        ->and($this->collection->itemAt(1))->toBe(2)
        ->and($this->collection->itemAt(2))->toBe(3);
});

test('itemAtOrDefault() returns null', function () {
    expect($this->collection->itemAtOrDefault(5))->toBeNull();
});

test('itemAtOrDefault() returns provided default value', function () {
    expect($this->collection->itemAtOrDefault(5, 'test'))->toBe('test');
});

test('except() returns Collection without element from provided array', function () {
    $other = Collection::fromArray([1, 2]);
    expect($this->collection->except($other)->count())->toBe(1)
        ->and($this->collection->except($other)->contains(1))->toBeFalse()
        ->and($this->collection->except($other)->contains(2))->toBeFalse()
        ->and($this->collection->except($other)->contains(3))->toBeTrue();
});

test('except() returns Collection without element from provided Collection', function () {
    $other = Collection::fromArray([1, 2]);
    expect($this->collection->except($other)->count())->toBe(1)
        ->and($this->collection->except($other)->contains(1))->toBeFalse()
        ->and($this->collection->except($other)->contains(2))->toBeFalse()
        ->and($this->collection->except($other)->contains(3))->toBeTrue();
});

test('first() throws RuntimeException on empty collection', function () {
    $this->collection = Collection::empty();
    $this->collection->first();
})->throws(RuntimeException::class, 'Collection is empty');

test('first() returns first item', function () {
    expect($this->collection->first())->toBe(1);
});

test('firstOrDefault() returns first item', function () {
    expect($this->collection->firstOrDefault())->toBe(1);
});

test('firstOrDefault() returns default', function () {
    $this->collection = Collection::empty();
    expect($this->collection->firstOrDefault())->toBeNull();
});

test('firstOrDefault() returns default provided value', function () {
    $this->collection = Collection::empty();
    expect($this->collection->firstOrDefault('test'))->toBe('test');
});

test('groupBy() group items by provided callable', function () {
    $this->collection = $this->collection->concat(Collection::fromArray([1, 2, 3, 4, 5, 5]));
    $grouped = $this->collection->groupBy(fn ($item) => $item % 2);
    expect($grouped->count())->toBe(2)
        ->and($grouped->first()->count())->toBe(6)
        ->and($grouped->last()->count())->toBe(3);
});

test('intersect() compute the intersection of other array', function () {
    $this->collection = $this->collection->concat(Collection::fromArray([1, 2, 3, 4, 5, 5]));
    $intersected = $this->collection->intersect(Collection::fromArray([1, 2]));
    expect($intersected->count())->toBe(4)
        ->and($intersected->contains(1))->toBeTrue()
        ->and($intersected->contains(2))->toBeTrue()
        ->and($intersected->contains(3))->toBeFalse();
});

test('intersect() compute the intersection of other Collection', function () {
    $this->collection = $this->collection->concat(Collection::fromArray([1, 2, 3, 4, 5, 5]));
    $intersected = $this->collection->intersect(Collection::fromArray([1, 2]));
    expect($intersected->count())->toBe(4)
        ->and($intersected->contains(1))->toBeTrue()
        ->and($intersected->contains(2))->toBeTrue()
        ->and($intersected->contains(3))->toBeFalse();
});

test('last() throws RuntimeException on empty collection', function () {
    $this->collection = Collection::empty();
    $this->collection->last();
})->throws(RuntimeException::class, 'Collection is empty');

test('last() returns last item', function () {
    expect($this->collection->last())->toBe(3);
});

test('lastOrDefault() returns last item', function () {
    expect($this->collection->lastOrDefault())->toBe(3);
});

test('lastOrDefault() returns default', function () {
    $this->collection = Collection::empty();
    expect($this->collection->lastOrDefault())->toBeNull();
});

test('lastOrDefault() returns default provided value', function () {
    $this->collection = Collection::empty();
    expect($this->collection->lastOrDefault('test'))->toBe('test');
});

test('max() throws exception on empty collection', function () {
    $collection = Collection::empty();
    $collection->max();
})->throws(RuntimeException::class, 'Cannot calculate max of an empty collection');

test('max() throws exception on non numeric collection', function () {
    $collection = Collection::fromArray(['foo', 'bar']);
    $collection->max();
})->throws(RuntimeException::class, 'Cannot calculate max of non-numeric values');

test('max() does max the collection', function () {
    expect($this->collection->max())->toBe(3);
});

test('min() throws exception on empty collection', function () {
    $collection = Collection::empty();
    $collection->min();
})->throws(RuntimeException::class, 'Cannot calculate min of an empty collection');

test('min() throws exception on non numeric collection', function () {
    $collection = Collection::fromArray(['foo', 'bar']);
    $collection->min();
})->throws(RuntimeException::class, 'Cannot calculate min of non-numeric values');

test('min() does min the collection', function () {
    expect($this->collection->min())->toBe(1);
});

test('sum() throws exception on empty collection', function () {
    $collection = Collection::empty();
    $collection->sum();
})->throws(RuntimeException::class, 'Cannot calculate sum of an empty collection');

test('sum() throws exception on non numeric collection', function () {
    $collection = Collection::fromArray(['foo', 'bar']);
    $collection->sum();
})->throws(RuntimeException::class, 'Cannot calculate sum of non-numeric values');

test('sum() does sum the collection', function () {
    expect($this->collection->sum())->toBe(6);
});

test('select() mps each item in the collection to a new value using a given function', function () {
    $mapped = $this->collection->select(fn ($item) => $item * 2);
    expect($mapped->count())->toBe(3)
        ->and($mapped->contains(2))->toBeTrue()
        ->and($mapped->contains(4))->toBeTrue()
        ->and($mapped->contains(6))->toBeTrue()
        ->and($mapped->contains(1))->toBeFalse();
});

test('single() throws RuntimeException when no item found', function () {
    $this->collection->single(fn ($item) => $item > 3);
})->throws(RuntimeException::class, 'No item found that matches the condition');

test('single() throws RuntimeException when more than 1 item found', function () {
    $this->collection->single(fn ($item) => $item > 1);
})->throws(RuntimeException::class, 'Expected exactly one item, found 2');

test('single() returns the single searched value', function () {
    expect($this->collection->single(fn ($item) => $item == 2))->toBe(2);
});

test('singleOrDefault() throws RuntimeException when more than 1 item found', function () {
    $this->collection->singleOrDefault(fn ($item) => $item > 1);
})->throws(RuntimeException::class, 'Expected exactly one item, found 2');

test('singleOrDefault() returns the single searched value', function () {
    expect($this->collection->singleOrDefault(fn ($item) => $item == 2))->toBe(2);
});

test('singleOrDefault() returns the default value', function () {
    expect($this->collection->singleOrDefault(fn ($item) => $item == 5, 42))->toBe(42);
});

test('skip() removes first items', function () {
    $this->collection = $this->collection->skip(2);
    expect($this->collection->count())->toBe(1)
        ->and($this->collection->contains(1))->toBeFalse()
        ->and($this->collection->contains(2))->toBeFalse()
        ->and($this->collection->contains(3))->toBeTrue();
});

test('skipLast() removes last items', function () {
    $this->collection = $this->collection->skipLast(2);
    expect($this->collection->count())->toBe(1)
        ->and($this->collection->contains(1))->toBeTrue()
        ->and($this->collection->contains(2))->toBeFalse()
        ->and($this->collection->contains(3))->toBeFalse();
});

test('skipWhile() removes items until I say so!', function () {
    $this->collection = $this->collection->skipWhile(fn ($item) => $item < 2);
    expect($this->collection->count())->toBe(2)
        ->and($this->collection->contains(1))->toBeFalse()
        ->and($this->collection->contains(2))->toBeTrue()
        ->and($this->collection->contains(3))->toBeTrue();
});

test('take() keep first items', function () {
    $this->collection = $this->collection->take(2);
    expect($this->collection->count())->toBe(2)
        ->and($this->collection->contains(1))->toBeTrue()
        ->and($this->collection->contains(2))->toBeTrue()
        ->and($this->collection->contains(3))->toBeFalse();
});

test('takeLast() keep last items', function () {
    $this->collection = $this->collection->takeLast(2);
    expect($this->collection->count())->toBe(2)
        ->and($this->collection->contains(1))->toBeFalse()
        ->and($this->collection->contains(2))->toBeTrue()
        ->and($this->collection->contains(3))->toBeTrue();
});

test('takeWhile() keep items until I say so!', function () {
    $this->collection = $this->collection->takeWhile(fn ($item) => $item < 2);
    expect($this->collection->count())->toBe(1)
        ->and($this->collection->contains(1))->toBeTrue()
        ->and($this->collection->contains(2))->toBeFalse()
        ->and($this->collection->contains(3))->toBeFalse();
});

test('union() throws InvalidArgumentException if 2 collections are of different type', function () {
    $other = Collection::fromArray(['a', 'b', 'c']);
    $this->collection->union($other);
})->throws(InvalidArgumentException::class, 'Collection items must be of the same type. Expected "integer", got "string" at index 3');

test('union() produces the union of 2 collection', function () {
    $other = Collection::fromArray([1, 2, 3, 4, 5]);
    $union = $this->collection->union($other);
    expect($union->count())->toBe(5)
        ->and($union->contains(1))->toBeTrue()
        ->and($union->contains(2))->toBeTrue()
        ->and($union->contains(3))->toBeTrue()
        ->and($union->contains(4))->toBeTrue()
        ->and($union->contains(5))->toBeTrue();
});

test('where() returns the desired items', function () {
    $this->collection = $this->collection->where(fn ($item) => $item > 1);
    expect($this->collection->count())->toBe(2)
        ->and($this->collection->contains(1))->toBeFalse()
        ->and($this->collection->contains(2))->toBeTrue()
        ->and($this->collection->contains(3))->toBeTrue();
});

test('zip() combines 2 collections of same size', function () {
    $other = Collection::fromArray(['a', 'b', 'c']);
    $zipped = $this->collection->zip($other,  fn ($item1, $item2) => $item1.' '.$item2);
    expect($zipped->count())->toBe(3)
        ->and($zipped->first())->toBe('1 a')
        ->and($zipped->last())->toBe('3 c');
});

test('zip() combines 2 collections of different size', function () {
    $other = Collection::fromArray(['a', 'b', 'c', 'd', 'e']);
    $zipped = $this->collection->zip($other,  fn ($item1, $item2) => $item1.' '.$item2);
    expect($zipped->count())->toBe(3)
        ->and($zipped->first())->toBe('1 a')
        ->and($zipped->last())->toBe('3 c');
});

test('toArray() returns the array of items', function () {
    expect($this->collection->toArray())->toBe([1, 2, 3]);
});

test('order() returns a collection sorted ASC', function () {
    $collection = Collection::fromArray([3, 2, 1]);
    $sorted = $collection->order();
    expect($sorted->count())->toBe(3)
        ->and($sorted->first())->toBe(1)
        ->and($sorted->last())->toBe(3);
});

test('orderBy() returns a collection ordered by callable (ASC)', function () {
    $collection = Collection::fromArray([
        new Person('Bob', 30),
        new Person('Alice', 20),
        new Person('Charlie', 25)
    ]);
    $sorted = $collection->orderBy(fn (Person $item) => $item->age);
    expect($sorted->count())->toBe(3)
        ->and($sorted->first()->name)->toBe('Alice')
        ->and($sorted->last()->name)->toBe('Bob');
});

test('orderDescending() returns a collection sorted DESC', function () {
    $sorted = $this->collection->orderDescending();
    expect($sorted->count())->toBe(3)
        ->and($sorted->first())->toBe(3)
        ->and($sorted->last())->toBe(1);
});

test('orderDescendingBy() returns a collection ordered by callable (ASC)', function () {
    $collection = Collection::fromArray([
        new Person('Alice', 20),
        new Person('Bob', 30),
        new Person('Charlie', 25)
    ]);
    $sorted = $collection->orderDescendingBy(fn (Person $item) => $item->age);
    expect($sorted->count())->toBe(3)
        ->and($sorted->first()->name)->toBe('Bob')
        ->and($sorted->last()->name)->toBe('Alice');
});

test('equal() returns true on identical collection', function () {
    $collection = Collection::fromArray([1, 2, 3]);
    expect($this->collection->equal($collection))->toBeTrue();
});

test('equal() return false on different collection', function () {
    $collection = Collection::fromArray(['a', 'b', 'c']);
    expect($this->collection->equal($collection))->toBeFalse();
});

test('equal() returns false on different size collection', function () {
    $collection = Collection::fromArray([1, 2]);
    expect($this->collection->equal($collection))->toBeFalse();
});

test('equalBy() return true on identical collection', function () {
    $collection = Collection::fromArray([1.0, 2.0, 3.0]);
    expect(
        $this->collection->equalBy($collection, fn ($first, $second) => (float)$first == (float)$second)
    )->toBeTrue();
});

test('equalBy() return true on identical collection 2', function () {
    $collection = Collection::fromArray(['1', '2', '3']);
    expect(
        $this->collection->equalBy($collection, fn ($first, $second) => (int)$first == (int)$second)
    )->toBeTrue();
});

test('equalBy() returns false on different collection', function () {
    $collection = Collection::fromArray(['a', 'b', 'c']);
    expect(
        $this->collection->equalBy($collection, fn ($first, $second) => $first == $second)
    )->toBeFalse();
});

test('equalBy() return false on different size collection', function () {
    $collection = Collection::fromArray([1, 2]);
    expect(
        $this->collection->equalBy($collection, fn ($first, $second) => (int)$first == (int)$second)
    )->toBeFalse();
});
