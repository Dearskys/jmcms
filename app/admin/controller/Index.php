<?php
namespace app\admin\controller;
use think\facade\Db;
use think\facade\Session;

class Index extends Admin {
	
	
	public function index(){
		return view('index');
	}
	
	
	//后台首页主体内容
	public function main(){
		if(!$this->request->isPost()){
			return view('main');
		}else{		
			//折线图数据
			$echat_data['day_count'] = [
				'title'=>'当月业绩折线图',
				'day'=>[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30],	//每月天数
				'data'=>[0,0,0,0,0,126,246,452,45,36,479,588,434,9,18,27,18,88,45,0,0,0,0,0,0,0,0,0,0,0]	//每天数据
			];
			
			if(config('my.show_home_chats',true)){
				$data['echat_data'] = $echat_data;
			}
			$data['card_data'] = $this->getCardData();
			$data['status'] = 200;
			return json($data);
		}
	}
	
	 
	//首页统计数据
	private function getCardData(){
		$card_data = [	//头部统计数据
			[
			  'title_icon'=>"el-icon-user",
			  'card_title'=> "访问",
			  'card_cycle'=> "年",
			  'card_cycle_back_color'=> "#409EFF",
			  'bottom_title'=> "访问总量",
			  'vist_num'=> rand(0,100000),
			  'vist_all_num'=> rand(0,100000),
			  'vist_all_icon'=> "el-icon-trophy",
			],
			[
			  'title_icon'=> "el-icon-download",
			  'card_title'=> "下载",
			  'card_cycle'=> "月",
			  'card_cycle_back_color'=> "#67C23A",
			  'bottom_title'=> "下载总量",
			  'vist_num'=> rand(0,100000),
			  'vist_all_num'=> rand(0,100000),
			  'vist_all_icon'=> "el-icon-download",
			],
			[
			  'title_icon'=> "el-icon-wallet",
			  'card_title'=> "收入",
			  'card_cycle'=> "日",
			  'card_cycle_back_color'=> "#F56C6C",
			  'bottom_title'=> "总收入",
			  'vist_num'=> rand(0,100000),
			  'vist_all_num'=> rand(0,100000),
			  'vist_all_icon'=> "el-icon-coin",
			],
			[
			  'title_icon'=> "el-icon-coordinate",
			  'card_title'=> "用户",
			  'card_cycle'=> "月",
			  'card_cycle_back_color'=> "#E6A23C",
			  'bottom_title'=> "总用户",
			  'vist_num'=> rand(0,100000),
			  'vist_all_num'=> rand(0,100000),
			  'vist_all_icon'=> "el-icon-data-line",
			],
		];
		
		return $card_data;
	}
	
	
	
	
}