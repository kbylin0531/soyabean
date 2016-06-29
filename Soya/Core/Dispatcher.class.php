<?php

/**
 * Created by linzhv@outlook.com
 * User: asus
 * Date: 16-6-14
 * Time: 10:35
 */
namespace Soya\Core;
use Application\Home\Controller\Blank;
use ReflectionMethod;

use Soya\Util\SEK;

/**
 * Class Dispatcher
 * 将URI解析结果调度到指定的控制器下的方法下
 * @package Soya\Core
 */
class Dispatcher extends \Soya {

    const CONF_NAME = 'dispatcher';
    const CONF_CONVENTION = [
        //空缺时默认补上
        'INDEX_MODULE'      => 'Home',
        'INDEX_CONTROLLER'  => 'Index',
        'INDEX_ACTION'      => 'index',

        //找不到对应时
        'EMPTY_MODULE'      => 'Home',
        'EMPTY_CONTROLLER'  => 'Home\\Controller\\EmptyController',
        'EMPTY_ACTION'      => '_empty',

        'CONTROLLER_SUFFIX' => 'Controller',
    ];

    private $_module = null;
    private $_controller = null;
    private $_action = null;

    /**
     * 匹配空缺补上默认
     * @param string|array $modules
     * @param string $ctrler
     * @param string $action
     * @return $this
     */
    public function fetch($modules,$ctrler,$action){
        $config = self::getConfig();
//        dumpout($modules,$ctrler,$action);
        $this->_module      = $modules?$modules:$config['INDEX_MODULE'];
        $this->_controller  = $ctrler?$ctrler:$config['INDEX_CONTROLLER'];
        $this->_action      = $action?$action:$config['INDEX_ACTION'];
        is_array($modules) and $this->_module = SEK::toModulesString($modules,'/');
        return $this;
    }

    /**
     * 制定对应的方法
     * @param string $modules
     * @param string $ctrler
     * @param string $action
     * @return mixed
     * @throws Exception
     */
    public function exec($modules=null,$ctrler=null,$action=null){
        null === $modules   and $modules = $this->_module;
        null === $ctrler    and $ctrler = $this->_controller;
        null === $action    and $action = $this->_action;


        define('__MODULE__',URI::getBasicUrl().'/'.$modules);
        define('__CONTROLLER__',__MODULE__.'/'.$ctrler);
        define('__ACTION__',__CONTROLLER__.'/'.$action);

        $config = self::getConfig();
        //模块检测
        is_dir(PATH_BASE."Application/{$modules}") or $modules = $config['EMPTY_MODULE'];

        //控制器名称及存实性检测
        $className = "Application\\{$modules}\\Controller\\{$ctrler}{$config['CONTROLLER_SUFFIX']}";
        if(!class_exists($className)){
            //TODO:控制器或者方法找不到的整理
            if($config['EMPTY_CONTROLLER'] or !class_exists($config['EMPTY_CONTROLLER'])){
                Exception::throwing("Controller '$className' not found!");
            }
            if(is_array($config['EMPTY_CONTROLLER'])){
                if(isset($config['EMPTY_CONTROLLER'][$modules])){
                    $className = $config['EMPTY_CONTROLLER'][$modules];
                }elseif(isset($config['EMPTY_CONTROLLER']['DEFAULT'])){
                    $className = $config['EMPTY_CONTROLLER']['DEFAULT'];
                }else{
                    Exception::throwing('Controller not found !');
                }
            }else{
                $className = $config['EMPTY_CONTROLLER'];
            }
        }
        $classInstance =  new $className();


        //方法名称及存实性检测
        if(!method_exists($classInstance,$action)){
            if($config['EMPTY_ACTION'] and method_exists($classInstance,$config['EMPTY_ACTION'])){
                $action = $config['EMPTY_ACTION'];
            }else{
                Exception::throwing($className,$action,'The method do not exits!');
            }
        }

        //获取实际目标方法
        $targetMethod = new ReflectionMethod($classInstance, $action);

        $result = null;
        if ($targetMethod->isPublic() and !$targetMethod->isStatic()) {//仅允许非静态的公开方法
            //方法的参数检测
            if ($targetMethod->getNumberOfParameters()) {//有参数
                $args = self::fetchMethodArguments($targetMethod);
                //执行方法
                $result = $targetMethod->invokeArgs($classInstance, $args);
            } else {//无参数的方法调用
                $result = $targetMethod->invoke($classInstance);
            }
        } else {
            Exception::throwing($className, $action);
        }

        \Soya::recordStatus('execute_end');
        return $result;
    }



    /**
     * 获取传递给盖饭昂奋的参数
     * @param ReflectionMethod $targetMethod
     * @return array
     * @throws Exception
     */
    private static function fetchMethodArguments(ReflectionMethod $targetMethod){
        //获取输入参数
        $vars = [];
        $args = [];
        switch(strtoupper($_SERVER['REQUEST_METHOD'])){
            case 'POST':
                $vars    =  array_merge($_GET,$_POST);
                break;
            case 'PUT':
                parse_str(file_get_contents('php://input'), $vars);
                break;
            default:
                $vars  =  $_GET;
        }
        //获取方法的固定参数
        $methodParams = $targetMethod->getParameters();

        //遍历方法的参数
        foreach ($methodParams as $param) {
            $paramName = $param->getName();

            if(isset($vars[$paramName])){
                $args[] =   $vars[$paramName];
            }elseif($param->isDefaultValueAvailable()){
                $args[] =   $param->getDefaultValue();
            }else{
                Exception::throwing("Miss action parameter '{$param}'!");
            }
        }

        return $args;
    }

}