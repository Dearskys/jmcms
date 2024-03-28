<?php 
namespace app\api\controller;
use think\exception\ValidateException;
use utils\wechart\UserService;
use EasyWeChat\Factory;
use think\facade\Log;

class Wechat extends Common{
	
	
	/**
	 * @description 公众号授权获取用户信息
	 * @apiParam (输入参数：) {string}			[url] 重定向地址
	 * @apiParam (输入参数：) {string}			[url] 重定向地址
	 */
	function officeLogin(){
		$url = $this->request->url(true);	//获取当前方法url地址 也就是授权重定向到当前方法
		$snsapi = 'snsapi_userinfo';
		try{
			$wxuser = \utils\wechart\UserService::officeAuth($url,$snsapi);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		print_r($wxuser);
	}
	
	/**
	 * @description 获取小程序openid
	 * @apiParam (输入参数：) {string}			[code] 小程序传入 
	 */
	function getOpenId(){
		$code = $this->request->post('code');
		try{
			$res = \utils\wechart\UserService::getOpenId($code);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		print_r($res['openid']);
	}
	
	/**
	 * @description 获取手机号,连同openid一起返回
	 * @apiParam (输入参数：) {string}			[code] 小程序传入 
	 * @apiParam (输入参数：) {string}			[iv] 小程序传入 
	 * @apiParam (输入参数：) {string}			[encryptedData] 小程序传入 
	 */
	function getMobile(){
		$postField = 'code,iv,encryptedData';
		$data = $this->request->only(explode(',',$postField),'post');
		try{
			$res = \utils\wechart\UserService::getMobile($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		print_r($res);
	}
	
	
	/**
	 * @description 生成公众号二维码 保存在 uploads/qrcode目录
	 * @apiParam (输入参数：) {string}			[scene] 二维码参数值
	 * @apiParam (输入参数：) {string}			[filename] 生成的文件名称
	 */
	function createOfficeQrcode(){
		$data['scene'] = 1;
		$data['filename'] = '123.png';
		try{
			$res = \utils\wechart\QrcodeService::createOfficeQrcode($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		print_r($res);
	}
	
	
	/**
	 * @description 生成小程序码 保存在 uploads/qrcode目录
	 * @apiParam (输入参数：) {string}			[width] 小程序码宽度
	 * @apiParam (输入参数：) {string}			[page] page页面地址，必须是真实的页面地址
	 * @apiParam (输入参数：) {string}			[scene] 二维码参数值
	 * @apiParam (输入参数：) {string}			[filename] 生成的文件名称
	 */
	function createMineQrcode(){
		$data['width'] = '600px';
		$data['page'] = 'pages/login/login'; 
		$data['scene'] = 1;
		$data['filename'] = '456.png';
		
		try{
			$res = \utils\wechart\QrcodeService::createMiniQrcode($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		print_r($res);
	}
	
	
	/**
	 * @description 微信支付jsapi模式  如果是小程序支付配置jsapiPay 方法配置参数 array_merge(config('my.mini_program'),config('my.wechart_pay')) ;公众支付配置 array_merge(config('my.official_accounts'),config('my.wechart_pay'))
	 * @apiParam (输入参数：) {string}			[body] 支付标题
	 * @apiParam (输入参数：) {string}			[out_trade_no] 支付订单号
	 * @apiParam (输入参数：) {int}				[total_fee] 支付金额 单位分
	 * @apiParam (输入参数：) {string}			[openid] 用户openid
	 * @apiParam (输入参数：) {string}			[attach] 其它参数，微信原样返回
	 * @apiParam (输入参数：) {string}			[notify_url] 支付回调地址
	 */
	function jsapiPay(){
		$data['body'] = '测试标题';
		$data['out_trade_no'] = doOrderSn(123);
		$data['total_fee'] = 1;
		$data['openid'] = 'oGXKT64A45BGrzyb55PXdAecmsYY';
		$data['attach'] = '';
		$data['notify_url'] = 'http://ysf.whpj.cc/api/Wechat/notify';
		
		try{
			$res = \utils\wechart\PayService::jsapiPay($data,array_merge(config('my.mini_program'),config('my.wechart_pay')));
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json($res);
	}
	
	
	/**
	 * @description 微信支付jsapi模式 ,返回一段url 需要将其生成二维码 才可发起支付
	 * @apiParam (输入参数：) {string}			[body] 支付标题
	 * @apiParam (输入参数：) {string}			[out_trade_no] 支付订单号
	 * @apiParam (输入参数：) {int}				[total_fee] 支付金额 单位分
	 * @apiParam (输入参数：) {string}			[attach] 其它参数，微信原样返回
	 * @apiParam (输入参数：) {string}			[notify_url] 支付回调地址
	 */
	function nativePay(){
		$data['body'] = '测试标题';
		$data['out_trade_no'] = doOrderSn(123);
		$data['total_fee'] = 1;
		$data['attach'] = '';
		$data['notify_url'] = 'http://ysf.whpj.cc/api/Wechat/notify';
		
		try{
			$res = \utils\wechart\PayService::nativePay($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		print_r($res);
	}
	
	
	/**
	 * @description 微信支付回调
	 */
	function notify(){
	    $xmldata = file_get_contents('php://input');
		$data = (array)simplexml_load_string($xmldata, 'SimpleXMLElement', LIBXML_NOCDATA);  //解析xml
		
		log::info('微信支付回调数据:'.print_r($data,true));
		
		if(!\utils\wechart\NotifyService::checkSign($data)){
			log::error('微信支付回调签名错误');
			return;
		}
		//下面写业务逻辑，成功响应下面xml，微信则不在发起通知
		
		return '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
	}
	
	/**
	* @description 微信退款  如果是小程序支付配置jsapiPay 方法配置参数 array_merge(config('my.mini_program'),config('my.wechart_pay')) ;公众支付配置 array_merge(config('my.official_accounts'),config('my.wechart_pay'))
	 * @apiParam (输入参数：) {string}			[out_trade_no] 支付订单号
	 * @apiParam (输入参数：) {int}				[refund_fee] 支付金额 单位分
	 * @apiParam (输入参数：) {string}			[desc] 退款说明
	 */
	function refund(){
		$data['out_trade_no'] = '2023101622202812352306';
		$data['total_fee'] = 1;
		$data['refund_fee'] = 1;
		$data['desc'] = '退款说明';
		
		try{
			$res = \utils\wechart\PayService::refund($data,array_merge(config('my.mini_program'),config('my.wechart_pay')));
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json($res);
	}
	
	/**
	* @description 查询订单  如果是小程序支付配置jsapiPay 方法配置参数 array_merge(config('my.mini_program'),config('my.wechart_pay')) ;公众支付配置 array_merge(config('my.official_accounts'),config('my.wechart_pay'))
	 * @apiParam (输入参数：) {string}			[out_trade_no] 支付订单号
	 */
	function queryOrder(){
		$order = '2023101622202812352306';
		try{
			$res = \utils\wechart\PayService::payQuery($order,array_merge(config('my.mini_program'),config('my.wechart_pay')));
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json($res);
	}
	
	
	/**
	* @description 企业付款到零钱  如果是小程序支付配置jsapiPay 方法配置参数 array_merge(config('my.mini_program'),config('my.wechart_pay')) ;公众支付配置 array_merge(config('my.official_accounts'),config('my.wechart_pay'))
	 * @apiParam (输入参数：) {string}			[re_user_name] 用户真实姓名
	 * @apiParam (输入参数：) {string}			[openid] 用户openid
	 * @apiParam (输入参数：) {int}				[amount] 支付金额 单位分
	 * @apiParam (输入参数：) {string}			[desc] 退款说明
	 */
	function payToUserBlance(){
		$data['re_user_name'] = '张三';
		$data['openid'] = 'oGXKT64A45BGrzyb55PXdAecmsYY';
		$data['amount'] = 1;
		$data['desc'] = '付款说明';
		
		try{
			$res = \utils\wechart\PayService::payToUserBlance($data,array_merge(config('my.mini_program'),config('my.wechart_pay')));
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json($res);
	}
	
	
	/**
	* @description 发送普通红包  如果是小程序支付配置jsapiPay 方法配置参数 array_merge(config('my.mini_program'),config('my.wechart_pay')) ;公众支付配置 array_merge(config('my.official_accounts'),config('my.wechart_pay'))
	 * @apiParam (输入参数：) {string}			[re_user_name] 用户真实姓名
	 * @apiParam (输入参数：) {string}			[openid] 用户openid
	 * @apiParam (输入参数：) {float64}				[amount] 支付金额 单位元
	 * @apiParam (输入参数：) {string}			[desc] 退款说明
	 */
	function sendNormalRedpack(){
		$data['send_name'] = '发送说明';
		$data['re_openid'] = 'oGXKT64A45BGrzyb55PXdAecmsYY';
		$data['total_amount'] = 1;
		$data['wishing'] = '祝福语';
		$data['act_name'] = '活动名称';
		$data['remark'] = '活动备注';
		
		try{
			$res = \utils\wechart\PayService::sendNormalRedpack($data,array_merge(config('my.mini_program'),config('my.wechart_pay')));
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json($res);
	}
	
	/**
	* @description 自动回复
	*/
	public function onMessage(){
		$app = Factory::officialAccount(config('my.official_accounts'));
		$app->server->push(function ($message) {
			 switch ($message['MsgType']) {
			    //关注自动回复
				case 'event':
					return '测试';
				break;
				
				//文字回复 可自定义规则
				case 'text':
					return '投票已结束';
				break;
			 }
		});
		$response = $app->server->serve();
		$response->send();
		return $response;
	}
	
	
	/**
	* @description 获取公众号菜单，需要设置ip白名单，设置以后10分钟后生效
	*/
	public function getMenu(){
		try{
			$res = \utils\wechart\MenuService::getMenu();
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json($res);
	}
	
	
	/**
	* @description 设置菜单,需要设置ip白名单，设置以后10分钟后生效
	*/  
	public function setMenu(){
		$buttons = [
			[
				"name"       => "云闪付",
				"sub_button" => [
					[
						"type" => "view",
						"name" => "云闪付推广平台",
						"url"  => "http://xxx.com"
					],
				],
			],
		];

		try{
			$res = \utils\wechart\MenuService::setMenu($buttons);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		print_r($res);
	}
	
	/**
	* @description 发送公众号模板消息，如需跳转到小程序 可以增加参数 miniprogram 官方文档说明： https://easywechat.com/5.x/official-account/template_message.html
	 * @apiParam (输入参数：) {string}			[body] 消息参数
	 * @apiParam (输入参数：) {string}			[openid] 用户openid
	 * @apiParam (输入参数：) {string}			[template_id] 模板id
	 * @apiParam (输入参数：) {string}			[url] 公众号跳转地址
	 */
	public function sendOfficialTempLateMsg(){
		$data['body'] = [
			 "first"  => "你好！",
			 "keyword1"   => "鄂A54M57",
			 "keyword2"   => "武汉站东广场",
		];
		$data['openid'] = 'oswRovy26PHxZ33lDp1PusW-99GQ';	//接收消息用户的openid
		$data['url'] = 'http://www.baidu.com';	//消息跳转地址
		
		$template_id = "hUDi3kCCVm-9ykDF5jNvmfSszmIDoFbBKgz3FDisdW4";
		
		try{
			$res = \utils\wechart\TemplateService::sendOfficialTempLateMsg($data,$template_id);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		
		return json($res);
	}
	

}

