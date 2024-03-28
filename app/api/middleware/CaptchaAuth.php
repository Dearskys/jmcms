<?php

//图片验证码中间件

namespace app\api\middleware;
use think\exception\ValidateException;

class CaptchaAuth
{
	
    public function handle($request, \Closure $next){	
		$captcha	= $request->post('captcha','','strip_tags,trim');	//验证码
		$key	= $request->post('key','','strip_tags,trim');	//验证码id
		
		if(empty($captcha)){
			throw new ValidateException('验证码不能为空');
		}
		
		if(!captcha_check($key,$captcha)){
			throw new ValidateException('验证码错误');
		}
		
		return $next($request);	
    }
} 