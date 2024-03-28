<?php
namespace app\admin\validate;
use think\validate;

class Adminuser extends validate{

	/**
	 *@description 验证器规则
	 *@buildcode(true)
	*/
	protected $rule = [
		'user'=>['require'],
		'pwd'=>['require'],
		'role_id'=>['require'],
	];

	/**
	 *@description 错误提示
	 *@buildcode(true)
	*/
	protected $message = [
		'user.require'=>'用户名不能为空',
		'pwd.require'=>'密码不能为空',
		'role_id.require'=>'所属角色不能为空',
	];

	/**
	 *@description 验证场景
	 *@buildcode(true)
	*/
	protected $scene  = [
		'add'=>['user','pwd','role_id'],
		'update'=>['user','role_id'],
	];



}