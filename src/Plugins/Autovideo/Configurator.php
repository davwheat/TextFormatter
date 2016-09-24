<?php

/**
* @package   s9e\TextFormatter
* @copyright Copyright (c) 2010-2016 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\TextFormatter\Plugins\Autovideo;

use s9e\TextFormatter\Configurator\Helpers\RegexpBuilder;
use s9e\TextFormatter\Plugins\ConfiguratorBase;

class Configurator extends ConfiguratorBase
{
	/**
	* @var string Name of attribute that stores the video's URL
	*/
	protected $attrName = 'src';

	/**
	* @var string
	*/
	protected $quickMatch = '://';

	/**
	* @var string
	*/
	protected $regexp = '#\\bhttps?://[-.\\w]+/[-./\\w]+\\.(?:mp4|ogg|webm)(?!\\S)#i';

	/**
	* @var string Name of the tag used to represent videos
	*/
	protected $tagName = 'VIDEO';

	/**
	* Creates the tag used by this plugin
	*
	* @return void
	*/
	protected function setUp()
	{
		if (isset($this->configurator->tags[$this->tagName]))
		{
			return;
		}

		// Create a tag
		$tag = $this->configurator->tags->add($this->tagName);

		// Add an attribute using the default url filter
		$filter = $this->configurator->attributeFilters['#url'];
		$tag->attributes->add($this->attrName)->filterChain->append($filter);

		// Set the default template
		$tag->template = '<video src="{@' . $this->attrName . '}"/>';
	}
}