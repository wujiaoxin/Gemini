<?php
namespace app\api\controller;
use think\Request;

class Index {
	
	public function index(){
		return json("VPDAI-API-SERVER");
	}
	
}

