<?php
namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Other as OtherModel;
use think\facade\Db;

class Other extends Admin{



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

			$field = 'other_id,title,jsq,tags,hk,color,ssq,rate';
			$query = OtherModel::field($field);

			$param = $this->request->post();
			if(isset($param['other_id']) && !empty($param['other_id'])) {
				$query = $query->where("other_id", $param['other_id']);
			}
			if(isset($param['title']) && $param['title'] != null) {
				$query = $query->where("title", $param['title']);
			}
			if(isset($param['ssq']) && $param['ssq'] != null) {
				$query = $query->whereLike("ssq","%".implode('-',$param['ssq'])."%");
			}
			$orderby = ($param['sort'] && $param['order']) ? $param['sort'].' '.$param['order'] : 'other_id desc';

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
		$postField = 'other_id,';
		$data = $this->request->only(explode(',',$postField),'post');

		if(!$data['other_id']){
			throw new ValidateException ('参数错误');
		}
		OtherModel::update($data);

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 添加
	 *@buildcode(true)
	*/
	public function add(){
		$postField = 'title,jsq,tags,hk,color,jzd,ssq,rate';
		$data = $this->request->only(explode(',',$postField),'post');

		$validate = new \app\admin\validate\Other;
		if(!$validate->scene('add')->check($data)){
			throw new ValidateException ($validate->getError());
		}

		$data['tags'] = implode(',',$data['tags']);
		$data['jzd'] = getItemData($data['jzd']);
		$data['ssq'] = is_array($data['ssq']) ? implode('-',$data['ssq']) : '';

		try{
			$res = OtherModel::insertGetId($data);
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
		$postField = 'other_id,title,jsq,tags,hk,color,jzd,ssq,rate';
		$data = $this->request->only(explode(',',$postField),'post');

		$validate = new \app\admin\validate\Other;
		if(!$validate->scene('update')->check($data)){
			throw new ValidateException ($validate->getError());
		}

		$data['tags'] = implode(',',$data['tags']);
		$data['jzd'] = getItemData($data['jzd']);
		$data['ssq'] = is_array($data['ssq']) ? implode('-',$data['ssq']) : '';

		try{
			OtherModel::update($data);
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
		$id =  $this->request->post('other_id', '', 'serach_in');
		if(!$id){
			throw new ValidateException ('参数错误');
		}
		$field = 'other_id,title,jsq,tags,hk,color,jzd,ssq,rate';

		$res = OtherModel::field($field)->findOrEmpty($id);
		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		$res['tags'] = !empty($res['tags']) ? explode(',',$res['tags']) : '';
		$res['jzd'] = !empty($res['jzd']) ? json_decode($res['jzd'],true) : [];
		$res['ssq'] = !empty($res['ssq']) ? explode('-',$res['ssq']) : '';

		return json(['status'=>200,'data'=>$res]);
	}


	/**
	 *@description 删除
	 *@buildcode(true)
	*/
	function delete(){
		$idx =  $this->request->post('other_id', '', 'serach_in');
		if(!$idx){
			throw new ValidateException ('参数错误');
		}
		OtherModel::destroy(['other_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 查看详情
	 *@buildcode(true)
	*/
	function detail(){
		$id =  $this->request->post('other_id', '', 'serach_in');
		if(!$id){
			throw new ValidateException ('参数错误');
		}
		$field = 'other_id,title,jsq,tags,hk,color,ssq,rate';
		$res = OtherModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


}