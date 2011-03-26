<?php

/**
* @package   s9e\Toolkit
* @copyright Copyright (c) 2010 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\Toolkit\TextFormatter\Plugins;

use s9e\Toolkit\TextFormatter\ConfigBuilder,
    s9e\Toolkit\TextFormatter\PluginConfig;

/**
* The Fabric plugin is a partial implementation of the Textile format.
*
* @link http://textile.thresholdstate.com/
*/
class FabricConfig extends PluginConfig
{
	protected $tagsNeeded = array(
		'URL',
		'IMG',
		'_'  => 'EM',
		'__' => 'I',
		'*'  => 'STRONG',
		'**' => 'B',
		'??' => 'CITE',
		'-'  => 'DEL',
		'+'  => 'INS',
		'^'  => 'SUPER',
		'~'  => 'SUB',
		'@'  => 'CODE',
		'%'  => 'SPAN',
		'==' => 'NOPARSE'
	);

	public function setUp()
	{
		foreach ($this->tagsNeeded as $tagName)
		{
			if (!$this->cb->tagExists($tagName))
			{
				$this->cb->predefinedTags->{'add' . $tagName}();
			}
		}
	}

	public function getConfig()
	{
		$urlRegexp = ConfigBuilder::buildRegexpFromList($this->cb->getAllowedSchemes()) . '://\\S+';

		$blockModifiers = array(
			'[\\#\\*]+ ',
			'::? ',
			';;? ',
			'h[1-6]\\. ',
			'p\\. ',
			'bq\\.(?: |:' . $urlRegexp . ')',
			'fn[1-9][0-9]{,2}\\. '
		);

		return array(
			'regexp' => array(
				'imagesAndLinks' =>
					'#([!"])(?P<text>.*?)(?P<attr>\\(.*?\\))?\\1(?P<url>:' . $urlRegexp . ')?#iS',

				'blockModifiers' => '#^(?:' . implode('|', $blockModifiers) . ')#Sm',

				'phraseModifiers' =>
					'#(?<!\\pL)(__|\\*\\*|\\?\\?|==|[_*\\-+^~@%]).+?(\\1)(?!\\pL)#Su',

				'acronyms' => '#([A-Z0-9]+)\\(([^\\)]+)\\)#S',

				'tableRow' => '#^\\s*\\|.*\\|$#ms'
			)
		);
	}
}