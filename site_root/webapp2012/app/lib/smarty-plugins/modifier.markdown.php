<?php

/*
 *  テキストに埋め込まれてるURLをリンクにする。
 *	simple markdown formatting courtesy of Lea Verou
 *		http://lea.verou.me/2012/04/in-defense-of-reinventing-wheels/
 *	URL->link regex from Jeff Atwood
 *		http://www.codinghorror.com/blog/2008/10/the-problem-with-urls.html
 */
function smarty_modifier_markdown($text, $maxWordLength = 2048, $syling = true, $linking = true)
{
	$php = <<<PHP
		\$prefix = '';
		\$suffix = '';
		if (substr(\$match[0], 0, 1) === '(' && substr(\$match[0], -1) === ')') {
			\$url = substr(\$match[0], 1, strlen(\$match[0])-2);
			\$prefix = '(';
			\$suffix = ')';
		} else if (substr(\$match[0], 0, 1) === '(') {
			\$url = substr(\$match[0], 1);
			\$prefix = '(';
		} else {
			\$url = \$match[0];
		}
		\$url = trim(\$url);
		\$link = \$prefix;
		\$link .= '<a target="_blank" href="' . \$url . '">';
		\$link .= (strlen(\$match[1]) > $maxWordLength) ? substr(\$match[1], 0, $maxWordLength - 1 ) . '…' : \$match[1];
		\$link .= '</a>';
		\$link .= \$suffix;
		return \$link;
PHP;
	$markdown = create_function('$match', $php);
	//	bold
	$text = preg_replace('@(?<!\\\\)\\*(?<!\\\\)\\*(.+?)(?<!\\\\)\\*(?<!\\\\)\\*@', '<strong>$1</strong>', $text);
	//	italic
	$text = preg_replace('@(?<!\\\\)\\*(.+?)(?<!\\\\)\\*@', '<em>$1</em>', $text);
	//	links
	return preg_replace_callback(
		'/\(?\s*?\bhttps?:\/\/([-A-Za-z0-9\+&@#\/%\?=~_\(\)|!:,\.;]*[-A-Za-z0-9\+&@#\/%=~_()|])/', 
		create_function('$match', $php),
		$text
	);
}