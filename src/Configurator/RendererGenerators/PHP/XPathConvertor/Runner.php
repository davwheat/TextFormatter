<?php

/**
* @package   s9e\TextFormatter
* @copyright Copyright (c) 2010-2019 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\TextFormatter\Configurator\RendererGenerators\PHP\XPathConvertor;

use RuntimeException;
use s9e\TextFormatter\Configurator\RendererGenerators\PHP\XPathConvertor\Convertors\AbstractConvertor;
use s9e\TextFormatter\Configurator\RendererGenerators\PHP\XPathConvertor\Convertors\BooleanFunctions;
use s9e\TextFormatter\Configurator\RendererGenerators\PHP\XPathConvertor\Convertors\BooleanOperators;
use s9e\TextFormatter\Configurator\RendererGenerators\PHP\XPathConvertor\Convertors\Comparisons;
use s9e\TextFormatter\Configurator\RendererGenerators\PHP\XPathConvertor\Convertors\Core;
use s9e\TextFormatter\Configurator\RendererGenerators\PHP\XPathConvertor\Convertors\Math;
use s9e\TextFormatter\Configurator\RendererGenerators\PHP\XPathConvertor\Convertors\MultiByteStringManipulation;
use s9e\TextFormatter\Configurator\RendererGenerators\PHP\XPathConvertor\Convertors\SingleByteStringFunctions;
use s9e\TextFormatter\Configurator\RendererGenerators\PHP\XPathConvertor\Convertors\SingleByteStringManipulation;

class Runner
{
	/**
	* @var array
	*/
	protected $callbacks;

	/**
	* @var array
	*/
	protected $groups;

	/**
	* @var array
	*/
	protected $matchGroup;

	/**
	* @var string
	*/
	protected $regexp = '((?!))';

	/**
	* @var array
	*/
	protected $regexps;

	/**
	* Constructor
	*
	* @return void
	*/
	public function __construct()
	{
		$this->setConvertors($this->getDefaultConvertors());
	}

	/**
	* Convert given XPath expression to PHP
	*
	* @param  string $expr
	* @return string
	*/
	public function convert($expr)
	{
		$match = $this->getMatch($expr);
		if (!isset($match))
		{
			throw new RuntimeException("Cannot convert '" . $expr . "'");
		}

		list($name, $args) = $match;

		return call_user_func_array($this->callbacks[$name], $args);
	}

	/**
	* Return the type/group associated with given XPath expression
	*
	* @param  string      $expr XPath expression
	* @return string|null       Expression's type/group, or NULL if unknown
	*/
	public function getType($expr)
	{
		$match = $this->getMatch($expr);

		return (isset($match, $this->matchGroup[$match[0]])) ? $this->matchGroup[$match[0]] : null;
	}

	/**
	* Set the list of convertors used by this instance
	*
	* @param  AbstractConvertor[] $convertors
	* @return void
	*/
	public function setConvertors(array $convertors)
	{
		$this->callbacks  = [];
		$this->matchGroup = [];
		$this->groups     = [];
		$this->regexps    = [];
		foreach ($convertors as $convertor)
		{
			$this->addConvertor($convertor);
		}

		// Sort regexps by length to keep their order consistent
		$this->sortRegexps();

		// Add regexp groups
		foreach ($this->groups as $group => $captures)
		{
			sort($captures);
			$this->regexps[$group] = '(?<' . $group . '>' . implode('|', $captures) . ')';
		}

		$this->regexp = '(^(?:' . implode('|', $this->regexps) . ')$)';
	}

	/**
	* Add a convertor to the list used by this instance
	*
	* @param  AbstractConvertor $convertor
	* @return void
	*/
	protected function addConvertor(AbstractConvertor $convertor)
	{
		foreach ($convertor->getRegexpGroups() as $name => $group)
		{
			$this->matchGroup[$name] = $group;
			$this->groups[$group][]  = '(?&' . $name . ')';
		}

		foreach ($convertor->getRegexps() as $name => $regexp)
		{
			$regexp = $this->insertCaptureNames($name, $regexp);
			$regexp = str_replace(' ', '\\s*', $regexp);
			$regexp = '(?<' . $name . '>' . $regexp . ')';

			$this->callbacks[$name] = [$convertor, 'convert' . $name];
			$this->regexps[$name]   = $regexp;
		}
	}

	/**
	* Get the list of arguments produced by a regexp's match
	*
	* @param  string[] $matches Regexp matches
	* @param  string   $name    Regexp name
	* @return string[]
	*/
	protected function getArguments(array $matches, $name)
	{
		$args = [];
		$i    = 0;
		while (isset($matches[$name . $i]))
		{
			$args[] = $matches[$name . $i];
			++$i;
		}

		return $args;
	}

	/**
	* Return the default list of convertors
	*
	* @return AbstractConvertor[]
	*/
	protected function getDefaultConvertors()
	{
		$convertors   = [];
		$convertors[] = new BooleanFunctions($this);
		$convertors[] = new BooleanOperators($this);
		$convertors[] = new Comparisons($this);
		$convertors[] = new Core($this);
		$convertors[] = new Math($this);
		if (extension_loaded('mbstring'))
		{
			$convertors[] = new MultiByteStringManipulation($this);
		}
		$convertors[] = new SingleByteStringFunctions($this);
		$convertors[] = new SingleByteStringManipulation($this);

		return $convertors;
	}

	/**
	* Get the match generated by this instance's regexp on given XPath expression
	*
	* @param  string     $expr XPath expression
	* @return array|null       Array of [<match name>, <arguments>] or NULL
	*/
	protected function getMatch($expr)
	{
		if (preg_match($this->regexp, $expr, $m))
		{
			foreach ($m as $name => $match)
			{
				if ($match !== '' && isset($this->callbacks[$name]))
				{
					return [$name, $this->getArguments($m, $name)];
				}
			}
		}

		return null;
	}

	/**
	* Insert capture names into given regexp
	*
	* @param  string $name   Name of the regexp, used to name captures
	* @param  string $regexp Original regexp
	* @return string         Modified regexp
	*/
	protected function insertCaptureNames($name, $regexp)
	{
		$i = 0;

		return preg_replace_callback(
			'((?<!\\\\)\\((?!\\?))',
			function ($m) use (&$i, $name)
			{
				return '(?<' . $name . $i++ . '>';
			},
			$regexp
		);
	}

	/**
	* Sort regexps by length
	*
	* @return void
	*/
	protected function sortRegexps()
	{
		uasort(
			$this->regexps,
			function ($a, $b)
			{
				return strlen($b) - strlen($a);
			}
		);
	}
}