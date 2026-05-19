<?php

namespace App\Services;

/**
 * Hotel layout: floors 1–9 have rooms X01–X10; floor 10 has 1001–1007.
 * Lift/stairs on the left; horizontal cost is |room1 - room2| on the same floor.
 * Between floors: distance to lift + 2 * floor delta + distance from lift.
 */
class HotelBookingService
{
    public const STATUS_FREE = 'free';

    public const STATUS_BLOCKED = 'blocked';

    public const STATUS_BOOKED = 'booked';

    public const MAX_ROOMS_PER_BOOKING = 5;

    /** @return list<int> */
    public static function allRoomNumbers(): array
    {
        $rooms = [];
        for ($floor = 1; $floor <= 9; $floor++) {
            for ($unit = 1; $unit <= 10; $unit++) {
                $rooms[] = $floor * 100 + $unit;
            }
        }
        for ($u = 1; $u <= 7; $u++) {
            $rooms[] = 1000 + $u;
        }

        return $rooms;
    }

    public static function floorOf(int $room): int
    {
        if ($room >= 1000) {
            return intdiv($room, 100);
        }

        return intdiv($room, 100);
    }

    /** Minutes from room door to lift (left), along the corridor. */
    public static function distanceToLift(int $room): int
    {
        if ($room >= 1000) {
            return $room - 1001;
        }

        return ($room % 100) - 1;
    }

    public static function travelTime(int $r1, int $r2): int
    {
        $f1 = self::floorOf($r1);
        $f2 = self::floorOf($r2);
        if ($f1 === $f2) {
            return abs($r1 - $r2);
        }

        return self::distanceToLift($r1) + 2 * abs($f1 - $f2) + self::distanceToLift($r2);
    }

    /**
     * Worst-case pairwise travel (diameter) for a set — matches “first to last” span on one floor.
     *
     * @param  list<int>  $rooms
     */
    public static function setDiameter(array $rooms): int
    {
        $n = count($rooms);
        if ($n <= 1) {
            return 0;
        }
        $max = 0;
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $max = max($max, self::travelTime($rooms[$i], $rooms[$j]));
            }
        }

        return $max;
    }

    /**
     * Pick up to N available rooms: same floor first (minimum corridor span), else minimum diameter.
     *
     * @param  array<int, string>  $statusByRoom  room number => status
     * @return list<int>|null
     */
    public function selectRooms(array $statusByRoom, int $n): ?array
    {
        $n = min($n, self::MAX_ROOMS_PER_BOOKING);
        if ($n < 1) {
            return null;
        }

        $free = [];
        foreach (self::allRoomNumbers() as $room) {
            if (($statusByRoom[$room] ?? self::STATUS_FREE) === self::STATUS_FREE) {
                $free[] = $room;
            }
        }
        if (count($free) < $n) {
            return null;
        }

        $sameFloor = $this->bestSameFloorSubset($free, $n);
        if ($sameFloor !== null) {
            return $sameFloor;
        }

        return $this->bestMultiFloorSubset($free, $n);
    }

    /**
     * @param  list<int>  $freeRooms
     * @return list<int>|null
     */
    private function bestSameFloorSubset(array $freeRooms, int $n): ?array
    {
        $byFloor = [];
        foreach ($freeRooms as $r) {
            $byFloor[self::floorOf($r)][] = $r;
        }
        foreach ($byFloor as &$list) {
            sort($list);
        }
        unset($list);

        $best = null;
        $bestSpan = PHP_INT_MAX;
        foreach ($byFloor as $floor => $list) {
            if (count($list) < $n) {
                continue;
            }
            $m = count($list);
            for ($i = 0; $i + $n <= $m; $i++) {
                $span = $list[$i + $n - 1] - $list[$i];
                if ($span < $bestSpan || ($span === $bestSpan && ($best === null || $list[$i] < $best[0]))) {
                    $bestSpan = $span;
                    $best = array_slice($list, $i, $n);
                }
            }
        }

        return $best;
    }

    /**
     * @param  list<int>  $freeRooms
     * @return list<int>|null
     */
    private function bestMultiFloorSubset(array $freeRooms, int $n): ?array
    {
        sort($freeRooms);
        $a = count($freeRooms);
        if ($a < $n) {
            return null;
        }

        $binom = $this->binomial($a, $n);
        if ($binom <= 80000) {
            return $this->exhaustiveBestByDiameter($freeRooms, $n);
        }

        $best = $this->bestPartitionedAcrossFloors($freeRooms, $n);
        if ($best !== null) {
            return $best;
        }

        return $this->greedyDiameterSubset($freeRooms, $n);
    }

    /**
     * @param  list<int>  $freeSorted
     * @return list<int>|null
     */
    private function exhaustiveBestByDiameter(array $freeSorted, int $n): ?array
    {
        $a = count($freeSorted);
        $best = null;
        $bestD = PHP_INT_MAX;
        $indices = range(0, $n - 1);

        while (true) {
            $pick = [];
            foreach ($indices as $i) {
                $pick[] = $freeSorted[$i];
            }
            $d = self::setDiameter($pick);
            if ($d < $bestD || ($d === $bestD && ($best === null || $this->lexLess($pick, $best)))) {
                $bestD = $d;
                $best = $pick;
            }
            $j = $n - 1;
            while ($j >= 0 && $indices[$j] === $j + $a - $n) {
                $j--;
            }
            if ($j < 0) {
                break;
            }
            $indices[$j]++;
            for ($k = $j + 1; $k < $n; $k++) {
                $indices[$k] = $indices[$k - 1] + 1;
            }
        }

        return $best;
    }

    /**
     * @param  list<int>  $a
     * @param  list<int>  $b
     */
    private function lexLess(array $a, array $b): bool
    {
        sort($a);
        sort($b);
        foreach ($a as $i => $v) {
            if ($v !== $b[$i]) {
                return $v < $b[$i];
            }
        }

        return false;
    }

    /**
     * Split across k distinct floors (2..min(5,n)), all compositions of counts, minimize diameter.
     *
     * @param  list<int>  $freeSorted
     * @return list<int>|null
     */
    private function bestPartitionedAcrossFloors(array $freeSorted, int $n): ?array
    {
        $byFloor = [];
        foreach ($freeSorted as $r) {
            $byFloor[self::floorOf($r)][] = $r;
        }
        foreach ($byFloor as &$list) {
            sort($list);
        }
        unset($list);

        $floorKeys = array_keys($byFloor);
        sort($floorKeys);
        $numFloors = count($floorKeys);
        $best = null;
        $bestD = PHP_INT_MAX;

        $maxK = min(5, $n, $numFloors);
        $evals = 0;
        $maxEvals = 250000;
        for ($k = 2; $k <= $maxK; $k++) {
            foreach ($this->combinationsOfSize($floorKeys, $k) as $chosenFloors) {
                foreach ($this->positiveCompositions($n, $k) as $parts) {
                    $feasible = true;
                    foreach ($chosenFloors as $idx => $fnum) {
                        if (count($byFloor[$fnum]) < $parts[$idx]) {
                            $feasible = false;
                            break;
                        }
                    }
                    if (! $feasible) {
                        continue;
                    }
                    foreach ($this->productFloorCombinations($byFloor, $chosenFloors, $parts) as $pick) {
                        $d = self::setDiameter($pick);
                        if ($d < $bestD || ($d === $bestD && ($best === null || $this->lexLess($pick, $best)))) {
                            $bestD = $d;
                            $best = $pick;
                        }
                        $evals++;
                        if ($evals >= $maxEvals) {
                            return $best;
                        }
                    }
                }
            }
        }

        return $best;
    }

    /**
     * @return \Generator<int, list<int>>
     */
    private function positiveCompositions(int $sum, int $parts): \Generator
    {
        if ($parts === 1) {
            yield [$sum];

            return;
        }
        for ($first = 1; $first <= $sum - $parts + 1; $first++) {
            foreach ($this->positiveCompositions($sum - $first, $parts - 1) as $rest) {
                yield array_merge([$first], $rest);
            }
        }
    }

    /**
     * @param  array<int, list<int>>  $byFloor
     * @param  list<int>  $floors
     * @param  list<int>  $sizes
     * @return \Generator<int, list<int>>
     */
    private function productFloorCombinations(array $byFloor, array $floors, array $sizes): \Generator
    {
        if (count($floors) === 0) {
            yield [];

            return;
        }
        $f = $floors[0];
        $need = $sizes[0];
        $tailFloors = array_slice($floors, 1);
        $tailSizes = array_slice($sizes, 1);
        foreach ($this->combinationsOfSize($byFloor[$f], $need) as $prefix) {
            foreach ($this->productFloorCombinations($byFloor, $tailFloors, $tailSizes) as $suffix) {
                yield array_merge($prefix, $suffix);
            }
        }
    }

    /**
     * @param  list<int>  $items
     * @return \Generator<int, list<int>>
     */
    private function combinationsOfSize(array $items, int $k): \Generator
    {
        $items = array_values($items);
        $n = count($items);
        if ($k > $n || $k < 0) {
            return;
        }
        if ($k === 0) {
            yield [];

            return;
        }
        $indices = range(0, $k - 1);
        while (true) {
            $comb = [];
            foreach ($indices as $i) {
                $comb[] = $items[$i];
            }
            yield $comb;
            $j = $k - 1;
            while ($j >= 0 && $indices[$j] === $j + $n - $k) {
                $j--;
            }
            if ($j < 0) {
                break;
            }
            $indices[$j]++;
            for ($t = $j + 1; $t < $k; $t++) {
                $indices[$t] = $indices[$t - 1] + 1;
            }
        }
    }

    /**
     * @param  list<int>  $freeSorted
     * @return list<int>|null
     */
    private function greedyDiameterSubset(array $freeSorted, int $n): ?array
    {
        $best = array_slice($freeSorted, 0, $n);
        $bestD = self::setDiameter($best);
        $a = count($freeSorted);
        for ($start = 0; $start <= $a - $n; $start++) {
            $pick = array_slice($freeSorted, $start, $n);
            $d = self::setDiameter($pick);
            if ($d < $bestD || ($d === $bestD && $this->lexLess($pick, $best))) {
                $bestD = $d;
                $best = $pick;
            }
        }

        return $best;
    }

    private function binomial(int $n, int $k): int
    {
        if ($k < 0 || $k > $n) {
            return 0;
        }
        if ($k > $n - $k) {
            $k = $n - $k;
        }
        $res = 1;
        for ($i = 1; $i <= $k; $i++) {
            $res = intdiv($res * ($n - $k + $i), $i);
        }

        return $res;
    }

    /** @return array<int, string> */
    public static function defaultStatusMap(): array
    {
        $map = [];
        foreach (self::allRoomNumbers() as $room) {
            $map[$room] = self::STATUS_FREE;
        }

        return $map;
    }
}
