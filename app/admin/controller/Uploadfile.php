<?php
namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Uploadfile as UploadfileModel;
use think\facade\Db;

class Uploadfile extends Admin{



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

			$field = 'uploadfile_id,title,pic,pic_2,pics,file,files';
			$query = UploadfileModel::field($field);

			$param = $this->request->post();
			if(isset($param['uploadfile_id']) && !empty($param['uploadfile_id'])) {
				$query = $query->where("uploadfile_id", $param['uploadfile_id']);
			}
			if(isset($param['title']) && $param['title'] != null) {
				$query = $query->where("title", $param['title']);
			}
			$orderby = ($param['sort'] && $param['order']) ? $param['sort'].' '.$param['order'] : 'uploadfile_id desc';

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
		$postField = 'uploadfile_id,';
		$data = $this->request->only(explode(',',$postField),'post');

		if(!$data['uploadfile_id']){
			throw new ValidateException ('参数错误');
		}
		UploadfileModel::update($data);

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 添加
	 *@buildcode(true)
	*/
	public function add(){
		$postField = 'title,pic,pic_2,pics,file,files';
		$data = $this->request->only(explode(',',$postField),'post');

		$validate = new \app\admin\validate\Uploadfile;
		if(!$validate->scene('add')->check($data)){
			throw new ValidateException ($validate->getError());
		}

		$data['pics'] = getItemData($data['pics']);
		$data['files'] = getItemData($data['files']);

		try{
			$res = UploadfileModel::insertGetId($data);
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
		$postField = 'uploadfile_id,title,pic,pic_2,pics,file,files';
		$data = $this->request->only(explode(',',$postField),'post');

		$validate = new \app\admin\validate\Uploadfile;
		if(!$validate->scene('update')->check($data)){
			throw new ValidateException ($validate->getError());
		}

		$data['pics'] = getItemData($data['pics']);
		$data['files'] = getItemData($data['files']);

		try{
			UploadfileModel::update($data);
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
		$id =  $this->request->post('uploadfile_id', '', 'serach_in');
		if(!$id){
			throw new ValidateException ('参数错误');
		}
		$field = 'uploadfile_id,title,pic,pic_2,pics,file,files';

		$res = UploadfileModel::field($field)->findOrEmpty($id);
		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		$res['pics'] = !empty($res['pics']) ? json_decode($res['pics'],true) : [];
		$res['files'] = !empty($res['files']) ? json_decode($res['files'],true) : [];

		return json(['status'=>200,'data'=>$res]);
	}


	/**
	 *@description 删除
	 *@buildcode(true)
	*/
	function delete(){
		$idx =  $this->request->post('uploadfile_id', '', 'serach_in');
		if(!$idx){
			throw new ValidateException ('参数错误');
		}
		UploadfileModel::destroy(['uploadfile_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 查看详情
	 *@buildcode(true)
	*/
	function detail(){
		$id =  $this->request->post('uploadfile_id', '', 'serach_in');
		if(!$id){
			throw new ValidateException ('参数错误');
		}
		$field = 'uploadfile_id,title,pic,pic_2,pics,file,files';
		$res = UploadfileModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


}