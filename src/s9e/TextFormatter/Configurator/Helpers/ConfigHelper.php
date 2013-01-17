<?php

/**
* @package   s9e\TextFormatter
* @copyright Copyright (c) 2010-2013 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\TextFormatter\Configurator\Helpers;

use Exception;
use ReflectionMethod;
use RuntimeException;
use Traversable;
use s9e\TextFormatter\Configurator\ConfigProvider;
use s9e\TextFormatter\Configurator\Items\ProgrammableCallback;
use s9e\TextFormatter\Configurator\Items\Variant;
use s9e\TextFormatter\Configurator\JavaScript\Code;

abstract class ConfigHelper
{
	/**
	* Recursively filter a config array to replace variants with the desired value
	*
	* @param  array|Traversable &$config  Config array
	* @param  string             $variant Preferred variant
	* @return void
	*/
	public static function filterVariants(&$config, $variant = null)
	{
		foreach ($config as $k => &$v)
		{
			// Use while instead of if to handle recursive variants. This is not supposed to happen
			// though
			while ($v instanceof Variant)
			{
				$v = $v->get($variant);

				// A null value indicates that the value is not supposed to exist for given variant.
				// This is different from having no specific value for given variant
				if ($v === null)
				{
					unset($config[$k]);
					continue 2;
				}
			}

			if (is_array($v) || $v instanceof Traversable)
			{
				self::filterVariants($v, $variant);
			}
		}
	}

	/**
	* Generate a quickMatch string from a list of strings
	*
	* This is basically a LCS implementation, tuned for small strings and fast failure
	*
	* @param  array $strings Array of strings
	* @return mixed          quickMatch string, or FALSE if none could be generated
	*/
	public static function generateQuickMatchFromList(array $strings)
	{
		foreach ($strings as $string)
		{
			$stringLen  = strlen($string);
			$substrings = array();

			for ($len = $stringLen; $len; --$len)
			{
				$pos = $stringLen - $len;

				do
				{
					$substrings[substr($string, $pos, $len)] = 1;
				}
				while (--$pos >= 0);
			}

			if (isset($goodStrings))
			{
				$goodStrings = array_intersect_key($goodStrings, $substrings);

				if (empty($goodStrings))
				{
					break;
				}
			}
			else
			{
				$goodStrings = $substrings;
			}
		}

		if (empty($goodStrings))
		{
			return false;
		}

		// The strings are stored by length descending, so we return the first in the list
		return strval(key($goodStrings));
	}

	/**
	* Optimize the size of a deep array by deduplicating identical structures
	*
	* This method is meant to be used on a config array which is only read and never modified
	*
	* @param  array &$config
	* @param  array &$cache
	* @return array
	*/
	public static function optimizeArray(array &$config, array &$cache = array())
	{
		foreach ($config as $k => &$v)
		{
			if (!is_array($v))
			{
				continue;
			}

			// Iterate over the cache to look for a matching structure
			foreach ($cache as &$cachedArray)
			{
				if ($cachedArray == $v)
				{
					// Replace the entry in $config with a reference to the cached value
					$config[$k] =& $cachedArray;

					// Skip to the next element
					continue 2;
				}
			}
			unset($cachedArray);

			// Record this value in the cache
			$cache[] =& $v;

			// Dig deeper into this array
			self::optimizeArray($v, $cache);
		}
		unset($v);
	}

	/**
	* Convert a structure to a (possibly multidimensional) array
	*
	* @param  mixed $value
	* @param  bool  $keepEmpty Whether to keep empty arrays instead of removing them
	* @return array
	*/
	public static function toArray($value, $keepEmpty = false)
	{
		$array = array();

		foreach ($value as $k => $v)
		{
			if (!isset($v))
			{
				// We don't record NULL values
				continue;
			}

			if ($v instanceof ConfigProvider)
			{
				$v = $v->asConfig();
			}
			elseif ($v instanceof Traversable || is_array($v))
			{
				$v = self::toArray($v);
			}
			elseif (!is_scalar($v))
			{
				$type = (is_object($v))
				      ? 'an instance of ' . get_class($v)
				      : 'a ' . gettype($v);

				throw new RuntimeException('Cannot convert ' . $type . ' to array');
			}

			if (!$keepEmpty && $v === array())
			{
				// We don't record empty structures
				continue;
			}

			$array[$k] = $v;
		}

		return $array;
	}
}