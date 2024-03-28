<?php

namespace app\api\middleware;
use think\exception\ValidateException;
use utils\Jwt;


class JwtAuth
{
	
    public function handle($request, \Closure $next)
    {	
		$token = $request->header('Authorization');
		if(!$token){
			throw new ValidateException('token不能为空');
		}
		$jwt = Jwt::getInstance();
		$jwt->setIss(config('my.jwt_iss'))->setAud(config('my.jwt_aud'))->setSecrect(config('my.jwt_secrect'))->setToken($token);
		
		if($jwt->validationToken()){
			$request->userInfo = $jwt->decode();
			return $next($request);	
		}else{
			return json(['status'=>config('my.jwtErrorCode'),'msg'=>'token失效']);
		}
    }
} 