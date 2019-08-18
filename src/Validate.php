<?php
/**
 * Create by PhpStorm.
 * User：麻破伦意识
 * Date：2019/5/30
 * Time：11:46
 */

namespace MaPoLun;

use EasySwoole\Validate\Validate as EasyswooleValidate;

/**
 * 验证类，使用规则借鉴tp5规则语法，EasySwoole验证调用方法，验证字段规则值时用~ 符号隔开，可拓展func类型标识自定义函数进行验证规则。
 * Class Base
 * @package App\Validate
 */
class Validate
{
    protected $rule;

    protected $message;

    /**
     * 输出Validate实例
     * @Author: 麻破伦意识
     * @Time: 2019/5/31
     * @return Validate
     */
    public function getExamplie(): EasyswooleValidate
    {
        $validate = new EasyswooleValidate();
        foreach ($this->rule as $key => $item) {
            $filed = $key;
            $rule = [];
            if (strpos($item, '|')) {
                // 规则
                $recond = explode('|', $item);
                $rule = $this->append($filed, $recond);
            } else {
                if (strpos($item, ':')) {
                    list($item, $rule_value) = explode(":", $item);
                }
                $msg_key = $key . "." . $item;
                $rule[] = array_filter([
                    'rule' => $item,
                    'message' => $this->message[$msg_key] ?? "",
                    'rule_value' => $rule_value ?? "",
                ]);
            }
            /**拼接Validate验证范式**/
            $addColumn = $validate->addColumn($filed);
            foreach ($rule as $k => $i) {
                $args = [];
                $funName = $i['rule'];
                $message = $i['message'] ?? null;
                $args = $this->normalData($i, $item, $args, $funName, $message); //组装args参数
                call_user_func_array([$addColumn,$funName],$args);
            }
        }
        return $validate;
    }

    /**
     * 重组Rule
     * @Author: 麻破伦意识
     * @Time: 2019/5/31
     * @param string|null $filed
     * @param array $rule
     * @return array
     */
    protected function append(?string $filed, array $rule): array
    {
        $newRule = [];
        array_walk($rule, function ($item, $io) use (&$newRule, $filed){
            $rule = $item;
            if (strpos($item, ':')) {
                list($rule, $rule_value) = explode(":", $item);
            }
            $msg_key = $filed . "." . $rule;
            $newRule[$io] = array_filter([
                'rule' => $rule,
                'message' => $this->message[$msg_key] ?? "",
                'rule_value' => $rule_value ?? "",
            ]);
        });
        return $newRule;
    }

    /**
     * 组装args参数
     * @Author: 麻破伦意识
     * @Time: 2019/6/1
     * @param $i
     * @param $item
     * @param $args
     * @param $funName
     * @return array
     */
    protected function normalData($i, $item, $args, $funName, $message)
    {
        if (isset($i['rule_value'])) {
            if (strpos($i['rule_value'], '~')) {
                $args = explode("~", $i['rule_value']);
                if ($funName == "inArray" || $funName == "notInArray") {
                    $args[0] = json_decode($args[0]);
                }
            } elseif($funName == "func") {
                $callbackName = $i['rule_value'];      // 匿名函数回调闭包验证
                $callback = function($params, $field_key) use ($callbackName){
                    return $this->$callbackName($params, $field_key);
                };
                array_push($args, $callback);
            } else {
                array_push($args, $i['rule_value']);
            }
        }
        foreach ($args as $k => $v) {
            if ($v === 'false') {
                $args[$k] = false;
            } elseif ($v === 'true') {
                $args[$k] = true;
            }
        }
        array_push($args, $message);
        return $args;
    }

    /**
     * 手机号
     * @Author: 麻破伦意识
     * @Time: 2019/6/1
     * @param $params
     * @param $field
     * @return bool
     */
    protected function isMobile($params, $field)
    {
        $rule = '^1(3|4|5|6|7|8|9)[0-9]\d{8}$^';
        $result = preg_match($rule, $params->$field);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 邮箱
     * @Author: 麻破伦意识
     * @Time: 2019/6/1
     * @param $params
     * @param $field
     * @return bool
     */
    protected function isEmail($params, $field)
    {
        $rule = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
        $result = preg_match($rule, $params->$field);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 身份证号码
     * @Author: 麻破伦意识
     * @Time: 2019/6/1
     * @param $value
     * @return bool
     */
    protected function isCardNo($params, $field)
    {
        $rule = '/(^\d(15)$)|((^\d{18}$))|(^\d{17}(\d|X|x)$)/';
        $result = preg_match($rule, $params->$field);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 银行卡号码
     * @Author: 麻破伦意识
     * @Time: 2019/6/1
     * @param $params
     * @param $field
     * @return bool
     */
    function isBank($params, $field) {
        $chars = "/^(\d{16}|\d{19}|\d{17})$/";
        if (preg_match($chars, $params->$field)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 6-18位数字、字母或者下划线
     * @Author: 麻破伦意识
     * @Time: 2019/6/1
     * @param $params
     * @param $field
     * @return bool
     */
    protected function isRule($params, $field)
    {
        $rule = '/^(\w){6,18}$/';
        $result = preg_match($rule, $params->$field);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 必须为数字+字母组合
     * @param $params
     * @param $field
     * @return bool
     */
    protected function alphaNum($params, $field)
    {
        $rule = '/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/';
        $result = preg_match($rule, $params->$field);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 必须为数字
     * @Author: 麻破伦意识
     * @Time: 2019/6/12
     * @param $params
     * @param $field
     * @return bool
     */
    protected function number($params, $field)
    {
        $rule = '/^\d+$/';
        $result = preg_match($rule, $params->$field);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 数组大于0
     */
    protected function thanzero($params, $field){
        if ($params->$field >0) {
            return true;
        } else {
            return false;
        }
    }
}