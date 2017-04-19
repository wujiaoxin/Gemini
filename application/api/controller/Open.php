<?php
// +----------------------------------------------------------------------
// | /mobile/open
// +----------------------------------------------------------------------
// | 移动端公开访问接口
// +----------------------------------------------------------------------
// | Author: fwj
// +----------------------------------------------------------------------

namespace app\api\controller;
use app\common\controller\Base;

class Open extends Base {
	
	public function _initialize() {
		parent::_initialize();
	}
	public function index() {
		$resp['code'] = 1;
		$resp['msg'] = 'OpenAPI';
		return json($resp);
	}
	
	public function banner() {
		$resp['code'] = 1;
		$resp['msg'] = '获取成功！';
		
		$imglist = array();
		
		$data["imglist"] = $imglist;
		$data["updatetime"] = time();

		$resp['data'] = $data;
				
		
		return json($resp);
	}
	
	
	public function aboutus() {
		return view();
	}
	
	public function guide() {
		return view();
	}
	
	public function question() {
		return view();
	}
	
	public function charge() {
		return view();
	}
	
	public function law() {
		return view();
	}
	
	public function contact() {
		return view();
	}
	
	public function protocol() {
		return view();
	}
	

	
}
