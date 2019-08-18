<?php

namespace App\Validate\User;


use App\Validate\Base;

/**
 * 注册验证类
 * Class SignUp
 * @package App\Validate\User
 */
class SignUp extends Base
{
    protected $rule = [
        'to_language' => 'required',
        'recommend_id' => 'required|func:alphaNum|betweenLen:8~11',
        'password' => 'required|func:alphaNum|betweenLen:8~11|equalWithColumn:re_password~true',
        'recommend' => 'required|func:alphaNum|betweenLen:8~11',

    ];

    protected $message = [
        'to_language.required' => "缺少必要参数to_language",
        'recommend_id.required' => "请输入ID号",
        'recommend_id.func' => "ID号必须为数字+字母组合",
        'recommend_id.betweenLen' => "ID号长度为8-11位",
        'password.required' => "请设置登录密码",
        'password.func' => "登录密码必须为数字+字母组合",
        'password.betweenLen' => "登录密码长度为8-11位",
        'password.equalWithColumn' => "确认密码不一致",
        'recommend.required' => "缺少必要参数推荐码",
        'recommend.func' => "推荐码必须为数字+字母组合",
        'recommend.betweenLen' => "推荐码长度为8-11位",
    ];
}