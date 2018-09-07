/** @const */
var shortnameRegexp = /:[-+_a-z0-9]+(?=:)/g;

/** @const */
var unicodeRegexp = /(?:[#*0-9]\ufe0f\u20e3?|[\xa9\xae\u203c\u2049\u2122\u2139\u2194-\u2199\u21a9\u21aa\u2328\u23cf\u23ed-\u23ef\u23f1\u23f2\u23f8-\u23fa\u24c2\u25aa\u25ab\u25b6\u25c0\u25fb\u25fc\u2600-\u2604\u260e\u2611\u2618\u2620\u2622\u2623\u2626\u262a\u262e\u262f\u2638-\u263a\u2640\u2642\u265f\u2660\u2663\u2665\u2666\u2668\u267b\u267e\u2692\u2694-\u2697\u2699\u269b\u269c\u26a0\u26b0\u26b1\u26c8\u26cf\u26d1\u26d3\u26e9\u26f0\u26f1\u26f4\u26f7\u26f8\u2702\u2708\u2709\u270f\u2712\u2714\u2716\u271d\u2721\u2733\u2734\u2744\u2747\u2763\u2764\u27a1\u2934\u2935\u2b05-\u2b07\u3030\u303d\u3297\u3299]\ufe0f|[\u261d\u270c\u270d](?:\ud83c[\udffb-\udfff]|\ufe0f)|[\u270a\u270b](?:\ud83c[\udffb-\udfff])?|[\u231a\u231b\u23e9-\u23ec\u23f0\u23f3\u25fd\u25fe\u2614\u2615\u2648-\u2653\u267f\u2693\u26a1\u26aa\u26ab\u26bd\u26be\u26c4\u26c5\u26ce\u26d4\u26ea\u26f2\u26f3\u26f5\u26fa\u26fd\u2705\u2728\u274c\u274e\u2753-\u2755\u2757\u2795-\u2797\u27b0\u27bf\u2b1b\u2b1c\u2b50\u2b55]|\u26f9(?:\ud83c[\udffb-\udfff]|\ufe0f)(?:\u200d[\u2640\u2642]\ufe0f)?|\ud83c(?:[\udd70\udd71\udd7e\udd7f\ude02\ude37\udf21\udf24-\udf2c\udf36\udf7d\udf96\udf97\udf99-\udf9b\udf9e\udf9f\udfcd\udfce\udfd4-\udfdf\udff5\udff7]\ufe0f|[\udde6-\uddff](?:\ud83c[\udde6-\uddff])?|[\udf85\udfc2\udfc7](?:\ud83c[\udffb-\udfff])?|[\udfc3\udfc4\udfca](?:\ud83c[\udffb-\udfff])?(?:\u200d[\u2640\u2642]\ufe0f)?|[\udfcb\udfcc](?:\ud83c[\udffb-\udfff]|\ufe0f)(?:\u200d[\u2640\u2642]\ufe0f)?|[\udc04\udccf\udd8e\udd91-\udd9a\ude01\ude1a\ude2f\ude32-\ude36\ude38-\ude3a\ude50\ude51\udf00-\udf20\udf2d-\udf35\udf37-\udf7c\udf7e-\udf84\udf86-\udf93\udfa0-\udfc1\udfc5\udfc6\udfc8\udfc9\udfcf-\udfd3\udfe0-\udff0\udff8-\udfff]|\udff3\ufe0f(?:\u200d\ud83c\udf08)?|\udff4(?:\u200d\u2620\ufe0f|\udb40\udc67\udb40\udc62\udb40(?:\udc65\udb40\udc6e\udb40\udc67|\udc73\udb40\udc63\udb40\udc74|\udc77\udb40\udc6c\udb40\udc73)\udb40\udc7f)?)|\ud83d(?:[\udc3f\udcfd\udd49\udd4a\udd6f\udd70\udd73\udd76-\udd79\udd87\udd8a-\udd8d\udda5\udda8\uddb1\uddb2\uddbc\uddc2-\uddc4\uddd1-\uddd3\udddc-\uddde\udde1\udde3\udde8\uddef\uddf3\uddfa\udecb\udecd-\udecf\udee0-\udee5\udee9\udef0\udef3]\ufe0f|[\udc42\udc43\udc46-\udc50\udc66\udc67\udc70\udc72\udc74-\udc76\udc78\udc7c\udc83\udc85\udcaa\udd7a\udd95\udd96\ude4c\ude4f\udec0\udecc](?:\ud83c[\udffb-\udfff])?|[\udc6e\udc71\udc73\udc77\udc81\udc82\udc86\udc87\ude45-\ude47\ude4b\ude4d\ude4e\udea3\udeb4-\udeb6](?:\ud83c[\udffb-\udfff])?(?:\u200d[\u2640\u2642]\ufe0f)?|[\udd74\udd90](?:\ud83c[\udffb-\udfff]|\ufe0f)|[\udc00-\udc3e\udc40\udc44\udc45\udc51-\udc65\udc6a-\udc6d\udc79-\udc7b\udc7d-\udc80\udc84\udc88-\udca9\udcab-\udcfc\udcff-\udd3d\udd4b-\udd4e\udd50-\udd67\udda4\uddfb-\ude44\ude48-\ude4a\ude80-\udea2\udea4-\udeb3\udeb7-\udebf\udec1-\udec5\uded0-\uded2\udeeb\udeec\udef4-\udef9]|\udc41\ufe0f(?:\u200d\ud83d\udde8\ufe0f)?|\udc68(?:\u200d(?:[\u2695\u2696\u2708]\ufe0f|\u2764\ufe0f\u200d\ud83d(?:\udc8b\u200d\ud83d)?\udc68|\ud83c[\udf3e\udf73\udf93\udfa4\udfa8\udfeb\udfed]|\ud83d(?:[\udc68\udc69]\u200d\ud83d(?:\udc66(?:\u200d\ud83d\udc66)?|\udc67(?:\u200d\ud83d[\udc66\udc67])?)|[\udcbb\udcbc\udd27\udd2c\ude80\ude92]|\udc66(?:\u200d\ud83d\udc66)?|\udc67(?:\u200d\ud83d[\udc66\udc67])?)|\ud83e[\uddb0-\uddb3])|\ud83c[\udffb-\udfff](?:\u200d(?:[\u2695\u2696\u2708]\ufe0f|\ud83c[\udf3e\udf73\udf93\udfa4\udfa8\udfeb\udfed]|\ud83d[\udcbb\udcbc\udd27\udd2c\ude80\ude92]|\ud83e[\uddb0-\uddb3]))?)?|\udc69(?:\u200d(?:[\u2695\u2696\u2708]\ufe0f|\u2764\ufe0f\u200d\ud83d(?:\udc8b\u200d\ud83d)?[\udc68\udc69]|\ud83c[\udf3e\udf73\udf93\udfa4\udfa8\udfeb\udfed]|\ud83d(?:[\udcbb\udcbc\udd27\udd2c\ude80\ude92]|\udc66(?:\u200d\ud83d\udc66)?|\udc67(?:\u200d\ud83d[\udc66\udc67])?|\udc69\u200d\ud83d(?:\udc66(?:\u200d\ud83d\udc66)?|\udc67(?:\u200d\ud83d[\udc66\udc67])?))|\ud83e[\uddb0-\uddb3])|\ud83c[\udffb-\udfff](?:\u200d(?:[\u2695\u2696\u2708]\ufe0f|\ud83c[\udf3e\udf73\udf93\udfa4\udfa8\udfeb\udfed]|\ud83d[\udcbb\udcbc\udd27\udd2c\ude80\ude92]|\ud83e[\uddb0-\uddb3]))?)?|\udc6f(?:\u200d[\u2640\u2642]\ufe0f)?|\udd75(?:\ud83c[\udffb-\udfff]|\ufe0f)(?:\u200d[\u2640\u2642]\ufe0f)?)|\ud83e(?:[\udd18-\udd1c\udd1e\udd1f\udd30-\udd36\uddb5\uddb6\uddd1-\uddd5](?:\ud83c[\udffb-\udfff])?|[\udd26\udd37-\udd39\udd3d\udd3e\uddb8\uddb9\uddd6-\udddd](?:\ud83c[\udffb-\udfff])?(?:\u200d[\u2640\u2642]\ufe0f)?|[\udd3c\uddde\udddf](?:\u200d[\u2640\u2642]\ufe0f)?|[\udd10-\udd17\udd1d\udd20-\udd25\udd27-\udd2f\udd3a\udd40-\udd45\udd47-\udd70\udd73-\udd76\udd7a\udd7c-\udda2\uddb0-\uddb4\uddb7\uddc0-\uddc2\uddd0\udde0-\uddff]))(?!\ufe0e)/g;

parseShortnames(text);
parseCustomAliases(text);
parseUnicode(text);


/**
* Add an emoji tag for given sequence
*
* @param  {!integer} tagPos Position of the tag in the original text
* @param  {!integer} tagLen Length of text consumed by the tag
* @param  {!string}  hex    Fully-qualified sequence of codepoints in hex
*/
function addTag(tagPos, tagLen, hex)
{
	var tag = addSelfClosingTag(config.tagName, tagPos, tagLen, 10);

	// Short sequence, only the relevant codepoints are kept
	var seq = hex.replace(/-(?:200d|fe0f)/g, '');
	tag.setAttribute(config.attrName, seq);

	// Twemoji sequence, leading zeroes and trailing VS16 are removed
	var tseq = hex.replace(/^0+/, '').replace(/-fe0f$/, '');
	tag.setAttribute('tseq', tseq);
}

/**
* Get the sequence of Unicode codepoints that corresponds to given emoji
*
* @param  {!string} str UTF-8 emoji
* @return {!string}     Codepoint sequence, e.g. "0023-20e3"
*/
function getHexSequence(str)
{
	var seq = [],
		i   = 0;
	do
	{
		var cp = str.charCodeAt(i);
		if (cp >= 0xD800 && cp <= 0xDBFF)
		{
			cp = (cp << 10) + str.charCodeAt(++i) - 56613888;
		}
		seq.push(('000' + cp.toString(16)).replace(/^0+(.{4,})$/, '$1'));
	}
	while (++i < str.length);

	return seq.join('-');
}

/**
* Parse custom aliases in given text
*
* @param {!string} text Original text
*/
function parseCustomAliases(text)
{
	if (!HINT.EMOJI_HAS_CUSTOM_ALIASES || !config.customRegexp)
	{
		return;
	}

	var matchPos = 0, m;
	if (HINT.EMOJI_HAS_CUSTOM_QUICKMATCH && config.customQuickMatch)
	{
		matchPos = text.indexOf(config.customQuickMatch);
		if (matchPos < 0)
		{
			return;
		}
	}

	config.customRegexp.lastIndex = matchPos;
	while (m = config.customRegexp.exec(text))
	{
		var alias = m[0], tagPos = m['index'];
		if (registeredVars['Emoji.aliases'][alias])
		{
			var hex = getHexSequence(registeredVars['Emoji.aliases'][alias]);
			addTag(tagPos, alias.length, hex);
		}
	}
}

/**
* Parse shortnames in given text
*
* @param {!string} text Original text
*/
function parseShortnames(text)
{
	var m, matchPos = text.indexOf(':');
	if (matchPos < 0)
	{
		return;
	}

	shortnameRegexp.lastIndex = matchPos;
	while (m = shortnameRegexp.exec(text))
	{
		var alias  = m[0] + ':',
			tagLen = alias.length,
			tagPos = m['index'];
		if (registeredVars['Emoji.aliases'][alias])
		{
			var hex = getHexSequence(registeredVars['Emoji.aliases'][alias]);
			addTag(tagPos, tagLen, hex);
		}
		else if (/^:[0-3][0-9a-f]{3,4}(?:-[0-9a-f]{4,5})*:$/.test(alias))
		{
			addTag(tagPos, tagLen, alias.substr(1, tagLen - 2));
		}
	}
}

/**
* Parse Unicode emoji in given text
*
* @param {!string} text Original text
*/
function parseUnicode(text)
{
	var m;
	unicodeRegexp.lastIndex = 0;
	while (m = unicodeRegexp.exec(text))
	{
		var emoji = m[0], tagPos = m['index'];
		addTag(tagPos, emoji.length, getHexSequence(emoji));
	}
}