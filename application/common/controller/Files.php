<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\common\controller;

class Files {
	
	/**
	 * 上传控制器
	 */
	public function upload() {

		$config = config('order_files_upload');
		$file = request()->file('file');
		$fileType = input('fileType', 'image', 'trim');
		if($fileType == 'video'){
			$info = $file->validate(['ext'=>'mp4,mkv,avi,3gp,mov,mpg,rmvb,flv'])->move($config['rootPath'], true, false);
		}else{
			$info = $file->validate(['ext'=>'jpg,jpeg,png,gif'])->move($config['rootPath'], true, false);
		}
		$infoExtend['order_id'] = input('order_id', '0', 'trim');
		$infoExtend['form_key'] = input('form_key', '', 'trim');
		$infoExtend['form_label'] = input('form_label', '', 'trim');
		if ($info) {
			$return['status'] = 1;
			$return['info']   = $this->save($config, $info, $infoExtend);
		} else {
			$return['status'] = 0;
			$return['info']   = $file->getError();
		}

		echo json_encode($return);
	}

	public function delete() {
		//TODO: remove local file & check uid
		$id   = input('id', '', 'trim,intval');
		$uid  =  session('user_auth.uid');
		$resp['status'] = 1;//TODO 标准化返回参数	
		$data['status'] = -1;
		if($id == ''){
			//return $this->error("缺少参数");
			$resp['status'] = 0;
			$resp['info'] = "缺少参数";
		}else{
			$resp['code'] = db("OrderFiles")->where(array('id' => $id,'uid' => $uid))->update($data);
		}		
		echo json_encode($resp);
	}

	/**
	 * 保存上传的信息到数据库
	 * @var view
	 * @access public
	 */
	public function save($config, $file, $infoExtend) {
		$file           = $this->parseFile($file);
		$file['status'] = 1;
		$file['uid'] = session('user_auth.uid');
		$file['storage_mode'] = 1;
		$file['descr']  = '';
		$file['order_id']    = $infoExtend['order_id']; //订单ID
		$file['form_key']    = $infoExtend['form_key']; //表单中文件字段
		$file['form_label']  = $infoExtend['form_label']; //表单中文件标签
		$dbname         = 'OrderFiles';
		$id             = db($dbname)->insertGetId($file);
		if ($id) {
			$data = db($dbname)->where(array('id' => $id))->find();
			return $data;
		} else {
			return false;
		}
	}
	
	/**
	 * 下载本地文件
	 * @param  array    $file     文件信息数组
	 * @param  callable $callback 下载回调函数，一般用于增加下载次数
	 * @param  string   $args     回调函数参数
	 * @return boolean            下载失败返回false
	 */
	public function downLocalFile($file, $callback = null, $args = null) {
		if (is_file($file['rootpath'] . $file['savepath'] . $file['savename'])) {
			/* 调用回调函数新增下载数 */
			is_callable($callback) && call_user_func($callback, $args);

			/* 执行下载 *///TODO: 大文件断点续传
			header("Content-Description: File Transfer");
			header('Content-type: ' . $file['type']);
			header('Content-Length:' . $file['size']);
			if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) {
				//for IE
				header('Content-Disposition: attachment; filename="' . rawurlencode($file['name']) . '"');
			} else {
				header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
			}
			readfile($file['rootpath'] . $file['savepath'] . $file['savename']);
			exit;
		} else {
			$this->error = '文件已被删除！';
			return false;
		}
	}

	public function getFile() {
		$path = input('path', '', 'trim');
		$fullPath = ROOT_PATH.'/public/wap/images/x.png';		
		$uid  = session('user_auth.uid');
		$role  = session('user_auth.role');
		if($uid == null){
			exit;
		}
		$fileInfo = db("OrderFiles")->field('path,uid,order_id')->where("path", $path)->find();
		if($fileInfo!=null){
			if($fileInfo['uid'] == $uid ){
				$fullPath = ROOT_PATH.$fileInfo['path'];
			}else{
				$authfilter['order_id'] = $fileInfo['order_id'];
				$authfilter['auth_uid'] = $uid;
				$authfilter['auth_role'] = $role;			
				$auth = db('OrderAuth')->where($authfilter)->find();
				if($auth != null){
					$fullPath = ROOT_PATH.$fileInfo['path'];
				}
			}
		}else{
			//exit;
		}	
		if (!is_file($fullPath)) {
			//exit;
			$fullPath = ROOT_PATH.'/public/wap/images/x.png';
		}		
		$mime_type = mime_content_type($fullPath); 
		header('Content-type: '.$mime_type);
		$size = readfile($fullPath);
		header('Content-Length:' . $size);
		exit;
	}
	
	protected function parseFile($info) {
		$data['create_time'] = $info->getATime(); //最后访问时间
		$data['savename']    = $info->getBasename(); //获取无路径的basename
		$data['c_time']      = $info->getCTime(); //获取inode修改时间
		$data['ext']         = $info->getExtension(); //文件扩展名
		$data['name']        = $info->getFilename(); //获取文件名
		$data['m_time']      = $info->getMTime(); //获取最后修改时间
		$data['owner']       = $info->getOwner(); //文件拥有者
		$data['savepath']    = $info->getPath(); //不带文件名的文件路径
		$data['path']        = str_replace("\\", '/', substr($info->getPathname(), 1)); //全路径
		$data['url']         = '/mobile/files/getFile?path='.$data['path'];
		$data['size']        = $info->getSize(); //文件大小，单位字节
		$data['md5']         = md5_file($info->getPathname());
		$data['sha1']        = sha1_file($info->getPathname());
		return $data;
	}
}