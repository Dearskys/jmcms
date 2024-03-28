<?php
namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Member as MemberModel;
use think\facade\Db;

class Member extends Admin{



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
			$orderby = ($param['sort'] && $param['order']) ? $param['sort'].' '.$param['order'] : 'member_id desc';

			$res =$query->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			$data['sum_amount'] = $query->sum('amount');
			return json($data);
		}
	}


	/**
	 *@description 修改排序开关
	 *@buildcode(true)
	*/
	function updateExt(){
		$postField = 'member_id,status';
		$data = $this->request->only(explode(',',$postField),'post');

		if(!$data['member_id']){
			throw new ValidateException ('参数错误');
		}
		MemberModel::update($data);

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 添加
	 *@buildcode(true)
	*/
	public function add(){
		$postField = 'username,sex,pic,mobile,email,password,amount,status,create_time,ssq';
		$data = $this->request->only(explode(',',$postField),'post');

		$validate = new \app\admin\validate\Member;
		if(!$validate->scene('add')->check($data)){
			throw new ValidateException ($validate->getError());
		}

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

		$validate = new \app\admin\validate\Member;
		if(!$validate->scene('update')->check($data)){
			throw new ValidateException ($validate->getError());
		}

		$data['create_time'] = !empty($data['create_time']) ? strtotime($data['create_time']) : '';
		$data['ssq'] = is_array($data['ssq']) ? implode('-',$data['ssq']) : '';

		try{
			MemberModel::update($data);
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
		$id =  $this->request->post('member_id', '', 'serach_in');
		if(!$id){
			throw new ValidateException ('参数错误');
		}
		$field = 'member_id,username,sex,pic,mobile,email,amount,status,create_time,ssq';

		$res = MemberModel::field($field)->findOrEmpty($id);
		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		$res['ssq'] = !empty($res['ssq']) ? explode('-',$res['ssq']) : '';

		return json(['status'=>200,'data'=>$res]);
	}


	/**
	 *@description 删除
	 *@buildcode(true)
	*/
	function delete(){
		$idx =  $this->request->post('member_id', '', 'serach_in');
		if(!$idx){
			throw new ValidateException ('参数错误');
		}
		MemberModel::destroy(['member_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 查看详情
	 *@buildcode(true)
	*/
	function detail(){
		$id =  $this->request->post('member_id', '', 'serach_in');
		if(!$id){
			throw new ValidateException ('参数错误');
		}
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
		$data = $this->request->only(explode(',',$postField),'post');

		if(empty($data['member_id'])){
			throw new ValidateException ('参数错误');
		}
		if(empty($data['password'])){
			throw new ValidateException ('密码不能为空');
		}

		$data['password'] = md5($data['password'].config('my.password_secrect'));
		MemberModel::update($data);

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/**
	 *@description 导入
	 *@buildcode(true)
	*/
	public function importData(){
		$data = $this->request->post();
		$list = [];
		foreach($data as $key=>$val){
			$list[$key]['username'] = $val['用户名'];
			$list[$key]['sex'] = getValByKey($val['性别'],'[{"key":"男","val":"1","label_color":"primary"},{"key":"女","val":"2","label_color":"warning"}]');
			$list[$key]['pic'] = $val['头像'];
			$list[$key]['mobile'] = $val['手机号'];
			$list[$key]['email'] = $val['邮箱'];
			$list[$key]['password'] = $val['密码'] ? md5($val['密码']) : '';
			$list[$key]['amount'] = $val['积分'];
			$list[$key]['status'] = getValByKey($val['状态'],'[{"key":"开启","val":"1"},{"key":"关闭","val":"0"}]');
			$list[$key]['create_time'] = time();
			$list[$key]['ssq'] = $val['省市区'];
		}
		(new MemberModel)->insertAll($list);
		return json(['status'=>200]);
	}


	/**
	 *@description 导出
	 *@buildcode(true)
	*/
	function dumpdata(){
		$page = $this->request->param('page', 1, 'intval');
		$limit = config('my.dumpsize') ? config('my.dumpsize') : 1000;

		$state = $this->request->param('state');
		$searchField = 'username,sex,mobile,email,status,create_time,ssq,order,sort';
		$param = $this->request->only(explode(',',$searchField));

		$field = 'username,sex,pic,mobile,email,password,amount,status,create_time,ssq';

		$query = MemberModel::field($field);

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
		$orderby = ($param['sort'] && $param['order']) ? $param['sort'].' '.$param['order'] : 'member_id desc';

		$res =$query->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		foreach($res['data'] as $key=>$val){
			$res['data'][$key]['sex'] = getItemVal($val['sex'],'[{"key":"男","val":"1","label_color":"primary"},{"key":"女","val":"2","label_color":"warning"}]');
			$res['data'][$key]['status'] = getItemVal($val['status'],'[{"key":"开启","val":"1"},{"key":"关闭","val":"0"}]');
			$res['data'][$key]['create_time'] = !empty($val['create_time']) ? date('Y-m-d H:i:s',$val['create_time']) : '';
			unset($res['data'][$key]['member_id']);
		}

		$data['status'] = 200;
		$data['header'] = explode(',','用户名,性别,头像,手机号,邮箱,密码,积分,状态,创建时间,省市区');
		$data['percentage'] = ceil($page * 100/ceil($res['total']/$limit));
		$data['filename'] = '会员管理.'.config('my.dump_extension');
		$data['data'] = $res['data'];
		return json($data);
	}


}