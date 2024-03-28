<?php
namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Goods as GoodsModel;
use think\facade\Db;

class Goods extends Admin{



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

			$field = 'goods_id,goods_name,pic,sale_price,status,sortid,create_time';
			$query = GoodsModel::field($field);

			$param = $this->request->post();
			if(isset($param['goods_id']) && !empty($param['goods_id'])) {
				$query = $query->where("goods.goods_id", $param['goods_id']);
			}
			if(isset($param['goods_name']) && $param['goods_name'] != null) {
				$query = $query->whereLike("goods.goods_name", "%".$param['goods_name']."%");
			}
			if(isset($param['class_id']) && $param['class_id'] != null) {
				$query = $query->where("goods.class_id", $param['class_id']);
			}
			if(isset($param['status']) && $param['status'] != null) {
				$query = $query->where("goods.status", $param['status']);
			}
			if(isset($param['cd']) && $param['cd'] != null) {
				$query = $query->whereLike("goods.cd", "%".$param['cd']."%");
			}
			if(isset($param['create_time']) && $param['create_time'] != null) {
				$query = $query->whereBetween('goods.create_time', [strtotime($param['create_time'][0]),strtotime($param['create_time'][1])]);
			}
			$withJoin = [
				'goodscata'=>explode(',','class_name'),
			];
			$query = $query->withJoin($withJoin,'left');

			$orderby = ($param['sort'] && $param['order']) ? $param['sort'].' '.$param['order'] : 'goods_id desc';

			$res =$query->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('class_id');
			return json($data);
		}
	}


	/**
	 *@description 修改排序开关
	 *@buildcode(true)
	*/
	function updateExt(){
		$postField = 'goods_id,status,sortid';
		$data = $this->request->only(explode(',',$postField),'post');

		if(!$data['goods_id']){
			throw new ValidateException ('参数错误');
		}
		GoodsModel::update($data);

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 添加
	 *@buildcode(true)
	*/
	public function add(){
		$postField = 'goods_name,class_id,pic,sale_price,images,status,cd,store,sortid,create_time,detail';
		$data = $this->request->only(explode(',',$postField),'post');

		$validate = new \app\admin\validate\Goods;
		if(!$validate->scene('add')->check($data)){
			throw new ValidateException ($validate->getError());
		}

		$data['images'] = getItemData($data['images']);
		$data['create_time'] = !empty($data['create_time']) ? strtotime($data['create_time']) : '';

		try{
			$res = GoodsModel::insertGetId($data);
			if($res && empty($data['sortid'])){
				GoodsModel::update(['sortid'=>$res,'goods_id'=>$res]);
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
		$postField = 'goods_id,goods_name,class_id,pic,sale_price,images,status,cd,store,sortid,create_time,detail';
		$data = $this->request->only(explode(',',$postField),'post');

		$validate = new \app\admin\validate\Goods;
		if(!$validate->scene('update')->check($data)){
			throw new ValidateException ($validate->getError());
		}


		if(!isset($data['class_id'])){
			$data['class_id'] = null;
		}
		$data['images'] = getItemData($data['images']);
		$data['create_time'] = !empty($data['create_time']) ? strtotime($data['create_time']) : '';

		try{
			GoodsModel::update($data);
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
		$id =  $this->request->post('goods_id', '', 'serach_in');
		if(!$id){
			throw new ValidateException ('参数错误');
		}
		$field = 'goods_id,goods_name,class_id,pic,sale_price,images,status,cd,store,sortid,create_time,detail';

		$res = GoodsModel::field($field)->findOrEmpty($id);
		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		$res['images'] = !empty($res['images']) ? json_decode($res['images'],true) : [];

		return json(['status'=>200,'data'=>$res]);
	}


	/**
	 *@description 删除
	 *@buildcode(true)
	*/
	function delete(){
		$idx =  $this->request->post('goods_id', '', 'serach_in');
		if(!$idx){
			throw new ValidateException ('参数错误');
		}
		GoodsModel::destroy(['goods_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 查看详情
	 *@buildcode(true)
	*/
	function detail(){
		$id =  $this->request->post('goods_id', '', 'serach_in');
		if(!$id){
			throw new ValidateException ('参数错误');
		}
		$field = 'goods_id,goods_name,pic,sale_price,status,sortid,create_time';
		$res = GoodsModel::field($field)->findOrEmpty($id);

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
		return json(['status'=>200,'data'=>$this->getSqlField('class_id')]);
	}


	/**
	 *@description 获取定义sql语句的字段信息
	 *@buildcode(true)
	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('class_id',explode(',',$list))){
			$data['class_ids'] = _generateSelectTree($this->query("select class_id,class_name,pid from ".config('database.connections.mysql.prefix')."goods_cata",'mysql'));
		}
		return $data;
	}


}