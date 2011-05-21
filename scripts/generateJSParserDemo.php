#!/usr/bin/php
<?php

include __DIR__ . '/../src/TextFormatter/ConfigBuilder.php';

$cb = new \s9e\Toolkit\TextFormatter\ConfigBuilder;

$cb->disallowHost('*.example.com');

$cb->BBCodes->addPredefinedBBCode('B');
$cb->BBCodes->addPredefinedBBCode('I');
$cb->BBCodes->addPredefinedBBCode('U');
$cb->BBCodes->addPredefinedBBCode('S');
$cb->BBCodes->addPredefinedBBCode('URL');
$cb->BBCodes->addPredefinedBBCode('LIST');
$cb->BBCodes->addPredefinedBBCode('COLOR');
$cb->BBCodes->addPredefinedBBCode('NOPARSE');
//$cb->BBCodes->addPredefinedBBCode('EMAIL');
$cb->BBCodes->addPredefinedBBCode('YOUTUBE');

// Force YouTube vids to autoplay
$cb->setTagAttributeOption('YOUTUBE', 'content', 'replaceWith', '$1&amp;autoplay=1');

$cb->Emoticons->addEmoticon(':)', '<img alt=":)" src="https://github.com/images/icons/public.png"/>');

$cb->Censor->addWord('apple', 'banana');

$cb->loadPlugin('Autolink');
$cb->loadPlugin('WittyPants');

$jsParser = $cb->getJSParser(array(
	'compilation'     => (empty($_SERVER['argv'][1])) ? 'none' : 'ADVANCED_OPTIMIZATIONS',
	'disableLogTypes' => (empty($_SERVER['argv'][2])) ? array() : array('debug', 'warning', 'error'),
	'removeDeadCode'  => (isset($_SERVER['argv'][3])) ? (bool) $_SERVER['argv'][3] : true
));

$closureCompilerNote = (empty($_SERVER['argv'][1])) ? '' : ' It has been minified to ' . round(strlen($jsParser) / 1024, 1) . 'KB (' . round(strlen(gzencode($jsParser, 9)) / 1024, 1) . 'KB gzipped) with [url=http://closure-compiler.appspot.com/home]Google Closure Compiler[/url].';

file_put_contents('/tmp/foo.js', $jsParser);

ob_start();
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title></title>
	<style type="text/css">
		div
		{
			margin-bottom: 10px;
		}

		#logdiv
		{
			border: dashed 1px #8af;
		}

		#logdiv,
		#preview
		{
			font-family: sans;
			white-space: pre-wrap;
		}

		label
		{
			cursor: pointer;
		}
	</style>
</head>
<body>
	<div style="float:left">
		<form>
			<textarea cols="80" rows="15">This is a demo of the Javascript port of [url=https://github.com/s9e/Toolkit/tree/master/src/TextFormatter]s9e\TextFormatter[/url].

A few BBCodes have been added such as:

[list]
	[*][b]bold[/b], [i]italic[/i], [u]underline[/u], [s]strikethrough[/s],
	[*][color=#f05]co[/color][color=#2f2]lo[/color][color=#02f]r,[/color]
	[*][NOPARSE][URL][/NOPARSE], [NOPARSE:123][NOPARSE][/NOPARSE:123], [NOPARSE][YOUTUBE][/NOPARSE], and [NOPARSE][LIST][/NOPARSE]
[/list]

Additionally, in order to demonstrate some other features:

[list]
	[*]one emoticon :) has been added [i](Emoticons plugin)[/i]
	[*]the word "apple" is censored and automatically replaced with "banana" [i](Censor plugin)[/i]
	[*]some typography is enhanced, e.g. (c) (tm) and "quotes" [i](WittyPants plugin)[/i]
	[*]links to [url=http://example.com]example.com[/url] are disabled [i](ConfigBuilder::disallowHost())[/i]
	[*]loose URLs such as http://github.com are automatically transformed into links [i](Autolink plugin)[/i]
	[*]a YouTube video---at the bottom---keeps playing as you're editing the other content, to demonstrate the partial-update algorithm used to refresh the preview
[/list]

Take a look at the log, hover the messages with the mouse and click them to get to the part of the text that generated them.

The parser/renderer used on this page page has been generated via [url=https://github.com/s9e/Toolkit/blob/master/scripts/generateJSParserDemo.php]this script[/url].<?php echo $closureCompilerNote; ?> The raw sources can be found [url=https://github.com/s9e/Toolkit/blob/master/src/TextFormatter/TextFormatter.js]at GitHub[/url].

[youtube]http://www.youtube.com/watch?v=QH2-TGUlwu4[/youtube]
</textarea>
			<br>
			<input type="checkbox" id="rendercheck" checked="checked"><label for="rendercheck"> Render</label>
			<input type="checkbox" id="logcheck"><label for="logcheck"> Show log</label>
		</form>
	</div>

	<div style="float:left">
		<form>
			<input type="checkbox" id="Autolink" checked="checked" onchange="toggle(this)"><label for="Autolink"> Autolink</label><br>
			<input type="checkbox" id="BBCodes" checked="checked" onchange="toggle(this)"><label for="BBCodes"> BBCodes</label><br>
			<input type="checkbox" id="Censor" checked="checked" onchange="toggle(this)"><label for="Censor"> Censor</label><br>
			<input type="checkbox" id="Emoticons" checked="checked" onchange="toggle(this)"><label for="Emoticons"> Emoticons</label><br>
			<input type="checkbox" id="WittyPants" checked="checked" onchange="toggle(this)"><label for="WittyPants"> WittyPants</label>
		</form>
	</div>

	<div style="clear:both"></div>

	<div id="logdiv" style="display:none"></div>

	<pre id="preview"></pre>

	<script type="text/javascript"><?php echo $jsParser; ?>

		var text,
			xml,

			textarea = document.getElementsByTagName('textarea')[0],
			preview = document.getElementById('preview'),

			rendercheck = document.getElementById('rendercheck'),

			logcheck = document.getElementById('logcheck'),
			logdiv = document.getElementById('logdiv'),
			autoHighlight = true,

			s = new XMLSerializer();

		rendercheck.onchange = refreshOutput;

		textarea.onmouseout = function()
		{
			autoHighlight = true;
		}

		logcheck.onchange = function()
		{
			if (logcheck.checked)
			{
				logdiv.style.display = '';
				refreshLog();
			}
			else
			{
				logdiv.style.display = 'none';
			}
		}

		function refreshOutput()
		{
			if (rendercheck.checked)
			{
				var newPreview = document.createElement('pre');

				newPreview.appendChild(
					s9e.TextFormatter.render(xml)
				);

				refreshElement(preview, newPreview);
			}
			else
			{
				preview.textContent = s.serializeToString(xml)
			}
		}

		function processNodes(oldNode, newNode)
		{
			if (oldNode.localName !== newNode.localName
			 || oldNode.nodeType  !== newNode.nodeType)
			{
				return 0;
			}

			if (oldNode.isEqualNode(newNode))
			{
				return 1;
			}

			if (oldNode.nodeType === Node.TEXT_NODE)
			{
				oldNode.textContent = newNode.textContent;
				return 1;
			}

			syncAttributes(oldNode, newNode);
			refreshElement(oldNode, newNode);

			return 1;
		}

		function refreshElement(oldEl, newEl)
		{
			var oldCnt = oldEl.childNodes.length,
				newCnt = newEl.childNodes.length,
				i = 0;

			while (i < oldCnt && i < newCnt)
			{
				var oldNode = oldEl.childNodes[i],
					newNode = newEl.childNodes[i];

				if (!processNodes(oldNode, newNode))
				{
					break;
				}

				++i;
			}

			var left = i,
				right = 0,
				maxRight = Math.min(oldCnt - left, newCnt - left);

			while (right < maxRight)
			{
				var oldNode = oldEl.childNodes[oldCnt - (right + 1)],
					newNode = newEl.childNodes[newCnt - (right + 1)];

				if (processNodes(oldNode, newNode))
				{
					++right;
				}
				else
				{
					break;
				}
			}

			/**
			* Clone the new nodes from newEl
			*/
			var frag = document.createDocumentFragment();
			i = left;
			while (i < (newCnt - right))
			{
				frag.appendChild(newEl.childNodes[i].cloneNode(true));
				++i;
			}

			/**
			* Remove the dirty nodes from oldEl
			*/
			i = oldCnt - right;
			while (--i >= left)
			{
				oldEl.removeChild(oldEl.childNodes[i]);
			}

			if (left === oldCnt)
			{
				oldEl.appendChild(frag);
			}
			else
			{
				oldEl.insertBefore(frag, oldEl.childNodes[left]);
			}
		}

		function syncAttributes(oldEl, newEl)
		{
			var oldCnt = oldEl.attributes.length,
				newCnt = newEl.attributes.length,
				i = oldCnt;

			while (--i >= 0)
			{
				var oldAttr = oldEl.attributes[i];

				if (!newEl.hasAttributeNS(oldAttr.namespaceURI, oldAttr.name))
				{
					oldEl.removeAttributeNS(oldAttr.namespaceURI, oldAttr.name);
				}
			}

			i = newCnt;
			while (--i >= 0)
			{
				var newAttr = newEl.attributes[i];
				oldEl.setAttributeNS(newAttr.namespaceURI, newAttr.name, newAttr.value);
			}
		}

		function refreshLog()
		{
			var log = s9e.TextFormatter.getLog(),
				msgs = [];

			['error', 'warning', 'debug'].forEach(function(type)
			{
				if (!log[type])
				{
					return;
				}

				log[type].forEach(function(entry)
				{
					var msg = '[' + type + '] [' + entry.pluginName + '] ' + entry.msg.replace(
							/%(?:([0-9])\$)?[sd]/g,
							function(str, p1)
							{
								return entry.params[(p1 ? p1 - 1 : 0)];
							}
						);

					if (entry.pos !== undefined)
					{
						if (!entry.len)
						{
							entry.len = 0;
						}

						msg = '<a style="cursor:pointer" onmouseover="highlight(' + entry.pos + ',' + entry.len + ')" onclick="select(' + entry.pos + ',' + entry.len + ')">' + msg + '</a>';
					}

					msgs.push(msg);
				});
			});

			logdiv.innerHTML = (msgs.length) ? msgs.join("\n") : 'No log';
		}

		function select(pos, len)
		{
			autoHighlight = false;
			textarea.focus();
			textarea.setSelectionRange(pos, pos + len);
		}

		function highlight(pos, len)
		{
			if (autoHighlight)
			{
				textarea.setSelectionRange(pos, pos + len);
			}
		}

		function toggle(el)
		{
			s9e.TextFormatter[(el.checked) ? 'enablePlugin' : 'disablePlugin'](el.id);
			text = '';
		}

		window.setInterval(function()
		{
			if (textarea.value === text)
			{
				return;
			}

			text = textarea.value;
			xml = s9e.TextFormatter.parse(text);

			refreshOutput();

			if (logcheck.checked)
			{
				refreshLog();
			}
		}, 20);
	</script>
</body>
</html><?php

file_put_contents(__DIR__ . '/../docs/JSParserDemo.html', ob_get_clean());

echo "Done.\n";