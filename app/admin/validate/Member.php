<?php
namespace app\admin\validate;
use think\validate;

class Member extends validate{

	/**
	 *@description 验证器规则
	 *@buildcode(true)
	*/
	protected $rule = [
		'username'=>['require'],
		'mobile'=>['unique:member','regex'=>'/^1[3456789]\d{9}$/'],
		'email'=>['unique:member','regex'=>'/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/'],
	];

	/**
	 *@description 错误提示
	 *@buildcode(true)
	*/
	protected $message = [
		'username.require'=>'用户名不能为空',
		'mobile.unique'=>'手机号已经存在',
		'mobile.regex'=>'手机号格式错误',
		'email.unique'=>'邮箱已经存在',
		'email.regex'=>'邮箱格式错误',
	];

	/**
	 *@description 验证场景
	 *@buildcode(true)
	*/
	protected $scene  = [
		'add'=>['username','mobile','email'],
		'update'=>['username','mobile','email'],
	];



}