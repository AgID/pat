<?php
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.9-dev
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2019 Fuel Development Team
 * @link       https://fuelphp.com
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Arr
{

    public static function get($array, $key, $default = null)
    {
        if (!is_array($array) and !$array instanceof \ArrayAccess) {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        if (is_null($key)) {
            return $array;
        }

        if (is_array($key)) {
            $return = array();
            foreach ($key as $k) {
                $return[$k] = static::get($array, $k, $default);
            }
            return $return;
        }

        is_object($key) and $key = (string)$key;

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $key_part) {
            if (($array instanceof \ArrayAccess and isset($array[$key_part])) === false) {
                if (!is_array($array) or !array_key_exists($key_part, $array)) {
                    return $default;
                }
            }

            $array = $array[$key_part];
        }

        return $array;
    }


    public static function set(&$array, $key, $value = null)
    {
        if (is_null($key)) {
            $array = $value;
            return;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                static::set($array, $k, $v);
            }
        } else {
            $keys = explode('.', $key);

            while (count($keys) > 1) {
                $key = array_shift($keys);

                if (!isset($array[$key]) or !is_array($array[$key])) {
                    $array[$key] = array();
                }

                $array =& $array[$key];
            }

            $array[array_shift($keys)] = $value;
        }
    }


    public static function pluck($array, $key, $index = null)
    {
        $return = array();
        $get_deep = strpos($key, '.') !== false;

        if (!$index) {
            foreach ($array as $i => $a) {
                $return[] = (is_object($a) and !($a instanceof \ArrayAccess)) ? $a->{$key} :
                    ($get_deep ? static::get($a, $key) : $a[$key]);
            }
        } else {
            foreach ($array as $i => $a) {
                $index !== true and $i = (is_object($a) and !($a instanceof \ArrayAccess)) ? $a->{$index} : $a[$index];
                $return[$i] = (is_object($a) and !($a instanceof \ArrayAccess)) ? $a->{$key} :
                    ($get_deep ? static::get($a, $key) : $a[$key]);
            }
        }

        return $return;
    }


    public static function key_exists($array, $key)
    {
        if (!is_array($array) and !$array instanceof \ArrayAccess) {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        is_object($key) and $key = (string)$key;

        if (!is_string($key)) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $key_part) {
            if (($array instanceof \ArrayAccess and isset($array[$key_part])) === false) {
                if (!is_array($array) or !array_key_exists($key_part, $array)) {
                    return false;
                }
            }

            $array = $array[$key_part];
        }

        return true;
    }


    public static function delete(&$array, $key)
    {
        if (is_null($key)) {
            return false;
        }

        if (is_array($key)) {
            $return = array();
            foreach ($key as $k) {
                $return[$k] = static::delete($array, $k);
            }
            return $return;
        }

        $key_parts = explode('.', $key);

        if (!is_array($array) or !array_key_exists($key_parts[0], $array)) {
            return false;
        }

        $this_key = array_shift($key_parts);

        if (!empty($key_parts)) {
            $key = implode('.', $key_parts);
            return static::delete($array[$this_key], $key);
        } else {
            unset($array[$this_key]);
        }

        return true;
    }


    public static function assoc_to_keyval($assoc, $key_field, $val_field)
    {
        if (!is_array($assoc) and !$assoc instanceof \Iterator) {
            throw new \InvalidArgumentException('The first parameter must be an array.');
        }

        $output = array();
        foreach ($assoc as $row) {
            if (isset($row[$key_field]) and isset($row[$val_field])) {
                $output[$row[$key_field]] = $row[$val_field];
            }
        }

        return $output;
    }


    public static function keyval_to_assoc($array, $key_field, $val_field)
    {
        if (!is_array($array) and !$array instanceof \Iterator) {
            throw new \InvalidArgumentException('The first parameter must be an array.');
        }

        $output = array();
        foreach ($array as $key => $value) {
            $output[] = array(
                $key_field => $key,
                $val_field => $value,
            );
        }

        return $output;
    }


    public static function to_assoc($arr)
    {
        if (($count = count($arr)) % 2 > 0) {
            throw new \BadMethodCallException('Number of values in to_assoc must be even.');
        }
        $keys = $vals = array();

        for ($i = 0; $i < $count - 1; $i += 2) {
            $keys[] = array_shift($arr);
            $vals[] = array_shift($arr);
        }
        return array_combine($keys, $vals);
    }


    public static function is_assoc($arr)
    {
        if (!is_array($arr)) {
            throw new \InvalidArgumentException('The parameter must be an array.');
        }

        $counter = 0;
        foreach ($arr as $key => $unused) {
            if (!is_int($key) or $key !== $counter++) {
                return true;
            }
        }
        return false;
    }


    public static function flatten($array, $glue = ':', $reset = true, $indexed = true)
    {
        static $return = array();
        static $curr_key = array();

        if ($reset) {
            $return = array();
            $curr_key = array();
        }

        foreach ($array as $key => $val) {
            $curr_key[] = $key;
            if (is_array($val) and ($indexed or array_values($val) !== $val)) {
                static::flatten($val, $glue, false, $indexed);
            } else {
                $return[implode($glue, $curr_key)] = $val;
            }
            array_pop($curr_key);
        }
        return $return;
    }


    public static function flatten_assoc($array, $glue = ':', $reset = true)
    {
        return static::flatten($array, $glue, $reset, false);
    }

    public static function reverse_flatten($array, $glue = ':')
    {
        $return = array();

        foreach ($array as $key => $value) {
            if (stripos($key, $glue) !== false) {
                $keys = explode($glue, $key);
                $temp =& $return;
                while (count($keys) > 1) {
                    $key = array_shift($keys);
                    $key = is_numeric($key) ? (int)$key : $key;
                    if (!isset($temp[$key]) or !is_array($temp[$key])) {
                        $temp[$key] = array();
                    }
                    $temp =& $temp[$key];
                }

                $key = array_shift($keys);
                $key = is_numeric($key) ? (int)$key : $key;
                $temp[$key] = $value;
            } else {
                $key = is_numeric($key) ? (int)$key : $key;
                $return[$key] = $value;
            }
        }

        return $return;
    }

    public static function filter_prefixed($array, $prefix, $remove_prefix = true)
    {
        $return = array();
        foreach ($array as $key => $val) {
            if (preg_match('/^' . $prefix . '/', $key)) {
                if ($remove_prefix === true) {
                    $key = preg_replace('/^' . $prefix . '/', '', $key);
                }
                $return[$key] = $val;
            }
        }
        return $return;
    }

    public static function filter_recursive($array, $callback = null)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = $callback === null ? static::filter_recursive($value) : static::filter_recursive($value, $callback);
            }
        }

        return $callback === null ? array_filter($array) : array_filter($array, $callback);
    }

    public static function remove_prefixed($array, $prefix)
    {
        foreach ($array as $key => $val) {
            if (preg_match('/^' . $prefix . '/', $key)) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    public static function filter_suffixed($array, $suffix, $remove_suffix = true)
    {
        $return = array();
        foreach ($array as $key => $val) {
            if (preg_match('/' . $suffix . '$/', $key)) {
                if ($remove_suffix === true) {
                    $key = preg_replace('/' . $suffix . '$/', '', $key);
                }
                $return[$key] = $val;
            }
        }
        return $return;
    }

    public static function remove_suffixed($array, $suffix)
    {
        foreach ($array as $key => $val) {
            if (preg_match('/' . $suffix . '$/', $key)) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    public static function filter_keys($array, $keys, $remove = false)
    {
        $return = array();
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $remove or $return[$key] = $array[$key];
                if ($remove) {
                    unset($array[$key]);
                }
            }
        }
        return $remove ? $array : $return;
    }

    public static function insert(array &$original, $value, $pos)
    {
        if (count($original) < abs($pos)) {
            \Errorhandler::notice('Position larger than number of elements in array in which to insert.');
            return false;
        }

        array_splice($original, $pos, 0, $value);

        return true;
    }

    public static function insert_assoc(array &$original, array $values, $pos)
    {
        if (count($original) < abs($pos)) {
            return false;
        }

        $original = array_slice($original, 0, $pos, true) + $values + array_slice($original, $pos, null, true);

        return true;
    }

    public static function insert_before_key(array &$original, $value, $key, $is_assoc = false)
    {
        $pos = array_search($key, array_keys($original));

        if ($pos === false) {
            \Errorhandler::notice('Unknown key before which to insert the new value into the array.');
            return false;
        }

        return $is_assoc ? static::insert_assoc($original, $value, $pos) : static::insert($original, $value, $pos);
    }

    public static function insert_after_key(array &$original, $value, $key, $is_assoc = false)
    {
        $pos = array_search($key, array_keys($original));

        if ($pos === false) {
            \Errorhandler::notice('Unknown key after which to insert the new value into the array.');
            return false;
        }

        return $is_assoc ? static::insert_assoc($original, $value, $pos + 1) : static::insert($original, $value, $pos + 1);
    }

    public static function insert_after_value(array &$original, $value, $search, $is_assoc = false)
    {
        $key = array_search($search, $original);

        if ($key === false) {
            \Errorhandler::notice('Unknown value after which to insert the new value into the array.');
            return false;
        }

        return static::insert_after_key($original, $value, $key, $is_assoc);
    }

    public static function insert_before_value(array &$original, $value, $search, $is_assoc = false)
    {
        $key = array_search($search, $original);

        if ($key === false) {
            \Errorhandler::notice('Unknown value before which to insert the new value into the array.');
            return false;
        }

        return static::insert_before_key($original, $value, $key, $is_assoc);
    }

    public static function sort($array, $key, $order = 'asc', $sort_flags = SORT_REGULAR)
    {
        if (!is_array($array)) {
            throw new \InvalidArgumentException('Arr::sort() - $array must be an array.');
        }

        if (empty($array)) {
            return $array;
        }

        $b = array();

        foreach ($array as $k => $v) {
            $b[$k] = static::get($v, $key);
        }

        switch ($order) {
            case 'asc':
                asort($b, $sort_flags);
                break;

            case 'desc':
                arsort($b, $sort_flags);
                break;

            default:
                throw new \InvalidArgumentException('Arr::sort() - $order must be asc or desc.');
                break;
        }

        $c = array();

        foreach ($b as $key => $val) {
            $c[] = $array[$key];
        }

        return $c;
    }


    public static function multisort($array, $conditions, $ignore_case = false)
    {
        $temp = array();
        $keys = array_keys($conditions);

        foreach ($keys as $key) {
            $temp[$key] = static::pluck($array, $key, true);
            is_array($conditions[$key]) or $conditions[$key] = array($conditions[$key]);
        }

        $args = array();
        foreach ($keys as $key) {
            $args[] = $ignore_case ? array_map('strtolower', $temp[$key]) : $temp[$key];
            foreach ($conditions[$key] as $flag) {
                $args[] = $flag;
            }
        }

        $args[] = &$array;

        call_user_func_array('array_multisort', $args);
        return $array;
    }


    public static function average($array)
    {
        // No arguments passed, lets not divide by 0
        if (!($count = count($array)) > 0) {
            return 0;
        }

        return (array_sum($array) / $count);
    }


    public static function replace_key($source, $replace, $new_key = null)
    {
        if (is_string($replace)) {
            $replace = array($replace => $new_key);
        }

        if (!is_array($source) or !is_array($replace)) {
            throw new \InvalidArgumentException('Arr::replace_key() - $source must an array. $replace must be an array or string.');
        }

        $result = array();

        foreach ($source as $key => $value) {
            if (array_key_exists($key, $replace)) {
                $result[$replace[$key]] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }


    public static function merge()
    {
        $array = func_get_arg(0);
        $arrays = array_slice(func_get_args(), 1);

        if (!is_array($array)) {
            throw new \InvalidArgumentException('Arr::merge() - all arguments must be arrays.');
        }

        foreach ($arrays as $arr) {
            if (!is_array($arr)) {
                throw new \InvalidArgumentException('Arr::merge() - all arguments must be arrays.');
            }

            foreach ($arr as $k => $v) {
                // numeric keys are appended
                if (is_int($k)) {
                    array_key_exists($k, $array) ? $array[] = $v : $array[$k] = $v;
                } elseif (is_array($v) and array_key_exists($k, $array) and is_array($array[$k])) {
                    $array[$k] = static::merge($array[$k], $v);
                } else {
                    $array[$k] = $v;
                }
            }
        }

        return $array;
    }


    public static function merge_assoc()
    {
        $array = func_get_arg(0);
        $arrays = array_slice(func_get_args(), 1);

        if (!is_array($array)) {
            throw new \InvalidArgumentException('Arr::merge_assoc() - all arguments must be arrays.');
        }

        foreach ($arrays as $arr) {
            if (!is_array($arr)) {
                throw new \InvalidArgumentException('Arr::merge_assoc() - all arguments must be arrays.');
            }

            foreach ($arr as $k => $v) {
                if (is_array($v) and array_key_exists($k, $array) and is_array($array[$k])) {
                    $array[$k] = static::merge_assoc($array[$k], $v);
                } else {
                    $array[$k] = $v;
                }
            }
        }

        return $array;
    }


    public static function prepend(&$arr, $key, $value = null)
    {
        $arr = (is_array($key) ? $key : array($key => $value)) + $arr;
    }


    public static function in_array_recursive($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $value) {
            if (!$strict and $needle == $value) {
                return true;
            } elseif ($needle === $value) {
                return true;
            } elseif (is_array($value) and static::in_array_recursive($needle, $value, $strict)) {
                return true;
            }
        }

        return false;
    }


    public static function is_multi($arr, $all_keys = false)
    {
        $values = array_filter($arr, 'is_array');
        return $all_keys ? count($arr) === count($values) : count($values) > 0;
    }


    public static function search($array, $value, $default = null, $recursive = true, $delimiter = '.', $strict = false)
    {
        if (!is_array($array) and !$array instanceof \ArrayAccess) {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        if (!is_null($default) and !is_int($default) and !is_string($default)) {
            throw new \InvalidArgumentException('Expects parameter 3 to be an string or integer or null.');
        }

        if (!is_string($delimiter)) {
            throw new \InvalidArgumentException('Expects parameter 5 must be an string.');
        }

        $key = array_search($value, $array, $strict);

        if ($recursive and $key === false) {
            $keys = array();
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    $rk = static::search($v, $value, $default, true, $delimiter, $strict);
                    if ($rk !== $default) {
                        $keys = array($k, $rk);
                        break;
                    }
                }
            }
            $key = count($keys) ? implode($delimiter, $keys) : false;
        }

        return $key === false ? $default : $key;
    }


    public static function unique($arr)
    {
        // filter out all duplicate values
        return array_filter($arr, function ($item) {
            // contrary to popular belief, this is not as static as you think...
            static $vars = array();

            if (in_array($item, $vars, true)) {
                // duplicate
                return false;
            } else {
                // record we've had this value
                $vars[] = $item;

                // unique
                return true;
            }
        });
    }

    public static function sum($array, $key)
    {
        if (!is_array($array) and !$array instanceof \ArrayAccess) {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        return array_sum(static::pluck($array, $key));
    }


    public static function reindex($arr)
    {
        // reindex this level
        $arr = array_merge($arr);

        foreach ($arr as $k => &$v) {
            is_array($v) and $v = static::reindex($v);
        }

        return $arr;
    }


    public static function previous_by_key($array, $key, $get_value = false, $strict = false)
    {
        if (!is_array($array) and !$array instanceof \ArrayAccess) {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        // get the keys of the array
        $keys = array_keys($array);

        // and do a lookup of the key passed
        if (($index = array_search($key, $keys, $strict)) === false) {
            // key does not exist
            return false;
        } // check if we have a previous key
        elseif (!isset($keys[$index - 1])) {
            // there is none
            return null;
        }

        // return the value or the key of the array entry the previous key points to
        return $get_value ? $array[$keys[$index - 1]] : $keys[$index - 1];
    }

    public static function next_by_key($array, $key, $get_value = false, $strict = false)
    {
        if (!is_array($array) and !$array instanceof \ArrayAccess) {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        // get the keys of the array
        $keys = array_keys($array);

        // and do a lookup of the key passed
        if (($index = array_search($key, $keys, $strict)) === false) {
            // key does not exist
            return false;
        } // check if we have a previous key
        elseif (!isset($keys[$index + 1])) {
            // there is none
            return null;
        }

        // return the value or the key of the array entry the previous key points to
        return $get_value ? $array[$keys[$index + 1]] : $keys[$index + 1];
    }

    public static function previous_by_value($array, $value, $get_value = true, $strict = false)
    {
        if (!is_array($array) and !$array instanceof \ArrayAccess) {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        // find the current value in the array
        if (($key = array_search($value, $array, $strict)) === false) {
            // bail out if not found
            return false;
        }

        // get the list of keys, and find our found key
        $keys = array_keys($array);
        $index = array_search($key, $keys);

        // if there is no previous one, bail out
        if (!isset($keys[$index - 1])) {
            return null;
        }

        // return the value or the key of the array entry the previous key points to
        return $get_value ? $array[$keys[$index - 1]] : $keys[$index - 1];
    }

    public static function next_by_value($array, $value, $get_value = true, $strict = false)
    {
        if (!is_array($array) and !$array instanceof \ArrayAccess) {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        // find the current value in the array
        if (($key = array_search($value, $array, $strict)) === false) {
            // bail out if not found
            return false;
        }

        // get the list of keys, and find our found key
        $keys = array_keys($array);
        $index = array_search($key, $keys);

        // if there is no next one, bail out
        if (!isset($keys[$index + 1])) {
            return null;
        }

        // return the value or the key of the array entry the next key points to
        return $get_value ? $array[$keys[$index + 1]] : $keys[$index + 1];
    }

    public static function subset(array $array, array $keys, $default = null)
    {
        $result = array();

        foreach ($keys as $key) {
            static::set($result, $key, static::get($array, $key, $default));
        }

        return $result;
    }
}
