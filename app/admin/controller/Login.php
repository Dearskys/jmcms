<?php
namespace app\admin\controller;
use think\exception\ValidateException;
use think\facade\Db;
use app\admin\model\Role as RoleModel;


class Login extends Admin{
	
	
	
	//用户登录 
    public function index(){
		if(!$this->request->isPost()) {
			return view('index');
		}else{
			$postField = 'username,password,verify,key';
			$data = $this->request->only(explode(',',$postField),'post');
			
			if(empty($data['username']) || empty($data['password'])){
				throw new ValidateException("用户名或者密码不能为空");
			}
			
			if (config('my.verify_status') && false === captcha_check($data['key'],$data['verify'])) {
				throw new ValidateException("验证码错误");
			}
			
			$where['a.user'] = trim($data['username']);
			$where['a.pwd']  = md5(trim($data['password']).config('my.password_secrect'));
			
			$info = Db::name('admin_user')->alias('a')->join('role b', 'a.role_id in(b.role_id)')->field('a.user_id,a.name,a.user as username,a.status,a.role_id as user_role_ids,b.role_id,b.name as role_name,b.status as role_status,b.access')->where($where)->find();
			
			if(!$info){
				throw new ValidateException("请检查用户名或者密码");
			}
			if(!($info['status']) || !($info['role_status'])){
				throw new ValidateException("该账户被禁用");
			}
			
			$info['access'] = explode(',',$info['access']);
			
			session('admin', $info);
			session('admin_sign', data_auth_sign($info));

			event('LoginLog',$data);	//写入登录日志
			
			return json(['status'=>200]);
		}
    }

	//验证码
	public function verify(){
		$data['data'] = captcha();
		$data['verify_status'] = config('my.verify_status',true);	//验证码开关
		$data['status'] = 200;
	    return json($data);
	}
	

	//退出
    public function logout(){
        session('admin', null);
		session('admin_sign', null);
	    return json(['status'=>200]);
    }
	
	
	//阿里云oss上传异步回调返回上传路径，放到这是因为这个地址必须外部能直接访问到
	function aliOssCallBack(){
		$body = file_get_contents('php://input');
		header("Content-Type: application/json");
		$url = $this->getendpoint(config('my.ali_oss_endpoint')).'/'.str_replace('%2F','/',$body);
        return json(['code'=>1,'data'=>$url]);
	}
	
	
	//获取阿里云oss客户端上传地址
	private function getendpoint($str){
		if(strpos(config('my.ali_oss_endpoint'),'aliyuncs.com') !== false){
			if(strpos($str,'https') !== false){
				$point = 'https://'.config('my.ali_oss_bucket').'.'.substr($str,8);
			}else{
				$point = 'http://'.config('my.ali_oss_bucket').'.'.substr($str,7);
			}	
		}else{
			$point = config('my.ali_oss_endpoint');
		}
		return $point;
	}
	

}
