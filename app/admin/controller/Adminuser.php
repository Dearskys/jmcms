<?php
namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Adminuser as AdminuserModel;
use think\facade\Db;

class Adminuser extends Admin{



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

			$field = 'user_id,name,user,note,status,create_time';
			$query = AdminuserModel::field($field);

			$param = $this->request->post();
			if(isset($param['user_id']) && !empty($param['user_id'])) {
				$query = $query->where("adminuser.user_id", $param['user_id']);
			}
			if(isset($param['name']) && $param['name'] != null) {
				$query = $query->where("adminuser.name", $param['name']);
			}
			if(isset($param['user']) && $param['user'] != null) {
				$query = $query->where("adminuser.user", $param['user']);
			}
			if(isset($param['role_id']) && $param['role_id'] != null) {
				$query = $query->where("adminuser.role_id", $param['role_id']);
			}
			if(isset($param['status']) && $param['status'] != null) {
				$query = $query->where("adminuser.status", $param['status']);
			}
			$withJoin = [
				'role'=>explode(',','name'),
			];
			$query = $query->withJoin($withJoin,'left');

			$orderby = ($param['sort'] && $param['order']) ? $param['sort'].' '.$param['order'] : 'user_id desc';

			$res =$query->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('role_id');
			return json($data);
		}
	}


	/**
	 *@description 修改排序开关
	 *@buildcode(true)
	*/
	function updateExt(){
		$postField = 'user_id,status';
		$data = $this->request->only(explode(',',$postField),'post');

		if(!$data['user_id']){
			throw new ValidateException ('参数错误');
		}
		AdminuserModel::update($data);

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 添加
	 *@buildcode(true)
	*/
	public function add(){
		$postField = 'name,user,pwd,role_id,note,status,create_time';
		$data = $this->request->only(explode(',',$postField),'post');

		$validate = new \app\admin\validate\Adminuser;
		if(!$validate->scene('add')->check($data)){
			throw new ValidateException ($validate->getError());
		}

		$data['pwd'] = md5($data['pwd'].config('my.password_secrect'));
		$data['create_time'] = time();

		try{
			$res = AdminuserModel::insertGetId($data);
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
		$postField = 'user_id,name,user,role_id,note,status,create_time';
		$data = $this->request->only(explode(',',$postField),'post');

		$validate = new \app\admin\validate\Adminuser;
		if(!$validate->scene('update')->check($data)){
			throw new ValidateException ($validate->getError());
		}

		$data['create_time'] = !empty($data['create_time']) ? strtotime($data['create_time']) : '';

		try{
			AdminuserModel::update($data);
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
		$id =  $this->request->post('user_id', '', 'serach_in');
		if(!$id){
			throw new ValidateException ('参数错误');
		}
		$field = 'user_id,name,user,role_id,note,status,create_time';

		$res = AdminuserModel::field($field)->findOrEmpty($id);
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
		$idx =  $this->request->post('user_id', '', 'serach_in');
		if(!$idx){
			throw new ValidateException ('参数错误');
		}

		if($ret = hook('app/admin/hook/Adminuser@beforDelete',$idx)){
			return $ret;
		}

		AdminuserModel::destroy(['user_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 查看详情
	 *@buildcode(true)
	*/
	function detail(){
		$id =  $this->request->post('user_id', '', 'serach_in');
		if(!$id){
			throw new ValidateException ('参数错误');
		}
		$field = 'user_id,name,user,note,status,create_time';
		$res = AdminuserModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/**
	 *@description 重置密码
	 *@buildcode(true)
	*/
	public function resetPwd(){
		$postField = 'user_id,pwd';
		$data = $this->request->only(explode(',',$postField),'post');

		if(empty($data['user_id'])){
			throw new ValidateException ('参数错误');
		}
		if(empty($data['pwd'])){
			throw new ValidateException ('密码不能为空');
		}

		$data['pwd'] = md5($data['pwd'].config('my.password_secrect'));
		AdminuserModel::update($data);

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 获取定义sql语句的字段信息
	 *@buildcode(true)
	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('role_id')]);
	}


	/**
	 *@description 获取定义sql语句的字段信息
	 *@buildcode(true)
	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('role_id',explode(',',$list))){
			$data['role_ids'] = $this->query("select role_id,name from pre_role",'mysql');
		}
		return $data;
	}


}