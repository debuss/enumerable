<?php

namespace Collection;

use ArrayIterator;
use Collection\Interface\Enumerable;
use InvalidArgumentException;
use OutOfBoundsException;
use RuntimeException;
use Traversable;

/**
 * Class Collection
 *
 * A collection class that implements the Enumerable interface.
 *
 * A collection is a data structure that holds a set of identical items.
 *
 * @template TItem
 * @implements Enumerable<TItem>
 * @package Collection
 */
final class Collection implements Enumerable
{

    /** @var array<TItem> */
    private array $items;

    /**
     * @param array<TItem> $items
     * @throws InvalidArgumentException
     */
    public function __construct(array $items = [])
    {
        if (count($items) > 0) {
            $this->validateTypeConsistency($items);
        }

        $this->items = array_values($items);
    }

    /**
     * @param array<TItem> $items
     * @throws InvalidArgumentException
     */
    private function validateTypeConsistency(array $items): void
    {
        $first = reset($items);
        $type = is_object($first) ? get_class($first) : gettype($first);

        foreach ($items as $index => $item) {
            $current_type = is_object($item) ? get_class($item) : gettype($item);
            if ($current_type !== $type) {
                throw new InvalidArgumentException(sprintf(
                    'Collection items must be of the same type. Expected "%s", got "%s" at index %d',
                    $type,
                    $current_type,
                    $index
                ));
            }
        }
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return Collection<TItem>
     */
    public static function empty(): Collection
    {
        return new Collection();
    }

    /**
     * @return Collection<TItem>
     */
    public static function repeat(mixed $element, int $count): Enumerable
    {
        return new Collection(array_fill(0, $count, $element));
    }

    /**
     * @return Collection<TItem>
     */
    public static function fromArray(array $array): Collection
    {
        return new Collection($array);
    }

    /**
     * @return Collection<TItem>
     */
    public static function fromJson(string $json): Collection
    {
        return new Collection(json_decode($json, true));
    }

    /**
     * @return Collection<int>
     */
    public static function fromRange(int $from, int $to, int $step = 1): Collection
    {
        return new Collection(range($from, $to, $step));
    }

    public function all(callable $callable): bool
    {
        foreach ($this->items as $item) {
            if (!$callable($item)) {
                return false;
            }
        }

        return true;
    }

    public function equal(Enumerable $second): bool
    {
        if ($this->count() !== $second->count()) {
            return false;
        }

        foreach ($this->items as $index => $item) {
            if ($item !== $second->itemAt($index)) {
                return false;
            }
        }

        return true;
    }

    public function equalBy(Enumerable $second, callable $callable): bool
    {
        if ($this->count() !== $second->count()) {
            return false;
        }

        foreach ($this->items as $index => $item) {
            if (!$callable($item, $second->itemAt($index))) {
                return false;
            }
        }

        return true;
    }

    public function any(callable $callable): bool
    {
        foreach ($this->items as $item) {
            if ($callable($item)) {
                return true;
            }
        }

        return false;
    }

    public function average(): float
    {
        if (!$this->count()) {
            throw new RuntimeException('Cannot calculate average of an empty collection');
        }

        if (!is_int($this->items[0]) && !is_float($this->items[0])) {
            throw new RuntimeException('Cannot calculate average of non-numeric values');
        }

        $sum = array_sum($this->items);
        $count = count($this->items);

        return $sum / $count;
    }

    public function max(): int|float
    {
        if (!$this->count()) {
            throw new RuntimeException('Cannot calculate max of an empty collection');
        }

        if (!is_int($this->items[0]) && !is_float($this->items[0])) {
            throw new RuntimeException('Cannot calculate max of non-numeric values');
        }

        return max($this->items);
    }

    public function min(): int|float
    {
        if (!$this->count()) {
            throw new RuntimeException('Cannot calculate min of an empty collection');
        }

        if (!is_int($this->items[0]) && !is_float($this->items[0])) {
            throw new RuntimeException('Cannot calculate min of non-numeric values');
        }

        return min($this->items);
    }

    /**
     * @return Collection<TItem>
     */
    public function order(): Collection
    {
        $sorted = $this->items;
        sort($sorted);

        return new Collection($sorted);
    }

    /**
     * @return Collection<TItem>
     */
    public function orderBy(callable $callable): Collection
    {
        $sorted = $this->items;
        usort($sorted, function ($a, $b) use ($callable) {
            return $callable($a) <=> $callable($b);
        });

        return new Collection($sorted);
    }

    /**
     * @return Collection<TItem>
     */
    public function orderDescending(): Collection
    {
        $sorted = $this->items;
        rsort($sorted);

        return new Collection($sorted);
    }

    /**
     * @return Collection<TItem>
     */
    public function orderDescendingBy(callable $callable): Collection
    {
        $sorted = $this->items;
        usort($sorted, function ($a, $b) use ($callable) {
            return $callable($b) <=> $callable($a);
        });

        return new Collection($sorted);
    }

    /**
     * @return Collection<TItem>
     */
    public function reverse(): Collection
    {
        return new Collection(array_reverse($this->items));
    }

    public function sum(): int|float
    {
        if (!$this->count()) {
            throw new RuntimeException('Cannot calculate sum of an empty collection');
        }

        if (!is_int($this->items[0]) && !is_float($this->items[0])) {
            throw new RuntimeException('Cannot calculate sum of non-numeric values');
        }

        return array_sum($this->items);
    }

    /**
     * @return Collection<TItem>
     */
    public function append(mixed $item): Collection
    {
        return new Collection(array_merge($this->items, (array)$item));
    }

    /**
     * @return Collection<TItem>
     */
    public function prepend(mixed $item): Collection
    {
        return new Collection(array_merge((array)$item, $this->items));
    }

    /**
     * @return Collection<Collection<TItem>>
     */
    public function chunk(int $length): Collection
    {
        $chunks = array_chunk($this->items, $length);

        return new Collection(array_map(fn ($chunk) => new Collection($chunk), $chunks));
    }

    /**
     * @param Enumerable<TItem> $items
     * @return Collection<TItem>
     */
    public function concat(Enumerable $items): Collection
    {
        return new Collection(array_merge($this->items, $items->toArray()));
    }

    public function contains(mixed $item): bool
    {
        if (is_callable($item)) {
            foreach ($this->items as $value) {
                if ($item($value)) {
                    return true;
                }
            }

            return false;
        }

        return in_array($item, $this->items, true);
    }

    public function countBy(callable $callable): int
    {
        $count = 0;

        foreach ($this->items as $item) {
            if ($callable($item)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return Collection<TItem>
     */
    public function distinct(): Collection
    {
        return new Collection(array_unique($this->items, SORT_REGULAR));
    }

    /**
     * @return Collection<TItem>
     */
    public function distinctBy(callable $callable): Collection
    {
        $unique = [];
        $distinct = [];

        foreach ($this->items as $item) {
            $key = $callable($item);

            if (!in_array($key, $unique, true)) {
                $unique[] = $key;
                $distinct[] = $item;
            }
        }

        return new Collection($distinct);
    }

    public function itemAt(int $index): mixed
    {
        if (!isset($this->items[$index])) {
            throw new OutOfBoundsException(sprintf(
                'Index %d is out of bounds for collection of size %d',
                $index,
                $this->count()
            ));
        }

        return $this->items[$index];
    }

    public function itemAtOrDefault(int $index, mixed $default = null): mixed
    {
        return $this->items[$index] ?? $default;
    }

    /**
     * @return Collection<TItem>
     */
    public function except(Enumerable $items): Collection
    {
        return new Collection(array_diff($this->items, $items->toArray()));
    }

    public function first(): mixed
    {
        if (empty($this->items)) {
            throw new RuntimeException('Collection is empty');
        }

        return $this->items[0];
    }

    public function firstOrDefault(mixed $default = null): mixed
    {
        return $this->items[0] ?? $default;
    }

    /**
     * @return Collection<covariant Collection<TItem>>
     */
    public function groupBy(callable $callable): Collection
    {
        $groups = [];

        foreach ($this->items as $item) {
            $key = $callable($item);
            if (!isset($groups[$key])) {
                $groups[$key] = Collection::empty();
            }

            $groups[$key] = $groups[$key]->append($item);
        }

        return new Collection($groups);
    }

    /**
     * @return Collection<TItem>
     */
    public function intersect(Enumerable $items): Collection
    {
        return new Collection(array_intersect($this->items, $items->toArray()));
    }

    public function last(): mixed
    {
        if (empty($this->items)) {
            throw new RuntimeException('Collection is empty');
        }

        return $this->items[$this->count() - 1];
    }

    public function lastOrDefault(mixed $default = null): mixed
    {
        return $this->items[$this->count() - 1] ?? $default;
    }

    /**
     * @return Collection<TItem>
     */
    public function select(callable $callable): Collection
    {
        $selected = [];

        foreach ($this->items as $index => $item) {
            $selected[] = $callable($item, $index);
        }

        return new Collection($selected);
    }

    public function single(callable $callable): mixed
    {
        $result = array_filter($this->items, $callable, ARRAY_FILTER_USE_BOTH);
        $count = count($result);

        if ($count === 0) {
            throw new RuntimeException('No item found that matches the condition');
        }

        if ($count > 1) {
            throw new RuntimeException(sprintf(
                'Expected exactly one item, found %s',
                count($result)
            ));
        }

        return reset($result);
    }

    public function singleOrDefault(callable $callable, mixed $default = null): mixed
    {
        $result = array_filter($this->items, $callable, ARRAY_FILTER_USE_BOTH);
        $count = count($result);

        if ($count === 0) {
            return $default;
        }

        if ($count > 1) {
            throw new RuntimeException(sprintf(
                'Expected exactly one item, found %s',
                count($result)
            ));
        }

        return reset($result);
    }

    /**
     * @return Collection<TItem>
     */
    public function skip(int $count): Collection
    {
        return new Collection(array_slice($this->items, $count));
    }

    /**
     * @return Collection<TItem>
     */
    public function skipLast(int $count): Collection
    {
        return new Collection(array_slice($this->items, 0, -$count));
    }

    /**
     * @return Collection<TItem>
     */
    public function skipWhile(callable $callable): Collection
    {
        $skipped = [];
        $skipping = true;

        foreach ($this->items as $index => $item) {
            if ($skipping && $callable($item, $index)) {
                continue;
            }

            $skipping = false;
            $skipped[] = $item;
        }

        return new Collection($skipped);
    }

    /**
     * @return Collection<TItem>
     */
    public function take(int $count): Collection
    {
        return new Collection(array_slice($this->items, 0, $count));
    }

    /**
     * @return Collection<TItem>
     */
    public function takeLast(int $count): Collection
    {
        return new Collection(array_slice($this->items, -$count));
    }

    /**
     * @return Collection<TItem>
     */
    public function takeWhile(callable $callable): Collection
    {
        $taken = [];
        $taking = true;

        foreach ($this->items as $index => $item) {
            if ($taking && $callable($item, $index)) {
                $taken[] = $item;
            } else {
                $taking = false;
            }
        }

        return new Collection($taken);
    }

    /**
     * @return Collection<TItem>
     */
    public function union(Enumerable $second): Collection
    {
        $merged = array_merge($this->items, iterator_to_array($second));
        $unique = array_unique($merged, SORT_REGULAR);

        return new Collection($unique);
    }

    /**
     * @return Collection<TItem>
     */
    public function where(callable $callable): Collection
    {
        $filtered = [];

        foreach ($this->items as $index => $item) {
            if ($callable($item, $index)) {
                $filtered[] = $item;
            }
        }

        return new Collection($filtered);
    }

    /**
     * @return Collection<TItem>
     */
    public function zip(Enumerable $second, callable $callable): Collection
    {
        $zipped = [];
        $secondItems = $second->toArray();

        foreach ($this->items as $index => $item) {
            if (!isset($secondItems[$index])) {
                break;
            }

            $zipped[] = $callable($item, $secondItems[$index]);
        }

        return new Collection($zipped);
    }

    public function toArray(): array
    {
        return $this->items;
    }
}
