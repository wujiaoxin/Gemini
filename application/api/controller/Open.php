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
		
		$resp = '{
			"code": 1,
			"msg": "获取成功！",
			"data": {
				"imglist": [
					{
						"imgurl": "https://www.vpdai.com/public/wap/images/1.png",
						"detailurl": "https://t.vpdai.com/api/open/aboutus",
						"title": "买车活动1",
						"sort": 1
					},
					{
						"imgurl": "https://www.vpdai.com/public/wap/images/2.png",
						"detailurl": "https://t.vpdai.com/api/open/aboutus",
						"title": "买车活动2",
						"sort": 2
					}
				]
			}
		}';
		$resp = json_decode($resp);
		return json($resp);
	}
	
	public function dynamicResource() {
		
		$resp = '{
				"code": 1,
				"msg": "获取成功！",
				"data": {
					"android": {
						"version": "1.0.0",
						"log": "1.更新了XXX功能；2.修复了XXX问题",
						"url": "https://www.vpdai.com/public/apk/1.apk",
						"updatetime": 1493027758
					},
					"indeximg": {
						"url": "http://t.vpdai.com/public/wap/images/indeximg.png",
						"updatetime": 1493027758
					},
					"banner": [
						{
							"imgurl": "https://www.vpdai.com/public/wap/images/1.png",
							"detailurl": "https://t.vpdai.com/api/open/aboutus",
							"title": "买车活动1",
							"sort": 1
						},
						{
							"imgurl": "https://www.vpdai.com/public/wap/images/2.png",
							"detailurl": "https://t.vpdai.com/api/open/aboutus",
							"title": "买车活动2",
							"sort": 2
						}
					]
				}
			}';
		$resp = json_decode($resp);
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
	
	public function appdl() {
		return view();
	}
	

	
}
