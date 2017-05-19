<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\common\model;
	
/**
 * 金融方案类
 * @author molong <molong@tensent.cn>
 */
class Programme extends \app\common\model\Base {

	public function result($data){
		unset($data['id']);
		$this->allowField(true)->save($data);
	}

}