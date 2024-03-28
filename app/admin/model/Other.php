<?php
namespace app\admin\model;
use think\Model;

class Other extends Model{

	/**
	 *@description 链接库
	 *@buildcode(true)
	*/
	protected $connection = 'mysql';

	/**
	 *@description 主键
	 *@buildcode(true)
	*/
	protected $pk = 'other_id';

	/**
	 *@description 数据表
	 *@buildcode(true)
	*/
	protected $name = 'other';



}