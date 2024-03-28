<?php
namespace app\admin\model;
use think\Model;

class Baseconfig extends Model{

	/**
	 *@description 链接库
	 *@buildcode(true)
	*/
	protected $connection = 'mysql';

	/**
	 *@description 主键
	 *@buildcode(true)
	*/
	protected $pk = 'id';

	/**
	 *@description 数据表
	 *@buildcode(true)
	*/
	protected $name = 'base_config';



}