<?php

namespace app\api\controller;
use think\App;
use think\facade\Log;
use think\exception\FuncNotFoundException;
use think\facade\Db;
use utils\Jwt;
use think\exception\ValidateException;


class Common
{
    
	protected $request;
    protected $app;
	
	protected $_data;
	
	protected $userInfo;
	
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
		//exit('禁止访问');
        $this->app     = $app;
        $this->request = $this->app->request;
		$this->_data = $this->request->param();
		
		//判断是否是json请求
		if(!$this->request->isJson()){
			$this->_data = $this->request->param();
		}else{
			$this->_data = json_decode(file_get_contents('php://input'),true);
		}
		
		$this->_data['timestamp'] = date('Y-m-d H:i:s', time());
		$this->userInfo = json_decode($this->request->userInfo,true);
		
		$list = Db::name('base_config')->cache(true,60)->select()->column('data','name');
		config($list,'base_config');
		
		if(config('my.api_input_log')){
			Log::info('接口地址：'.request()->pathinfo().',接口输入：'.print_r($this->_data,true));
		}
    }
	
	//验证器 并且抛出异常
	protected function validate($data,$validate){
		try{
			validate($validate)->scene($this->request->action())->check($data);
		}catch(ValidateException $e){
			throw new ValidateException ($e->getError());
		}
		return true;
	}
	
	 /**
     * 生成token
     * @param  userinfo 用户信息
     */
	protected function setToken($userinfo){
		$jwt = Jwt::getInstance();
		$jwt->setIss(config('my.jwt_iss'))->setAud(config('my.jwt_aud'))->setSecrect(config('my.jwt_secrect'))->setExpTime(config('my.jwt_expire_time'));
		$token = $jwt->setUid($userinfo)->encode()->getToken();
		return $token;
	}

	
	public function __call($method, $args){
        throw new FuncNotFoundException('方法不存在',$method);
    }
	
}
