<?php
namespace app\admin\model;
use think\Model;

class Goods extends Model{

	/**
	 *@description 链接库
	 *@buildcode(true)
	*/
	protected $connection = 'mysql';

	/**
	 *@description 主键
	 *@buildcode(true)
	*/
	protected $pk = 'goods_id';

	/**
	 *@description 数据表
	 *@buildcode(true)
	*/
	protected $name = 'goods';



	/**
	 *@description 关联模型
	 *@buildcode(true)
	*/
	function goodscata(){
		return $this->hasOne(\app\admin\model\Goodscata::class,'class_id','class_id');
	}


}