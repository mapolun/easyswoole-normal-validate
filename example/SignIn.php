<?php
/**
 * Create by PhpStorm.
 * User：麻破伦意识
 * Date：2019/6/6
 * Time：17:41
 */

namespace App\Validate\User;


use App\Validate\Base;

/**
 * 登录验证类
 * Class SignIn
 * @package App\Validate\User
 */
class SignIn extends Base
{
    protected $rule = [
        'to_language' => 'required',
        'recommend_id' => 'required|func:alphaNum|betweenLen:8~11',
        'password' => 'required|func:alphaNum|betweenLen:8~11',
    ];

    protected $message = [
        'to_language.required' => "缺少必要参数to_language",
        'recommend_id.required' => "请输入ID号",
        'recommend_id.func' => "ID号必须为数字+字母组合",
        'recommend_id.betweenLen' => "ID号长度为8-11位",
        'password.required' => "请输入登录密码",
        'password.func' => "登录密码必须为数字+字母组合",
        'password.betweenLen' => "登录密码长度为8-11位",
    ];
}