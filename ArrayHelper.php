<?php
namespace mgcode\helpers;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
    const TRIM_BEGINNING = 1;
    const TRIM_END = 2;
    const TRIM_BOTH = 3;

    /**
     * Trims array by percents
     * @param array $array
     * @param float $trimmedPercent
     * @param int $position
     * @return array
     */
    public static function trimByPercents(array $array, $trimmedPercent = 0.1, $position = self::TRIM_BOTH)
    {
        $g = $trimmedPercent * count($array);
        $g = floor($g);

        // Trim values if we have to trim
        if ($g) {
            $offset = in_array($position, [static::TRIM_BEGINNING, static::TRIM_BOTH]) ? $g : 0;
            $length = null;
            if ($position == static::TRIM_END) {
                $length = count($array) - $g;
            } else if ($position == static::TRIM_BOTH) {
                $length = count($array) - $g * 2;
            }
            $array = array_slice($array, $offset, $length);
        }

        return $array;
    }

    /**
     * Sorts arrays in specific order by column values
     * @param array $objects
     * @param string|\Closure $column key name of the array element, or property name of the object,
     * @param array $keys
     */
    public static function sortByColumnSpecific(array &$objects, $column, array $keys)
    {
        // We flip the array and use isset instead of in_array. Isset performs much faster.
        $keys = array_flip($keys);

        // Match array column to keys and organize objects into sub-arrays.
        // This is needed, because otherwise we need to iterate all objects for all keys.
        $organize = array();
        foreach ($objects as $k => $object) {
            $val = static::getValue($object, $column);
            if (isset($keys[$val])) {
                $index = $keys[$val];
                $organize[$index][] = $object;
                unset($objects[$k]);
            }
        }

        // Sort organized array
        ksort($organize, SORT_NUMERIC);

        // Merge all sub arrays
        $result = call_user_func_array('array_merge', $organize);

        // Override the variable and merge with unsorted values
        $objects = array_merge($result, $objects);
    }
}