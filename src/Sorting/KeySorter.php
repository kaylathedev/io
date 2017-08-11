<?php
namespace WaffleSystems\IO\Sorting;

class KeySorter
{

    private $internalKey;

    public function __construct($internalKey)
    {
        if (!is_array($internalKey)) {
            $internalKey = [$internalKey];
        }
        $this->internalKey = $internalKey;
    }

    private function internalCompare($left, $right)
    {
        if ($this->internalKey !== null) {
            foreach ($this->internalKey as $key) {
                $left = $left[$key];
            }
            foreach ($this->internalKey as $key) {
                $right = $right[$key];
            }
        }
        if ($left == $right) {
            return 0;
        }

        return $left < $right ? -1 : 1;
    }

    public function sortDescending(array $data)
    {
        uasort($data, [$this, 'internalCompare']);
        return $data;
    }
}
