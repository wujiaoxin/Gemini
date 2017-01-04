<?php

namespace app\wechat\controller;
use think\Request;

class Index extends \think\Controller{
	protected $wechat;
    protected $openid;
	
	protected function replyText($keys){
		return $this->wechat->text($keys)->reply();
	}

	protected function _event($event) {
		switch ($event) {
			// 粉丝关注事件
			case 'subscribe':
			case 'scan':
				return $this->replyText('欢迎关注公众号！');
			// 粉丝取消关注
			case 'unsubscribe':
				exit("success");
			// 点击微信菜单的链接
			case 'click':
				return $this->replyText('你点了菜单链接！');
			// 微信扫码推事件
			case 'scancode_push':
			case 'scancode_waitmsg':
				$scanInfo = $this->wechat->getRev()->getRevScanInfo();
				return $this->replyText("你扫码的内容是:".$scanInfo['ScanResult']);
			case 'location':
				$location = $this->wechat->getRevEventGeo();
				return $this->replyText($location[x].':'.$location[y]);
				//return $this->replyText("catch you！");
			default:
				return $this->replyText($event);
		}
	}
	
	public function index() {
		$this->wechat = & load_wechat('Receive');
		if ($this->wechat->valid() === FALSE) {
			exit($this->wechat->errMsg);
		}
		$this->openid = $this->wechat->getRev()->getRevFrom();
		$msgType = $this->wechat->getRev()->getRevType();
		switch ($msgType) {
			case 'text': 
				$keys = $this->wechat->getRevContent();
				return $this->replyText($keys);
			case 'event':
				$event = $this->wechat->getRevEvent();
				return $this->_event(strtolower($event['event']));
			case 'image':
			case 'video':
				return $this->replyText('video');
			case 'location':
				$location = $this->wechat->getRevEventGeo();
				return $this->replyText($location[x].':'.$location[y]);
			default:
				return $this->replyText($msgType);
		}
	}
	public function menu() {
		$menu = & load_wechat('menu');
		$indexUrl = URL('wechat/index/getOauthRedirect');
		$dealerUrl = URL('mobile/open/dealer');
		$aboutUrl = URL('mobile/open/aboutus');
		$userUrl = URL('mobile/user/index');
		$helpUrl = URL('mobile/open/help');
		$data = json_decode('{
			"button":[
				{	
					"type":"view",
					"name":"进入VP贷",
					"url":"'.$indexUrl.'"
				},
				{	
					"type":"view",
					"name":"车商申请",
					"url":"'.$dealerUrl.'"
				}, 
				{
					"name":"服务中心",
					"sub_button":[
					{
						"type":"view",
						"name":"个人中心",
						"url":"'.$userUrl.'"
					},
					{
						"type": "scancode_push", 
						"name": "扫码注册", 
						"key": "scan_reg"
					},
					{	
						"type":"view",
						"name":"使用帮助",
						"url":"'.$helpUrl.'"
					},					
					{
						"type":"view",
						"name":"关于VP贷",
						"url":"'.$aboutUrl.'"
					}]
				}]
			}') ;
		$result = $menu->createMenu($data);
		if($result===FALSE){
			echo $menu->errMsg;
			//echo "createMenu error <br/>";
			//var_dump($data);
		}else{
			echo "createMenu Success <br/>";
			//var_dump($data);
		}
	}

	public function getOauthRedirect() {
		$oauth = & load_wechat('Oauth');
		$callback = URL('wechat/index/getUserInfo');
		$state = 1;
		$scope = 'snsapi_userinfo'; 
		$result = $oauth->getOauthRedirect($callback, $state, $scope);
		if($result===FALSE){
			echo "error";
			return false;
		}else{//TODO: Redirect

			$this->redirect($result,302);
			//echo $result;
		}
	}
	
	public function getUserInfo() {
		$oauth = & load_wechat('Oauth');
		$result = $oauth->getOauthAccessToken();
		if($result===FALSE){
			echo "Error: getOauthAccessToken failed";
			return false;
		}else{
			$access_token = $result['access_token'];
			$openid = $result['openid'];
			if(!empty($openid)){
				$model =  model('MemberWechat');
				session('user_openid', $openid);
				$isLogin = $model->loginByOpenid($openid);
				if($isLogin == 1){
					$redirectUrl = URL('mobile/index/index');
					$this->redirect($redirectUrl,302);
				}else if($isLogin == 0){//未关联用户
					$userInfo = $model->where('openid',$openid)->find();
					if($userInfo == null){
						$userInfo = $oauth->getOauthUserinfo($access_token, $openid);
						if($userInfo===FALSE){
							echo "Error: getOauthUserinfo failed";
							return false;
						}else{
							$model->save($userInfo);
							$redirectUrl = URL('mobile/user/login');
							$this->redirect($redirectUrl,302);
						}
					}else{//已入库用户
						//echo "Error: 已入库用户";
						$redirectUrl = URL('mobile/user/login');
						$this->redirect($redirectUrl,302);
					}
				}else{//异常用户:例如被禁用
					echo "Error: 异常用户";
				}
			}else{//未获取OPENID
				echo "Error: Can't get openid";
			}
		}		
	}
	
}

