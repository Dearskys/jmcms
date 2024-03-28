<?php
namespace app\api\model;
use think\Model;

class Member extends Model{

	/**
	 *@description 链接库
	 *@buildcode(true)
	*/
	protected $connection = 'mysql';

	/**
	 *@description 主键
	 *@buildcode(true)
	*/
	protected $pk = 'member_id';

	/**
	 *@description 数据表
	 *@buildcode(true)
	*/
	protected $name = 'member';



}