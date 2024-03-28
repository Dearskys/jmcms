<?php
namespace app\api\controller;
use think\exception\ValidateException;
use app\api\model\Member as MemberModel;
use app\api\controller\Common;
use think\facade\Db;

class Member extends Common{



	/**
	 *@description 数据列表
	 *@buildcode(true)
	*/
	function index(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$field = 'member_id,username,sex,pic,mobile,email,amount,status,create_time,ssq';
		$query = MemberModel::field($field);

		$param = $this->request->post();
		if(isset($param['member_id']) && !empty($param['member_id'])) {
			$query = $query->where("member_id", $param['member_id']);
		}
		if(isset($param['username']) && $param['username'] != null) {
			$query = $query->whereLike("username", "%".$param['username']."%");
		}
		if(isset($param['sex']) && $param['sex'] != null) {
			$query = $query->where("sex", $param['sex']);
		}
		if(isset($param['mobile']) && $param['mobile'] != null) {
			$query = $query->where("mobile", $param['mobile']);
		}
		if(isset($param['email']) && $param['email'] != null) {
			$query = $query->where("email", $param['email']);
		}
		if(isset($param['status']) && $param['status'] != null) {
			$query = $query->where("status", $param['status']);
		}
		if(isset($param['create_time']) && $param['create_time'] != null) {
			$query = $query->whereBetween('create_time', [strtotime($param['create_time'][0]),strtotime($param['create_time'][1])]);
		}
		if(isset($param['ssq']) && $param['ssq'] != null) {
			$query = $query->whereLike("ssq","%".implode('-',$param['ssq'])."%");
		}
		$query = $query->order('member_id desc');

		$res =$query->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		$data['status'] = 200;
		$data['data'] = $res;
		return json($data);
	}


	/**
	 *@description 添加
	 *@buildcode(true)
	*/
	public function add(){
		$postField = 'username,sex,pic,mobile,email,password,amount,status,create_time,ssq';
		$data = $this->request->only(explode(',',$postField),'post');

		$this->validate($data,\app\api\validate\Member::class);

		$data['password'] = md5($data['password'].config('my.password_secrect'));
		$data['create_time'] = time();
		$data['ssq'] = is_array($data['ssq']) ? implode('-',$data['ssq']) : '';

		try{
			$res = MemberModel::insertGetId($data);
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
		$postField = 'member_id,username,sex,pic,mobile,email,amount,status,create_time,ssq';
		$data = $this->request->only(explode(',',$postField),'post');

		$this->validate($data,\app\api\validate\Member::class);

		$data['create_time'] = !empty($data['create_time']) ? strtotime($data['create_time']) : '';
		$data['ssq'] = is_array($data['ssq']) ? implode('-',$data['ssq']) : '';

		try{
			MemberModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200]);
	}


	/**
	 *@description 删除
	 *@buildcode(true)
	*/
	function delete(){
		$idx =  $this->request->post('member_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		MemberModel::destroy(['member_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 查看详情
	 *@buildcode(true)
	*/
	function detail(){
		$id =  $this->request->post('member_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'member_id,username,sex,pic,mobile,email,amount,status,create_time,ssq';
		$res = MemberModel::field($field)->findOrEmpty($id);

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
		$postField = 'member_id,password';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(empty($data['member_id'])) throw new ValidateException ('参数错误');
		if(empty($data['password'])) throw new ValidateException ('密码不能为空');

		$data['password'] = md5($data['password'].config('my.password_secrect'));
		$res = MemberModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 发送验证码
	 *@buildcode(true)
	*/
	public function sendSms(){
		$mobile = $this->request->post('mobile');
		if(empty($mobile)) throw new ValidateException ('手机号不能为空');
		if(!preg_match('/^1[3456789]\d{9}$/',$mobile)) throw new ValidateException ('手机号格式错误');

		$data['mobile']	= $mobile;	//发送手机号
		$data['code']	= sprintf('%06d', rand(0,999999));		//验证码

		try{
			$res = \utils\sms\AliSmsService::sendSms($data);
		}catch(\Exception $e){
			throw new ValidateException ($e->getMessage());
		}
		$key = md5(time().$data['mobile']);
		cache($key,['mobile'=>$data['mobile'],'code'=>$data['code']],300);
		return json(['status'=>200,'msg'=>'发送成功','key'=>$key]);
	}


	/**
	 *@description 用户登录
	 *@buildcode(true)
	*/
	public function login(){
		$postField = 'username,password';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(empty($data['username'])) throw new ValidateException ('用户名不能为空');
		if(empty($data['password'])) throw new ValidateException ('密码不能为空');

		$fields = 'member_id,username,sex,pic,mobile';

		$where['username'] = $data['username'];
		$where['password'] = md5($data['password'].config('my.password_secrect'));

		$res = MemberModel::field($fields)->where($where)->findOrEmpty();
		if($res->isEmpty()){
			throw new ValidateException ('账号或密码错误');
		}
		return json(['status'=>200,'data'=>$res,'token'=>$this->setToken(json_encode($res))]);
	}


	/**
	 *@description 用户登录
	 *@buildcode(true)
	*/
	public function logina(){
		$postField = 'mobile';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(empty($data['mobile'])) throw new ValidateException ('手机号不能为空');

		$fields = 'member_id,mobile,pic,sex,username';

		$where['mobile'] = $data['mobile'];

		$res = MemberModel::field($fields)->where($where)->findOrEmpty();
		if($res->isEmpty()){
			throw new ValidateException ('登录失败，请检查手机号');
		}
		return json(['status'=>200,'data'=>$res,'token'=>$this->setToken(json_encode($res))]);
	}


}