<?php
/**
 * Created by linzhv@outlook.com
 * User: asus
 * Date: 16-6-14
 * Time: 11:08
 */
namespace Soya\Util;
use Soya\Core\Exception;
use Soya\Util\Helper\StringHelper;

/**
 * Class SEK - System Execute Kits
 * @package Soya\Util
 */
final class SEK {
    /**
     * 调用位置
     */
    const CALL_PLACE_BACKWORD           = 0; //表示调用者自身的位置
    const CALL_PLACE_SELF               = 1;// 表示调用调用者的位置
    const CALL_PLACE_FORWARD            = 2;
    const CALL_PLACE_FURTHER_FORWARD    = 3;
    /**
     * 信息组成
     */
    const CALL_ELEMENT_FUNCTION = 1;
    const CALL_ELEMENT_FILE     = 2;
    const CALL_ELEMENT_LINE     = 4;
    const CALL_ELEMENT_CLASS    = 8;
    const CALL_ELEMENT_TYPE     = 16;
    const CALL_ELEMENT_ARGS     = 32;
    const CALL_ELEMENT_ALL      = 0;

    /**
     * 配置类型
     * 值使用字符串而不是效率更高的数字是处于可以直接匹配后缀名的考虑
     */
    const CONF_TYPE_PHP     = 'php';
    const CONF_TYPE_INI     = 'ini';
    const CONF_TYPE_YAML    = 'yaml';
    const CONF_TYPE_XML     = 'xml';
    const CONF_TYPE_JSON    = 'json';
    /**
     * merge configure from $source to $dest
     * @param array $dest dest config
     * @param array $sourse sourse config whose will overide the $dest config
     * @param bool|false $cover it will merge the target in recursion while $cover is true
     *                  (will perfrom a high efficiency for using the built-in function)
     * @return void
     */
    public static function merge(array &$dest,array $sourse,$cover=false){
        if($cover){
            $dest = array_merge($dest,$sourse);
        }else{
            foreach($sourse as $key=>$val){
                if(isset($dest[$key]) and is_array($val)){
                    self::merge($dest[$key],$val);
                }else{
                    $dest[$key] = $val;
                }
            }
        }
    }

    /**
     * 获取类常量
     * use defined() to avoid error of E_WARNING level
     * @param string $class 完整的类名称
     * @param string $constant 常量名称
     * @param mixed $replacement 不存在时的代替
     * @return mixed
     */
    public static function fetchClassConstant($class,$constant,$replacement=null){
        $name = "{$class}::{$constant}";
        return defined($name)?constant($name):$replacement;
    }

    /**
     * 模块序列转换成数组形式
     * 且数组形式的都是大写字母开头的单词形式
     * @param string|array $modules 模块序列
     * @param string $mmbridge 模块之间的分隔符
     * @return array
     * @throws Exception
     */
    public static function toModulesArray($modules, $mmbridge='/'){
        if(is_string($modules)){
            if(false === stripos($modules,$mmbridge)){
                $modules = [$modules];
            }else{
                $modules = explode($mmbridge,$modules);
            }
        }
        if(!is_array($modules)){
            throw new Exception('Parameter should be an array!');
        }
        return array_map(function ($val) {
            return StringHelper::toJavaStyle($val);
        }, $modules);
    }

    /**
     * 模块学列数组转换成模块序列字符串
     * 模块名称全部小写化
     * @param array|string $modules 模块序列
     * @param string $mmb
     * @return string
     * @throws Exception
     */
    public static function toModulesString($modules,$mmb='/'){
        if(is_array($modules)){
//            foreach($modules as &$modulename){
//                $modulename = StringHelper::toCStyle($modulename);
//            }
            $modules = implode($mmb,$modules);
        }
        is_string($modules) or Exception::throwing('Invalid Parameters!');
        return trim($modules,' /');
    }
    /**
     * 将参数序列装换成参数数组，应用Router模块的配置
     * @param string $params 参数字符串
     * @param string $ppb
     * @param string $pkvb
     * @return array
     */
    public static function toParametersArray($params,$ppb='/',$pkvb='/'){//解析字符串成数组
        $pc = [];
        if($ppb !== $pkvb){//使用不同的分割符
            $parampairs = explode($ppb,$params);
            foreach($parampairs as $val){
                $pos = strpos($val,$pkvb);
                if(false === $pos){
                    //非键值对，赋值数字键
                }else{
                    $key = substr($val,0,$pos);
                    $val = substr($val,$pos+strlen($pkvb));
                    $pc[$key] = $val;
                }
            }
        }else{//使用相同的分隔符
            $elements = explode($ppb,$params);
            $count = count($elements);
            for($i=0; $i<$count; $i += 2){
                if(isset($elements[$i+1])){
                    $pc[$elements[$i]] = $elements[$i+1];
                }else{
                    //单个将被投入匿名参数,先废弃
                }
            }
        }
        return $pc;
    }

    /**
     * 将参数数组转换成参数序列，应用Router模块的配置
     * @param array $params 参数数组
     * @param string $ppb
     * @param string $pkvb
     * @return string
     */
    public static function toParametersString(array $params=null,$ppb='/',$pkvb='/'){
        //希望返回的是字符串是，返回值是void，直接修改自$params
        if(empty($params)) return '';
        $temp = '';
        if($params){
            foreach($params as $key => $val){
                $temp .= "{$key}{$pkvb}{$val}{$ppb}";
            }
            return substr($temp,0,strlen($temp) - strlen($ppb));
        }else{
            return $temp;
        }
    }

    /**
     * 从字面商判断$path是否被包含在$scope的范围内
     * @param string $path 路径
     * @param string $scope 范围
     * @return bool
     */
    public static function checkPathContainedInScope($path, $scope) {
        if (false !== strpos($path, '\\')) $path = str_replace('\\', '/', $path);
        if (false !== strpos($scope, '\\')) $scope = str_replace('\\', '/', $scope);
        $path = rtrim($path, '/');
        $scope = rtrim($scope, '/');
//        dumpout($path,$scope);
        return (IS_WINDOWS ? stripos($path, $scope) : strpos($path, $scope)) === 0;
    }

    /**
     * 获取调用者本身的位置
     * @param int $elements 为0是表示获取全部信息
     * @param int $place 位置属性
     * @return array|string
     */
    public static function getCallPlace($elements=self::CALL_ELEMENT_ALL, $place=self::CALL_PLACE_SELF){
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
        if($elements){
            $result = [];
            $elements & self::CALL_ELEMENT_ARGS     and $result[self::CALL_ELEMENT_ARGS]    = isset($trace[$place]['args'])?$trace[$place]['args']:null;
            $elements & self::CALL_ELEMENT_CLASS    and $result[self::CALL_ELEMENT_CLASS]   = isset($trace[$place]['class'])?$trace[$place]['class']:null;
            $elements & self::CALL_ELEMENT_FILE     and $result[self::CALL_ELEMENT_FILE]    = isset($trace[$place]['file'])?$trace[$place]['file']:null;
            $elements & self::CALL_ELEMENT_FUNCTION and $result[self::CALL_ELEMENT_FUNCTION]= isset($trace[$place]['function'])?$trace[$place]['function']:null;
            $elements & self::CALL_ELEMENT_LINE     and $result[self::CALL_ELEMENT_LINE]    = isset($trace[$place]['line'])?$trace[$place]['line']:null;
            $elements & self::CALL_ELEMENT_TYPE     and $result[self::CALL_ELEMENT_TYPE]    = isset($trace[$place]['type'])?$trace[$place]['type']:null;
            return $result;
        }else{
            return $trace[$place];
        }
    }


    /**
     * 去除代码中的空白和注释
     * @param string $content 代码内容
     * @return string
     */
    public static function stripWhiteSpace($content) {
        $stripStr   = '';
        //分析php源码
        $tokens     = token_get_all($content);
        $last_space = false;
        for ($i = 0, $j = count($tokens); $i < $j; $i++) {
            if (is_string($tokens[$i])) {
                $last_space = false;
                $stripStr  .= $tokens[$i];
            } else {
                switch ($tokens[$i][0]) {
                    //过滤各种php注释
                    case T_COMMENT:
                    case T_DOC_COMMENT:
                        break;
                    //过滤空格
                    case T_WHITESPACE:
                        if (!$last_space) {
                            $stripStr  .= ' ';
                            $last_space = true;
                        }
                        break;
                    case T_START_HEREDOC:
                        $stripStr .= "<<<Soya\n";
                        break;
                    case T_END_HEREDOC:
                        $stripStr .= "Soya;\n";
                        for($k = $i+1; $k < $j; $k++) {
                            if(is_string($tokens[$k]) && $tokens[$k] == ';') {
                                $i = $k;
                                break;
                            } else if($tokens[$k][0] == T_CLOSE_TAG) {
                                break;
                            }
                        }
                        break;
                    default:
                        $last_space = false;
                        $stripStr  .= $tokens[$i][1];
                }
            }
        }
        return $stripStr;
    }

    /**
     * 数组递归遍历
     * @param array $array 待递归调用的数组
     * @param callable $filter 遍历毁掉函数
     * @return array
     */
    public static function arrayRecursiveWalk(array $array, callable $filter) {
        $result = [];
        foreach ($array as $key => $val) {
            $result[$key] = is_array($val) ? self::arrayRecursiveWalk($val,$filter) : call_user_func($filter, $val);
        }
        return $result;
    }
    
    /**
     * 加载配置文件 支持格式转换 仅支持一级配置
     * @param string $file 配置文件名
     * @param callable $parser 配置解析方法 有些格式需要用户自己解析
     * @return array|mixed
     * @throws Exception
     */
    public static function parseConfigFile($file,callable $parser=null){
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        switch ($ext) {
            case self::CONF_TYPE_PHP:
                return include $file;
            case self::CONF_TYPE_INI:
                return parse_ini_file($file);
            case self::CONF_TYPE_YAML:
                return yaml_parse_file($file);
            case self::CONF_TYPE_XML:
                return (array)simplexml_load_file($file);
            case self::CONF_TYPE_JSON:
                return json_decode(file_get_contents($file), true);
            default:
                if (isset($parser)) {
                    return $parser($file);
                } else {
                    return Exception::throwing('无法解析配置文件');
                }
        }
    }
}