<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */
 
function smarty_outputfilter_encoding_utf8_to_sjis($source, &$smarty)
{
	return mb_convert_encoding($source, 'sjis-win', 'utf-8');
}
