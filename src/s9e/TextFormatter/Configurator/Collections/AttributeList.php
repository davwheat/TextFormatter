<?php

/**
* @package   s9e\TextFormatter
* @copyright Copyright (c) 2010-2013 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\TextFormatter\Configurator\Collections;

use s9e\TextFormatter\Configurator\Validators\AttributeName;

/**
* Hosts a list of attribute names. The config array it returns contains the names, deduplicated and
* sorted
*/
class AttributeList extends NormalizedList
{
	/**
	* {@inheritdoc}
	*/
	public function normalizeValue($attrName)
	{
		return AttributeName::normalize($attrName);
	}

	/**
	* {@inheritdoc}
	*/
	public function asConfig()
	{
		$list = array_unique($this->items);
		sort($list);

		return $list;
	}
}