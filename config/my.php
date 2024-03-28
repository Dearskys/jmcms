<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 自定义配置
// +----------------------------------------------------------------------
return [
	'upload_subdir'		=> 'Ym',				//文件上传二级目录 标准的日期格式
	'nocheck'			=> ['/admin/Login/verify.html','/admin/Login/index.html','/admin/Login/logout.html','/admin/Base/getMenu.html','/admin/Index/index.html','/admin/Upload/upload.html','/admin/Upload/createFile.html','/admin/Login/aliOssCallBack.html'],	//不需要验证权限的url
	'error_log_code'	=> 500,					//写入日志的状态码
	
	'password_secrect'	=> 'xhadmin',			//密码加密秘钥
	
	'multiple_login'		=> true,  			//后台单点登录 true 允许多个账户登录 false 只允许一个账户登录
	
	'dump_extension'	=> 'xlsx',				//默认导出格式
	'verify_status'		=> false,				//后台登录验证码开关
	'water_img'			=>	'./static/images/water.png',	//水印图片路径
	
	'check_file_status'	=> true,			//上传图片是否检测图片存在
	
	'show_home_chats'	=> true,			//是否显示首页图表
	
	'api_upload_auth'=> true,	//api应用上传是否验证token  true 验证 false不验证 需要重新生成

	
	//腾讯云短信配置
	'tencent_sms_appid'	=>	14008291012,						//appiid
	'tencent_sms_appkey' => '0837886a56aaab33e6e9d49bacc65c',	//appkey
	'tencent_sms_tempCode'=> 18261127,							//短信模板id
	'tencent_sms_signname' => 'vueadmin',						//短信签名
	
	//阿里云短信配置
	'ali_sms_accessKeyId'		=> 'LTAI5t8K3hVwoJ3Z1ga9yM',				//阿里云短信 keyId	
	'ali_sms_accessKeySecret'	=> 'J4rAkx5BRrJRBRO4WvhV7VW1vjq',	//阿里云短信 keysecret
	'ali_sms_signname'			=> 'vueamdin',			//签名
	'ali_sms_tempCode'			=> 'SMS_238150018',						//短信模板 Code
	
	//oss开启状态 以及配置指定oss
	'oss_status'			=> false,			//true启用  false 不启用
	'oss_upload_type'		=> 'server',		//client 客户端直传  server 服务端传
	'oss_default_type'		=> 'ali',			//oss使用类别 ali(阿里),qiniuyun(七牛),tencent(腾讯)
	
	//阿里云oss配置
	'ali_oss_accessKeyId'		=> 'LTAI5t63MtSkZxAqEyy8VZf',						//阿里云短信 keyId	
	'ali_oss_accessKeySecret'	=> '4UI62NuZepXJqettpBbv2049El',				//阿里云短信 keysecret
	'ali_oss_endpoint'			=> 'http://i.whpj.vip',							//建议填写自己绑定的域名
	'ali_oss_bucket'			=> 'xhadmin',				
	
	//七牛云oss配置
	'qny_oss_accessKey' 		=> 'bm1sR9bx0F5HK7KqRtAhZ8zOxb-HCGYx5pJU',  //access_key
	'qny_oss_secretKey' 		=> 'YrRaySbqu7M1PzZHOuJM0bUdb7GBPRiYa7Lq',     //secret_key
	'qny_oss_bucket'	  		=> 'xhadmin',							//bucket
	'qny_oss_domain'	  		=> 'http://images.whpj.vip', 		//七牛云访问的域名
	'qny_oss_client_uploadurl'	=> 'http://up-z0.qiniup.com',		//七牛云客户端直传上传地址 不用动如果提示地址错误 根据提示换就行
	
	//腾讯云cos配置
	'tencent_oss_secretId'		=> 'AKIDrX2KMq4nHVTBepY2GFySzJRLzOaXEj',				//腾讯云keyId	
	'tencent_oss_secretKey'		=> 'KOCj00kESP6XjSv85FRM2TxCAsKVQ5',		//腾讯云keysecret
	'tencent_oss_bucket'		=> 'uupx-1309810810',							//腾讯云bucket
	'tencent_oss_region'		=> 'ap-guangzhou',									//地区，根据自己的填写
	'tencent_oss_schema'		=> 'http',							//访问前缀 支持http  https	
  
	//api jwt鉴权配置
	'jwt_expire_time'		=> '+5 day',			//second秒 hour小时  minute分钟 day 天
	'jwt_secrect'			=> 'boTCfOGKwqTNKArTboaTCfOGKawqTJKArTboTCfOGKwqTNKTboTCfOGKwqTNKArT',	//签名秘钥
	'jwt_iss'				=> 'client.vueadmin',	//发送端
	'jwt_aud'				=> 'server.vueadmin',	//接收端
	'jwtExpireCode'			=> 101,					//jwt过期
	'jwtErrorCode'			=> 102,					//jwt无效
	
	//小程序配置
	'mini_program'			=> [
		'app_id' => 'wxd1b8315d0b00433',					//小程序appid
		'secret' => '3dcc2f9cf37c9f1399471ba1387a859',		//小程序secret
	],
	
	//公众号配置
	'official_accounts'		=> [
		'app_id'        => 'wxa2c835d94364852',						//公众号appid
		'secret'		=> '8687684cae5dad3eb9abc4df686f733',			//公众号secret
		'token'			=> 'chengdie',									//token
		'aes_key'		=> '', 											// EncodingAESKey，兼容与安全模式下请一定要填写！！！
	],
			
	//微信支付配置
	'wechart_pay'			=> [
		'mch_id'         => '1346545201',															//商户号
		'key'            => '6a5888d05ceb8033ebf0a3dfbf2b416e',										//微信支付32位秘钥
		'cert_path'      => app()->getRootPath().'extend/utils/wechart/zcerts/apiclient_cert.pem',	//证书路径
		'key_path'       => app()->getRootPath().'extend/utils/wechart/zcerts/apiclient_key.pem',	//证书路径
	],	

];
