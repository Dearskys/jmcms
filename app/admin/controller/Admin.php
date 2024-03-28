<?php

namespace app\admin\controller;
use think\exception\FuncNotFoundException;
use think\exception\ValidateException;
use app\BaseController;
use think\facade\Db;


class Admin extends BaseController
{
	
	
	protected function initialize(){
		$controller = $this->request->controller();
		$action = $this->request->action();
		$app = app('http')->getName();
		
		$admin = session('admin');
        $userid = session('admin_sign') == data_auth_sign($admin) ? $admin['user_id'] : 0;
		
        if(!$userid && ($app <> 'admin' || $controller <> 'Login')){
			echo '<script type="text/javascript">top.parent.frames.location.href="'.url('admin/Login/index').'";</script>';exit();
        }
		
		$url =  "/{$app}/{$controller}/{$action}.html";
		if(session('admin.role_id') <> 1 && !in_array($url,config('my.nocheck'))  && !in_array($action,['getExtends','getInfo','getFieldList'])){	
			if(!in_array($url,session('admin.access'))){
				throw new ValidateException ('你没操作权限');
			}	
		}
		
		event('DoLog',session('admin.username'));	//写入操作日志
		
		$list = Db::name('base_config')->cache(true,60)->select()->column('data','name');
		config($list,'base_config');
	}
	
	
	//验证器 并且抛出异常
//	protected function validate($data,$validate){
//		try{
//			validate($validate)->scene($this->request->action())->check($data);
//		}catch(ValidateException $e){
//			throw new ValidateException ($e->getError());
//		}
//		return true;
//	}
	
	//格式化sql字段查询 转化为 key=>val 结构
	protected function query($sql,$connect='mysql'){
		preg_match_all('/select(.*)from/iUs',$sql,$all);
		if(!empty($all[1][0])){
			$sqlvalue = explode(',',trim($all[1][0]));
		}
		if(strpos($sql,'tkey') !== false){
			$sqlvalue[1] = 'tkey';
		}
		
		if(strpos($sql,'tval') !== false){
			$sqlvalue[0] = 'tval';
		}
		$sql = str_replace('pre_',config('database.connections.'.$connect.'.prefix'),$sql);
		$list = Db::connect($connect)->query($sql);
		$array = [];
		foreach($list as $k=>$v){
			$array[$k]['key'] = $v[trim($sqlvalue[1])];
			$array[$k]['val'] = $v[$sqlvalue[0]];
			if($sqlvalue[2]){
				$array[$k]['pid'] = $v[trim($sqlvalue[2])];
			}
		}
		return $array;
	}
	
	
	//将带有下拉分页的格式化为前端匹配的数据格式
	protected function getSelectPageData($sql,$where,$limit,$connect='mysql'){
		preg_match_all('/select(.*)from/iUs',$sql,$all);
		if(!empty($all[1][0])){
			$sqlvalue = explode(',',trim($all[1][0]));
		}
		
		$res = loadList($sql,$where,$limit,'',$connect);
		
		if(strpos($sql,'tkey') !== false){
			$sqlvalue[1] = 'tkey';
		}
		
		if(strpos($sql,'tval') !== false){
			$sqlvalue[0] = 'tval';
		}
		
		$array = [];
		foreach($res['data'] as $k=>$v){
			$array[$k]['key'] = $v[trim($sqlvalue[1])];
			$array[$k]['val'] = $v[trim($sqlvalue[0])];
		}
		
		$data['data'] = $array;
		$data['total'] = $res['total'];
		
		return $data;
	}
	
	
	
	public function __call($method, $args){
        throw new FuncNotFoundException('方法不存在',$method);
    }
	
	
	
}
