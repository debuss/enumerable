<?php

namespace Collection\Interface;

use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use RuntimeException;

/**
 * Interface Enumerable
 *
 * Provides various methods for manipulating and querying enumerable of items.
 *
 * @template TItem
 * @extends IteratorAggregate<int, TItem>
 */
interface Enumerable extends Countable, IteratorAggregate
{

    /**
     * Creates an empty instance of the Enumerable.
     *
     * @return Enumerable<TItem>
     */
    public static function empty(): Enumerable;

    /**
     * Creates an instance of the class with $element repeated $count time.
     *
     * @return Enumerable<TItem>
     */
    public static function repeat(mixed $element, int $count): Enumerable;

    /**
     * Creates an instance of the class from an array.
     *
     * @param array<TItem> $array
     * @return Enumerable<TItem>
     */
    public static function fromArray(array $array): Enumerable;

    /**
     * Creates an instance of the class from a JSON string.
     *
     * @return Enumerable<TItem>
     */
    public static function fromJson(string $json): Enumerable;

    /**
     * Creates an instance of the class from a range of numbers.
     *
     * @return Enumerable<int>
     */
    public static function fromRange(int $from, int $to, int $step = 1): Enumerable;

    /**
     * Determine whether 2 Enumerables are equal.
     *
     * @param Enumerable<TItem> $second
     */
    public function equal(Enumerable $second): bool;

    /**
     * Determine whether 2 Enumerables are equal by a given condition.
     *
     * @param Enumerable<TItem> $second
     * @param callable $callable A function to test each item for a condition (must return true/false).
     */
    public function equalBy(Enumerable $second, callable $callable): bool;

    /**
     * Checks if all elements in the enumerable satisfy a given condition.
     *
     * @param callable $callable A function to test each item for a condition (must return true/false).
     */
    public function all(callable $callable): bool;

    /**
     * Checks if any element in the enumerable satisfies a given condition.
     *
     * @param callable $callable A function to test each item for a condition (must return true/false).
     */
    public function any(callable $callable): bool;

    /**
     * Calculates the average of the elements in the enumerable.
     *
     * @throws RuntimeException If the enumerable is empty or contains non-numeric values.
     */
    public function average(): float;

    /**
     * Finds the maximum value in the enumerable.
     *
     * @throws RuntimeException If the enumerable is empty or contains non-numeric values.
     */
    public function max(): int|float;

    /**
     * Finds the minimum value in the enumerable.
     *
     * @throws RuntimeException If the enumerable is empty or contains non-numeric values.
     */
    public function min(): int|float;

    /**
     * Sorts the enumerable in ascending order.
     *
     * @return Enumerable<TItem> A new Enumerable with sorted items.
     */
    public function order(): Enumerable;

    /**
     * Sorts the enumerable in ascending order using a custom comparison function.
     *
     * @return Enumerable<TItem> A new Enumerable with sorted items.
     */
    public function orderBy(callable $callable): Enumerable;

    /**
     * Sorts the enumerable in descending order.
     *
     * @return Enumerable<TItem> A new Enumerable with sorted items.
     */
    public function orderDescending(): Enumerable;

    /**
     * Sorts the enumerable in descending order using a custom comparison function.
     *
     * @return Enumerable<TItem> A new Enumerable with sorted items.
     */
    public function orderDescendingBy(callable $callable): Enumerable;

    /**
     * Reverses the order of the items in the enumerable.
     *
     * @return Enumerable<TItem> A new Enumerable with reversed items.
     */
    public function reverse(): Enumerable;

    /**
     * Calculates the sum of the elements in the enumerable.
     *
     * @throws RuntimeException If the enumerable is empty or contains non-numeric values.
     */
    public function sum(): int|float;

    /**
     * Appends an item to the enumerable.
     *
     * @param TItem $item The item to append.
     * @return Enumerable<TItem> A new Enumerable that ends with $item.
     */
    public function append(mixed $item): Enumerable;

    /**
     * Prepends an item to the enumerable.
     *
     * @return Enumerable<TItem> A new Enumerable that ends with $item.
     */
    public function prepend(mixed $item): Enumerable;

    /**
     * Splits the elements of an Enumerable into chunks of size at most $length.
     *
     * @return Enumerable<TItem> A new Enumerable with the items in chunks.
     */
    public function chunk(int $length): Enumerable;

    /**
     * Concatenates two Enumerables.
     *
     * @param Enumerable<TItem> $items The Enumerable to concatenate with.
     * @return Enumerable<TItem> A new Enumerable with the items concatenated.
     */
    public function concat(Enumerable $items): Enumerable;

    /**
     * Checks if the enumerable contains a specific item.
     *
     * @param TItem $item The item to check for or a callable to test each item for a condition (must return true/false).
     */
    public function contains(mixed $item): bool;

    /**
     * Counts the number of items in the enumerable according to a given condition.
     *
     * @param callable $callable A function to test each item for a condition (must return true/false).
     */
    public function countBy(callable $callable): int;

    /**
     * @return Enumerable<TItem> A new Enumerable with distinct items.
     */
    public function distinct(): Enumerable;

    /**
     * Filters the enumerable based on a given condition.
     *
     * @param callable $callable A function to test each item for a condition (must return a string representing the
     *                           item "key", keys will be used to check uniqueness of items).
     * @return Enumerable<TItem> A new Enumerable with filtered items.
     */
    public function distinctBy(callable $callable): Enumerable;

    /**
     * Get the item at a specified index in an Enumerable.
     *
     * @return TItem
     * @throws RuntimeException If the index is out of bounds.
     */
    public function itemAt(int $index): mixed;

    /**
     * Get the item at a specified index in an Enumerable or a default value.
     *
     * @return TItem
     */
    public function itemAtOrDefault(int $index, mixed $default = null): mixed;

    /**
     * Produces the difference of two Enumerable.
     *
     * The difference of two Enumerable is defined as the items of the first enumerable that don't appear in the second
     * enumerable.
     *
     * It doesn't return those items in $items that don't appear in the first enumerable.
     * Only unique elements are returned.
     *
     * @param Enumerable<TItem> $items
     * @return Enumerable<TItem>
     */
    public function except(Enumerable $items): Enumerable;

    /**
     * Returns the first item of an Enumerable.
     *
     * @return TItem
     * @throws RuntimeException If the enumerable is empty.
     */
    public function first(): mixed;

    /**
     * Returns the first item of n Enumerable or a default value.
     *
     * @param mixed $default The default value to return if the enumerable is empty.
     * @return TItem
     */
    public function firstOrDefault(mixed $default = null): mixed;

    /**
     * Group the items of n Enumerable by a given key, calculated by $callable.
     *
     * @param callable $callable
     * @return Enumerable<covariant Enumerable<TItem>>
     */
    public function groupBy(callable $callable): Enumerable;

    /**
     * Returns the intersection of two Enumerables.
     *
     * The intersection of two Enumerables is defined as the items that appear in both Enumerables.
     *
     * @param Enumerable<TItem> $items
     * @return Enumerable<TItem>
     */
    public function intersect(Enumerable $items): Enumerable;

    /**
     * Returns the last item of n Enumerable.
     *
     * @throws RuntimeException If the enumerable is empty.
     * @return TItem
     */
    public function last(): mixed;

    /**
     * Returns the last item of n Enumerable or a default value.
     *
     * @param mixed $default The default value to return if the enumerable is empty.
     * @return TItem
     */
    public function lastOrDefault(mixed $default = null): mixed;

    /**
     * Maps each item in the enumerable to a new value using a given function.
     *
     * @param callable $callable(TItem $item, int $index) A function to transform each item.
     * @return Enumerable<TItem> A new Enumerable with the transformed items.
     */
    public function select(callable $callable): Enumerable;

    /**
     * Returns the only item of a enumerable that satisfies a specified condition.
     *
     * Throws a RuntimeException if more than one such item exists or if no such item exists.
     *
     * @param callable $callable(TItem $item, int $index) A function to filter the single item.
     * @return TItem The single item that satisfies the condition.
     */
    public function single(callable $callable): mixed;


    /**
     * Returns the only item of an Enumerable that satisfies a specified condition, or a default value.
     *
     * Throws a RuntimeException if more than one such item exists.
     *
     * @param callable $callable(TItem $item, int $index) A function to filter the single item.
     * @param TItem $default The default value to return if no such item exists.
     * @return TItem The single item that satisfies the condition, or a default value.
     */
    public function singleOrDefault(callable $callable, mixed $default = null): mixed;

    /**
     * Skips the first $count items of the enumerable and returns the rest.
     *
     * @param int $count The number of items to skip.
     * @return Enumerable<TItem> A new Enumerable with the remaining items.
     */
    public function skip(int $count): Enumerable;

    /**
     * Skips the last $count items of the enumerable and returns the rest.
     *
     * @param int $count The number of items to skip.
     * @return Enumerable<TItem> A new Enumerable with the remaining items.
     */
    public function skipLast(int $count): Enumerable;

    /**
     * Skips items in the enumerable while a condition is true and returns the rest.
     *
     * @param callable $callable A function to test each item for a condition (must return true/false).
     * @return Enumerable<TItem> A new Enumerable with the remaining items.
     */
    public function skipWhile(callable $callable): Enumerable;

    /**
     * Takes the first $count items of the enumerable and skips the rest.
     *
     * @param int $count The number of items to skip.
     * @return Enumerable<TItem> A new Enumerable with taken items.
     */
    public function take(int $count): Enumerable;

    /**
     * Takes the last $count items of the enumerable and skips the rest.
     *
     * @param int $count The number of items to skip.
     * @return Enumerable<TItem> A new Enumerable with taken items.
     */
    public function takeLast(int $count): Enumerable;

    /**
     * Takes items in the enumerable while a condition is true and skips the rest.
     *
     * @param callable $callable A function to test each item for a condition (must return true/false).
     * @return Enumerable<TItem> A new Enumerable with taken items.
     */
    public function takeWhile(callable $callable): Enumerable;

    /**
     * Produces the union of two Enumerables.
     *
     * The union of two Enumerables is defined as the items that appear in either enumerable, without duplicates.
     *
     * @param Enumerable<TItem> $second
     * @return Enumerable<TItem>
     * @throws InvalidArgumentException If the Enumerables are not of the same type.
     */
    public function union(Enumerable $second): Enumerable;

    /**
     * Filters an Enumerable of values based on a callable.
     *
     * @return Enumerable<TItem> A new Enumerable with filtered items.
     */
    public function where(callable $callable): Enumerable;

    /**
     * Applies a specified function to the corresponding items of two Enumerables, producing a new enumerable of the
     * results.
     *
     * @param Enumerable<TItem> $second
     * @param callable $callable
     * @return Enumerable<TItem> A new Enumerable with the items zipped.
     */
    public function zip(Enumerable $second, callable $callable): Enumerable;

    /**
     * Converts the enumerable to an array.
     *
     * @return array<TItem>
     */
    public function toArray(): array;
}
