<?php
// +----------------------------------------------------------------------
// | /mobile/open
// +----------------------------------------------------------------------
// | 移动端公开访问接口
// +----------------------------------------------------------------------
// | Author: fwj
// +----------------------------------------------------------------------

namespace app\mobile\controller;
use app\common\controller\Base;

class Open extends Base {
	
	public function _initialize() {
		parent::_initialize();
	}
	public function index() {
		return "ok";
	}
	
	public function dealer() {//车商申请
		$Dealer = model('Dealer');
		if (IS_POST) {
			$data = input('post.');
			if ($data) {
				$saveData['mobile'] = $data["mobile"];
				$saveData['name'] = $data["name"];
				$saveData['contacts'] = $data["contacts"];
				$saveData['inviter'] = $data["inviter"];
				$saveData['invite_code'] = $Dealer->buildInviteCode();
				$saveData['status'] = 0;
				$result = $Dealer->save($saveData);
				if ($result) {
					return $this->success("提交成功", url('/mobile/index'));
				} else {
					return $this->error($Dealer->getError());
				}
			} else {
				return $this->error($Dealer->getError());
			}
		} else {
			return $this->fetch();
		}
	}
	
	public function protocol() {		
		return $this->fetch();
	}
	
	public function privacy() {		
		return $this->fetch();
	}

	public function invite() {		
		return $this->fetch();
	}

	public function aboutus() {		
		return $this->fetch();
	}
	
	public function help() {		
		return $this->fetch();
	}
	
	public function sendSmsCode($phone = '', $verifycode = '', $send_code  = '', $type = ''){
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		if (!$phone) {
			$data["code"] = 0;
			$data["msg"] = '手机号不能为空！';
			return json($data);
		}
		if ($verifycode) {
			$verify = new \org\Verify();
			$result = $verify->check($verifycode, 1);
			if (!$result) {
				$data["code"] = 0;
				$data["msg"] = '图形验证码错误！';
				return json($data);
			}
		} else {
			$data["code"] = 0;
			$data["msg"] = '图形验证码为空！';
			return json($data);
		}		
		$smsCode = rand(1000,9999);
		$smsMsg = '您的验证码为:' . $smsCode;
		
		//$rc = true;
		$rc = $this->sendSms($phone,$smsMsg);
		if($rc){
			session('smsPhone',$phone);
			session('smsCode',$smsCode);
			$resp['code'] = 1;
			$resp['msg'] = '发送成功';//.$smsCode;//fixed:方便调试，发布需删除
		}		
		return json($resp);
	}
	
    /**
	*get imge base64
	*/
    public function imgToBase64($filePath){
        $img_base64 = '';
        $img_info = getimagesize($filePath);
        $img_type = $img_info[2];
        $fp = fopen($filePath,'r');
        if($fp){
            $file_content = chunk_split(base64_encode(fread($fp,filesize($filePath))));
            fclose($fp);
        }
        return $file_content;
    }
	
	public function tryOCR($id = '',$type = 'idcard'){//TODO:权限校验
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		if (!$id) {
			$resp["code"] = 0;
			$resp["msg"] = 'id不能为空';
			return json($resp);
		}		
		$images_path = get_order_files($id);
		
		if(isset($images_path['path'])){
			$images_path = ROOT_PATH .$images_path['path'];	
		}else{
			$resp["code"] = 0;
			$resp["msg"] = '找不到图片';
			return json($resp);
		}
		//dump($images_path);		
		$host = "https://dm-51.data.aliyun.com";
		$path = "/rest/160601/ocr/ocr_idcard.json";
		$method = "POST";
		$appcode = "78ad23084155412c99d12c97436a7b79";
		$headers = array();
		array_push($headers, "Authorization:APPCODE " . $appcode);
		array_push($headers, "Content-Type".":"."application/json; charset=UTF-8");
		$querys = "";
		$base64_img_string = $this->imgToBase64($images_path);
		$bodys = "{\"inputs\":[{\"image\":{\"dataType\":50,\"dataValue\":\"".$base64_img_string."\"},\"configure\":{\"dataType\":50,\"dataValue\":\"{\\\"side\\\":\\\"face\\\"}\"}}]}";
		$url = $host . $path;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		if (1 == strpos("$".$host, "https://"))
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}
		curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
		$response = curl_exec($curl);
		
		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == '200') {
			$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$resp_header = substr($response, 0, $headerSize);
			$resp_body = substr($response, $headerSize);
			//var_dump($resp_body);
			if(isset($resp_body)){
				$ocr_result = json_decode($resp_body,true);
				//dump($ocr_result['outputs'][0]['outputValue']['dataValue']);
			}
			if(isset($ocr_result)){
				$ocr_result = json_decode($ocr_result['outputs'][0]['outputValue']['dataValue'],true);
				if($ocr_result['success']){
					$resp['code'] = 1;
					$resp['msg'] = '识别成功';
					$resp['data']['name'] = $ocr_result['name'];
					$resp['data']['idcard_num'] = $ocr_result['num'];
					return json($resp);
				}else{
					$resp['code'] = 0;
					$resp['msg'] = '识别失败';
				}
				//dump($ocr_result['address']);
			}
		}
		return json($resp);
		
	}
	
}
