<?php
use think\facade\Route;

Route::rule('Member/add', 'Member/add')->middleware(['JwtAuth']);	//会员管理添加;
Route::rule('Member/update', 'Member/update')->middleware(['JwtAuth']);	//会员管理修改;
Route::rule('Member/logina', 'Member/logina')->middleware(['SmsAuth']);	//会员管理用户登录;
