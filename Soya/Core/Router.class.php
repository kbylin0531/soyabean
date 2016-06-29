<?php
/**
 * Created by linzhv@outlook.com
 * User: asus
 * Date: 16-6-14
 * Time: 10:53
 */

namespace Soya\Core;

/**
 * Class Router
 * TODO:只管分配，不管创建
 * @package Soya\Core
 */
class Router {

    /**
     * @param $uri
     * @return mixed|null 返回匹配的路由规则，无匹配时返回null
     */
    private function fetchURIRoute($uri){
        $config = self::getConfig();
        //静态路由
        if($config['STATIC_ROUTE_ON'] and $config['STATIC_ROUTE_RULES']){
            if(isset($config['STATIC_ROUTE_RULES'][$uri])){
                return $config['STATIC_ROUTE_RULES'][$uri];
            }
        }
        //规则路由
        if($config['WILDCARD_ROUTE_ON'] and $config['WILDCARD_ROUTE_RULES']){
            foreach($config['WILDCARD_ROUTE_RULES'] as $pattern => $rule){
                $pattern = preg_replace('/\[.+?\]/','([^/\[\]]+)',$pattern);//非贪婪匹配
                $rst = $this->_matchRegular($pattern,$rule, trim($uri,' /'));
                if(null !== $rst) return $rst;
            }
        }
        //正则路由
        if($config['REGULAR_ROUTE_ON'] and $config['REGULAR_ROUTE_RULES']){
            foreach($config['REGULAR_ROUTE_RULES'] as $pattern => $rule){
                $rst = $this->_matchRegular($pattern,$rule, trim($uri,' /'));
                if(null !== $rst) return $rst;
            }
        }
        return null;
    }
    
    /**
     * 使用正则表达式匹配uri
     * @param string $pattern 路由规则
     * @param array|string|callable $rule 路由导向结果
     * @param string $uri 传递进来的URL字符串
     * @return array|string|null
     */
    private function _matchRegular($pattern, $rule, $uri){
        // Does the RegEx match? use '#' to ignore '/'
        if (preg_match('#^'.$pattern.'$#', $uri, $matches)) {
            if(is_array($rule)){
                if(isset($rule[3])){
                    $index = 1;//忽略第一个匹配（全匹配）
                    foreach($rule[3] as $pname=>&$pval){
                        if(isset($matches[$index])){
                            $pval = $matches[$index];
                        }
                        ++$index;
                    }
                    $_GET = array_merge($rule[3],$_GET);// 优先使用$_GET覆盖
                }else{
                    //未设置参数项，不作动作
                }
            }elseif(is_string($rule)){
                $rule = preg_replace('#^'.$pattern.'$#', $rule, $uri);//参数一代表的正则表达式从参数三的字符串中寻找匹配并替换到参数二代表的字符串中
            }elseif(is_callable($rule)){
                // Remove the original string from the matches array.
                $fulltext = array_shift($matches);
                // Execute the callback using the values in matches as its parameters.
                $rule = call_user_func_array($rule, [$matches,$fulltext]);//参数二是完整的匹配
                if(!is_string($rule) and !is_array($rule)){
                    //要求结果必须返回string或者数组
                    return null;
                }
            }
            return $rule;
        }
        return null;
    }
}