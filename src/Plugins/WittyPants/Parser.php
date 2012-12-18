<?php

/**
* @package   s9e\TextFormatter
* @copyright Copyright (c) 2010-2012 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\TextFormatter\Plugins\WittyPants;

use s9e\TextFormatter\Parser;
use s9e\TextFormatter\Plugins\ParserBase;

class Parser extends ParserBase
{
	/**
	* {@inheritdoc}
	*/
	public function parse($text, array $matches)
	{
		$attrName = $this->config['attrName'];
		$tagName  = $this->config['tagName'];

		// Do apostrophes ’ after a letter or at the beginning of a word
		preg_match_all(
			"#(?<=\\pL)'|(?<!\\S)'(?=\\pL|[0-9]{2})#uS",
			$text,
			$matches,
			PREG_OFFSET_CAPTURE
		);
		foreach ($matches[0] as $m)
		{
			$this->parser->addSelfClosingTag($tagName, $m[1], 1)->setAttribute($attrName, "\xE2\x80\x99");
		}

		// Do symbols found after a digit:
		//  - apostrophe ’ if it's followed by an "s" as in 80's
		//  - prime ′ and double prime ″
		//  - multiply sign × if it's followed by an optional space and another digit
		preg_match_all(
			'#[0-9](?:["\']? ?x(?= ?[0-9])|["\']s?)#S',
			$text,
			$matches,
			PREG_OFFSET_CAPTURE
		);
		foreach ($matches[0] as $m)
		{
			// Test for a multiply sign or an "s" at the end of the match
			$c = substr($m[0][0], -1);
			if ($c === 's')
			{
				$pos  = $m[0][1] + strlen($m[0][0]) - 2;
				$char = "\xE2\x80\x99";

				$this->parser->addSelfClosingTag($tagName, $pos, 1)->setAttribute($attrName, $char);
			}
			elseif ($c === 'x')
			{
				$pos  = $m[0][1] + strlen($m[0][0]) - 1;
				$char = "\xC3\x97";

				$this->parser->addSelfClosingTag($tagName, $pos, 1)->setAttribute($attrName, $char);
			}

			// Test for a prime right after the digit
			$c = $m[0][0][1];
			if ($c === "'" || $c === '"')
			{
				$pos  = 1 + $m[0][1];
				$char = ($c === "'") ? "\xE2\x80\xB2" : "\xE2\x80\xB3";

				$this->parser->addSelfClosingTag($tagName, $pos, 1)->setAttribute($attrName, $char);
			}
		}

		// Do quote pairs ‘’ and “”
		preg_match_all(
			'#(?<![0-9\\pL])(["\']).+?\\1(?![0-9\\pL])#uS',
			$text,
			$matches,
			PREG_OFFSET_CAPTURE
		);
		foreach ($matches[0] as $m)
		{
			$left  = $this->parser->addSelfClosingTag($tagName, $m[1], 1);
			$right = $this->parser->addSelfClosingTag($tagName, $m[1] + strlen($m[0]) - 1, 1);

			$left->setAttribute($attrName, ($m[0][0] === '"') ? "\xE2\x80\x9C" : "\xE2\x80\x98");
			$right->setAttribute($attrName, ($m[0][0] === '"') ? "\xE2\x80\x9D" : "\xE2\x80\x99");

			$left->pairWith($right);
		}

		// Do en dash –, em dash — and ellipsis …
		preg_match_all(
			'#(?:---?|\\.\\.\\.)#S',
			$text,
			$matches,
			PREG_OFFSET_CAPTURE
		);
		$chars = array(
			'--'  => "\xE2\x80\x93",
			'---' => "\xE2\x80\x94",
			'...' => "\xE2\x80\xA6"
		);
		foreach ($matches[0] as $m)
		{
			$pos  = $m[1];
			$len  = strlen($m[0]);
			$char = $chars[$m[0]];

			$this->parser->addSelfClosingTag($tagName, $pos, $len)->setAttribute($attrName, $char);
		}

		// Do symbols ©, ® and ™
		preg_match_all(
			'#\\((?:c|r|tm)\\)#i',
			$text,
			$matches,
			PREG_OFFSET_CAPTURE
		);
		$chars = array(
			'(c)'  => "\xC2\xA9",
			'(r)'  => "\xC2\xAE",
			'(tm)' => "\xE2\x84\xA2"
		);
		foreach ($matches[0] as $m)
		{
			$pos  = $m[1];
			$len  = strlen($m[0]);
			$char = $chars[strtr($m[0][0], 'CMRT', 'cmrt')];

			$this->parser->addSelfClosingTag($tagName, $pos, $len)->setAttribute($attrName, $char);
		}
	}
}