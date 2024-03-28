<?php 
namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Files as FileModel;
use app\admin\model\Adminuser as AdminuserModel;
use think\facade\Db;

class Base extends Admin {

	
	/*
 	* @Description 左侧菜单
 	*/
	public function getMenu(){
		$menu =  $this->getBaseMenus();
		$order_array = array_column($menu, 'sortid');			//数组排序 根据sortid 正序
		array_multisort($order_array,SORT_ASC,$menu );
		
		$mymenu = $this->getMyMenus(session('admin'),$menu);
		return json(['status'=>200,'data'=>$mymenu]);
	}
	
	
	//获取当前角色的菜单
	private function getMyMenus($roleInfo,$totalMenus){		
		if($roleInfo['role_id'] == 1){
			return $totalMenus;
		}
		$tree = [];
		foreach($totalMenus as $key=>$val){
			if(in_array($val['url'],$roleInfo['access'])){
				$tree[] = array_merge($val,['children'=>$this->getMyMenus($roleInfo,$val['children'])]);
			}
		}
		return array_values($tree);
	}
	
	/*
 	* @Description 图片管理列表
 	*/
	function fileList(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$field = 'id,filepath,hash,create_time';

		$res = FileModel::where('type',1)->field($field)->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		$data['status'] = 200;
		$data['data'] = $res;
		return json($data);
	}


	/*
 	* @Description  删除图片
 	*/
	function deleteFile(){
		$filepath =  $this->request->post('filepath', '', 'serach_in');
		if(!$filepath) $this->error('请选择图片');
		
		event('DeleteFile',$filepath);	//删除文件物理路径
		
		FileModel::where('filepath','in',$filepath)->delete();
		
		return json(['status'=>200,'msg'=>'操作成功']);
	}
	
	/*
 	* @Description  重置密码
 	*/
	public function resetPwd(){
		$password = $this->request->post('password');
		
		if(empty($password)) $this->error('密码不能为空');
		
		$data['user_id'] = session('admin.user_id');
		$data['pwd'] = md5($password.config('my.password_secrect'));
		
		$res = AdminuserModel::update($data);
		
		return json(['status'=>200,'msg'=>'操作成功']);
	}
	
	
	public function getRoleMenus(){
		$menu = $this->getNodeMenus(0);
		$order_array = array_column($menu, 'sortid');			//数组排序 根据sortid 正序
		array_multisort($order_array,SORT_ASC,$menu );

		return json(['status'=>200,'menus'=>$menu]);
	}
	
	
	protected function getBaseMenus(){
		$field = 'node_id,pid,title,status,icon,sortid,path';
		$list = Db::name('node')->field($field)->where('status',1)->where('type',1)->order('sortid asc')->select()->toArray();
		if($list){
			foreach($list as $key=>$val){
				$menus[$key]['node_id'] = $val['node_id'];
				$menus[$key]['pid'] = $val['pid'];
				$menus[$key]['title'] = $val['title'];
				$menus[$key]['sortid'] = $val['sortid'];
				$menus[$key]['icon'] = $val['icon'] ? $val['icon'] : 'el-icon-menu';
				$menus[$key]['url'] = $val['path'];
			}
			return _generateListTree($menus,0,['node_id','pid']);
		}
	}
	
	
	//权限系统获取菜单
	private function getNodeMenus($pid){
		$field = 'node_id,title,pid,status,icon,sortid,path';
		$list = Db::name('node')->field($field)->where('pid',$pid)->order('sortid asc')->select()->toArray();
		if($list){
			foreach($list as $key=>$val){
				$menus[$key]['sortid'] = $val['sortid'];
				$menus[$key]['access'] = $val['path'];
				$menus[$key]['title'] = $val['title'];
				$sublist = Db::name('node')->field($field)->where('pid',$val['node_id'])->order('sortid asc')->select()->toArray();
				if($sublist){
					$menus[$key]['children'] = $this->getNodeMenus($val['node_id']);
				}
			}
			return array_values($menus);
		}
	}



}

