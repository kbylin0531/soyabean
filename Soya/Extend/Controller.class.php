<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/21
 * Time: 21:29
 */

namespace Soya\Extend;
use Soya\Core\Exception;
use Soya\Core\URI;
use Soya\Util\SEK;

/**
 * Class Controller 控制器
 * @package Soya\Extend
 */
class Controller extends \Soya{

    const CONF_NAME = 'controller';
    const CONF_CONVENTION = [
        'VIEW_DRIVER_INDEX'  => 1,
    ];


    /**
     * 分配给模板的变量集合
     * @var array
     */
    private $_tVars = [];

    /**
     * 控制器上下文环境
     * @var array
     */
    private static $_context = null;

    /**
     * 获取调用类的上下文环境
     * @access private 可以保证以来它的方法获取的上下文环境是new过控制器之后的上下文环境中获取的
     * @param string|null $clsnm 类名称,不设置的情况下将自动获取
     * @return $this
     * @throws Exception
     */
    private static function fetchContext($clsnm=null){
        null === $clsnm and $clsnm = static::class;//get_called_class()
        if(preg_match('/^Application\\\(.*)\\\Controller\\\(.*)Controller$/',$clsnm,$matches)){
            self::$_context['m'] = str_replace('\\','/',$matches[1]);
            self::$_context['c'] = $matches[2];
        }else{
            //如果出现名称空间不规范的情况，直接终止程序
            //一般会在调度器中检测不到类时抛出异常，所以这一步很难执行到
            Exception::throwing("控制器'{$clsnm}'不符合命名空间规范！");
        }
    }

    /**
     * 设置默认的模板主题
     * @param string $tname 主题名称
     * @return $this
     */
    protected function theme($tname){
        isset(self::$_context) or self::fetchContext();
        self::$_context['t'] = $tname;
        return $this;
    }

    /**
     * 分配模板变量
     * 全部格式转换成：
     * $tpl_var =>  array($value,$nocache=false)
     * @param array|string $tpl_var 变量名称 或者 "名称/变量值"键值对数组
     * @param mixed $value 变量值
     * @return $this 可以链式调用
     */
    protected function assign($tpl_var,$value=null){
        if (is_array($tpl_var)) {
            foreach ($tpl_var as $_key => $_val) {
                $_key and $this->_tVars[$_key] = $_val;
            }
        } else {
            $tpl_var and $this->_tVars[$tpl_var] = $value;
        }
    }

    /**
     * 显示模板
     * 例如：
     *  $this->display('index2');
     *  将自动找到该控制器对应的模板目录下的对应模板
     * 使用final的作用是避免被集成类复写,从而避免SEK::getCallPlace(SEK::CALL_ELEMENT_FUNCTION,2)出现的不稳定
     * @param string $template   当前控制器下的模板文件名称，可以不含模板后缀名
     * @param mixed  $cache_id   cache id to be used with this template（参照Smarty）
     * @param mixed  $compile_id compile id to be used with this template（参照Smarty）
     * @param object $parent     next higher level of Smarty variables（参照Smarty）
     * @return void
     */
    final protected function display($template = null, $cache_id = null, $compile_id = null, $parent = null){
        null === self::$_context and self::fetchContext();
        //未设置时使用调用display的函数名称
        if(null === $template){//如果未设置参数一,获取当前调用方法的名称作为模板的默认名称
            self::$_context['a'] = SEK::getCallPlace(SEK::CALL_ELEMENT_FUNCTION,SEK::CALL_PLACE_FORWARD)[SEK::CALL_ELEMENT_FUNCTION];
            $context = self::$_context;
        }else{
            $context = $this->parseTemplateLocation($template);
        }
        $view = View::getInstance(self::getConfig('VIEW_DRIVER_INDEX'));
        //模板变量导入
        $view->assign($this->_tVars);

        //格式化模板变量
        \Soya::recordStatus('display_begin');
//        \Soya\dumpout($context);
        $view->display($context,$cache_id,$compile_id,$parent);
        \Soya::recordStatus('display_end');
    }

    /**
     * 解析模板位置
     * 测试代码：
    $this->parseTemplateLocation('ModuleA/ModuleB@ControllerName/ActionName:themeName'),
    $this->parseTemplateLocation('ModuleA/ModuleB@ControllerName/ActionName'),
    $this->parseTemplateLocation('ControllerName/ActionName:themeName'),
    $this->parseTemplateLocation('ControllerName/ActionName'),
    $this->parseTemplateLocation('ActionName'),
    $this->parseTemplateLocation('ActionName:themeName')
     * @param string $location 模板位置
     * @return array
     */
    public static function parseTemplateLocation($location){
        //获取相对于调用控制器的目录，需要获取上下文环境
        null === self::$_context and self::fetchContext();
        //资源解析结果：元素一表示解析结果
        $result = [];

        //-- 解析字符串成数组 --//
        $tpos = strpos($location,':');
        //解析主题
        if(false !== $tpos){
            //存在主题
            $result['t'] = substr($location,$tpos+1);//末尾的pos需要-1-1
            $location = substr($location,0,$tpos);
        }
        //解析模块
        $mcpos = strpos($location,'@');
        if(false !== $mcpos){
            $result['m'] = substr($location,0,$mcpos);
            $location = substr($location,$mcpos+1);
        }
        //解析控制器和方法
        $capos = strpos($location,'/');
        if(false !== $capos){
            $result['c'] = substr($location,0,$capos);
            $result['a'] = substr($location,$capos+1);
        }else{
            $result['a'] = $location;
        }

        isset(self::$_context['t']) and !isset($result['t']) and $result['t'] = self::$_context['t'];
        isset(self::$_context['m']) and !isset($result['m']) and $result['m'] = self::$_context['m'];
        isset(self::$_context['c']) and !isset($result['c']) and $result['c'] = self::$_context['c'];
        isset(self::$_context['a']) and !isset($result['a']) and $result['a'] = self::$_context['a'];
        return $result;
    }

    /**
     * 页面跳转
     * 与URI::redirect的区别是后者认为参数中的url是一个有效的跳转链接
     * @param string $compo   形式如'Cms/install/third' 的action定位
     * @param array $params   URL参数
     * @param int $time       等待时间
     * @param string $message 跳转等待提示语
     * @return void
     */
    public function redirect($compo,array $params=[],$time=0,$message=''){
//        \Soya\dumpout($compo);
        URI::redirect(URI::url($compo,$params),$time,$message);
    }

    /**
     * 默认跳转操作 支持错误导向和正确跳转
     * 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     * @param string $message 提示信息
     * @param string $title 跳转页面标题
     * @param bool $status 页面状态,true为积极的一面，false为消极的一面
     * @param bool $jumpback 页面操作，true时表示返回之前的页面，false时提示完毕后自动关闭窗口
     * @param int $wait 页面等待时间
     * @return void
     * @throws \Exception
     */
    protected static function jump($message,$title='跳转',$status=true,$jumpback=true,$wait=1) {
        Response::sendNocache(true);//保证输出不受静态缓存影响
        $vars = [];
        $vars['wait'] = $wait;
        $vars['title'] = $title;
        $vars['message'] = $message;
        $vars['status'] = $status?1:0;

        $vars['jumpurl'] = $jumpback?
            'javascript:history.back(-1);':
            'javascript:window.close();';

        \Soya::loadTemplate('jump',$vars);
    }

    /**
     * 跳转到成功显示页面
     * @param string $message 提示信息
     * @param int $waittime 等待时间
     * @param string $title 显示标题
     * @throws \Exception
     */
    public function success($message,$waittime=1,$title='success'){
        self::jump($message,$title,true,1,$waittime);
    }

    /**
     * 跳转到错误信息显示页面
     * @param string $message 提示信息
     * @param int $waittime 等待时间
     * @param string $title 显示标题
     * @throws \Exception
     */
    public function error($message,$waittime=3,$title='error'){
        self::jump($message,$title,false,1,$waittime);
    }

}