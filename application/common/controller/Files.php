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
		//$upload_type = input('get.filename', 'images', 'trim');
		//$config      = $this->$upload_type();
		// 获取表单上传文件 例如上传了001.jpg
		$config = config('order_files_upload');
		$file = request()->file('file');
		$info = $file->validate(['ext'=>'jpg,jpeg,png,gif'])->move($config['rootPath'], true, false);

		if ($info) {
			//if($info->getExtension() == 'php'){				
			//}
			$return['status'] = 1;
			$return['info']   = $this->save($config, $info);
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
	public function save($config, $file) {
		$file           = $this->parseFile($file);
		$file['status'] = 1;
		$file['uid'] = session('user_auth.uid');
		$file['storage_mode'] = 1;
		$file['descr']  = '';
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

	protected function parseFile($info) {
		$data['create_time'] = $info->getATime(); //最后访问时间
		$data['savename']    = $info->getBasename(); //获取无路径的basename
		$data['c_time']      = $info->getCTime(); //获取inode修改时间
		$data['ext']         = $info->getExtension(); //文件扩展名
		$data['name']        = $info->getFilename(); //获取文件名
		$data['m_time']      = $info->getMTime(); //获取最后修改时间
		$data['owner']       = $info->getOwner(); //文件拥有者
		$data['savepath']    = $info->getPath(); //不带文件名的文件路径
		$data['url']         = $data['path']         = str_replace("\\", '/', substr($info->getPathname(), 1)); //全路径
		$data['size']        = $info->getSize(); //文件大小，单位字节
		$data['is_file']     = $info->isFile(); //是否是文件
		$data['is_execut']   = $info->isExecutable(); //是否可执行
		$data['is_readable'] = $info->isReadable(); //是否可读
		$data['is_writable'] = $info->isWritable(); //是否可写
		$data['md5']         = md5_file($info->getPathname());
		$data['sha1']        = sha1_file($info->getPathname());
		return $data;
	}
}