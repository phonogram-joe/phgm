<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

function smarty_prefilter_encoding_sjis_to_utf8($source, $smarty)
{
	return mb_convert_encoding($source, 'utf-8', 'sjis-win');
}
