<?php
namespace app\admin\validate;
use think\validate;

class Goods extends validate{

	/**
	 *@description 验证器规则
	 *@buildcode(true)
	*/
	protected $rule = [
		'goods_name'=>['require'],
	];

	/**
	 *@description 错误提示
	 *@buildcode(true)
	*/
	protected $message = [
		'goods_name.require'=>'商品名称不能为空',
	];

	/**
	 *@description 验证场景
	 *@buildcode(true)
	*/
	protected $scene  = [
		'add'=>['goods_name'],
		'update'=>['goods_name'],
	];



}