<?php
namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Node as NodeModel;
use think\facade\Db;

class Node extends Admin{



	/**
	 *@description 数据列表
	 *@buildcode(true)
	*/
	function index(){
		if (!$this->request->isPost()){
			return view('index');
		}else{
			$limit  = $this->request->post('limit', 20, 'intval');
			$page = $this->request->post('page', 1, 'intval');

			$field = 'node_id,title,type,path,status,icon,sortid,pid';
			$query = NodeModel::field($field);

			$param = $this->request->post();
			if(isset($param['node_id']) && !empty($param['node_id'])) {
				$query = $query->where("node_id", $param['node_id']);
			}
			$orderby = ($param['sort'] && $param['order']) ? $param['sort'].' '.$param['order'] : 'sortid asc';

			$res =$query->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$res['data'] = _generateListTree($res['data'],0,['node_id','pid']);

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('pid');
			return json($data);
		}
	}


	/**
	 *@description 修改排序开关
	 *@buildcode(true)
	*/
	function updateExt(){
		$postField = 'node_id,status,sortid';
		$data = $this->request->only(explode(',',$postField),'post');

		if(!$data['node_id']){
			throw new ValidateException ('参数错误');
		}
		NodeModel::update($data);

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 添加
	 *@buildcode(true)
	*/
	public function add(){
		$postField = 'pid,title,type,path,status,icon,sortid';
		$data = $this->request->only(explode(',',$postField),'post');

		$validate = new \app\admin\validate\Node;
		if(!$validate->scene('add')->check($data)){
			throw new ValidateException ($validate->getError());
		}

		try{
			$res = NodeModel::insertGetId($data);
			if($res && empty($data['sortid'])){
				NodeModel::update(['sortid'=>$res,'node_id'=>$res]);
			}
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/**
	 *@description 修改
	 *@buildcode(true)
	*/
	public function update(){
		$postField = 'node_id,pid,title,type,path,status,icon,sortid';
		$data = $this->request->only(explode(',',$postField),'post');

		$validate = new \app\admin\validate\Node;
		if(!$validate->scene('update')->check($data)){
			throw new ValidateException ($validate->getError());
		}


		if(!isset($data['pid'])){
			$data['pid'] = null;
		}

		try{
			NodeModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/**
	 *@description 修改信息之前查询信息
	 *@buildcode(true)
	*/
	function getUpdateInfo(){
		$id =  $this->request->post('node_id', '', 'serach_in');
		if(!$id){
			throw new ValidateException ('参数错误');
		}
		$field = 'node_id,pid,title,type,path,status,icon,sortid';

		$res = NodeModel::field($field)->findOrEmpty($id);
		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}


		return json(['status'=>200,'data'=>$res]);
	}


	/**
	 *@description 删除
	 *@buildcode(true)
	*/
	function delete(){
		$idx =  $this->request->post('node_id', '', 'serach_in');
		if(!$idx){
			throw new ValidateException ('参数错误');
		}
		NodeModel::destroy(['node_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 查看详情
	 *@buildcode(true)
	*/
	function detail(){
		$id =  $this->request->post('node_id', '', 'serach_in');
		if(!$id){
			throw new ValidateException ('参数错误');
		}
		$field = 'node_id,title,type,path,status,icon,sortid';
		$res = NodeModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/**
	 *@description 获取定义sql语句的字段信息
	 *@buildcode(true)
	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('pid')]);
	}


	/**
	 *@description 获取定义sql语句的字段信息
	 *@buildcode(true)
	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('pid',explode(',',$list))){
			$data['pids'] = _generateSelectTree($this->query("select node_id,title,pid from ".config('database.connections.mysql.prefix')."node",'mysql'));
		}
		return $data;
	}


}