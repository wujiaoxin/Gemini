<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

return array(
	'default_return_type'    => 'json',
	'session'           => array(
		'prefix'     => 'geminiapi',
		'type'       => '',
		'auto_start' => true,
		'use_cookies'=> false,//PHPSESSID=uj01rpjnk60pjk1e17m57n2n95
		'use_trans_sid'=> false,
		'expire'     => 86400,
	),
);