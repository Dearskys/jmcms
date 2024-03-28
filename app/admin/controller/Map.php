<?php
namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Map as MapModel;
use think\facade\Db;

class Map extends Admin{



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

			$field = 'map_id,title,bddt,gddt,txdt';
			$query = MapModel::field($field);

			$param = $this->request->post();
			if(isset($param['map_id']) && !empty($param['map_id'])) {
				$query = $query->where("map_id", $param['map_id']);
			}
			if(isset($param['title']) && $param['title'] != null) {
				$query = $query->where("title", $param['title']);
			}
			$orderby = ($param['sort'] && $param['order']) ? $param['sort'].' '.$param['order'] : 'map_id desc';

			$res =$query->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			return json($data);
		}
	}


	/**
	 *@description 修改排序开关
	 *@buildcode(true)
	*/
	function updateExt(){
		$postField = 'map_id,';
		$data = $this->request->only(explode(',',$postField),'post');

		if(!$data['map_id']){
			throw new ValidateException ('参数错误');
		}
		MapModel::update($data);

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 添加
	 *@buildcode(true)
	*/
	public function add(){
		$postField = 'title,bddt,gddt,txdt';
		$data = $this->request->only(explode(',',$postField),'post');

		$validate = new \app\admin\validate\Map;
		if(!$validate->scene('add')->check($data)){
			throw new ValidateException ($validate->getError());
		}

		try{
			$res = MapModel::insertGetId($data);
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
		$postField = 'map_id,title,bddt,gddt,txdt';
		$data = $this->request->only(explode(',',$postField),'post');

		$validate = new \app\admin\validate\Map;
		if(!$validate->scene('update')->check($data)){
			throw new ValidateException ($validate->getError());
		}

		try{
			MapModel::update($data);
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
		$id =  $this->request->post('map_id', '', 'serach_in');
		if(!$id){
			throw new ValidateException ('参数错误');
		}
		$field = 'map_id,title,bddt,gddt,txdt';

		$res = MapModel::field($field)->findOrEmpty($id);
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
		$idx =  $this->request->post('map_id', '', 'serach_in');
		if(!$idx){
			throw new ValidateException ('参数错误');
		}
		MapModel::destroy(['map_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 查看详情
	 *@buildcode(true)
	*/
	function detail(){
		$id =  $this->request->post('map_id', '', 'serach_in');
		if(!$id){
			throw new ValidateException ('参数错误');
		}
		$field = 'map_id,title,bddt,gddt,txdt';
		$res = MapModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


}