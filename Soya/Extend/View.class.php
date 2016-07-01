<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/22
 * Time: 12:22
 */

namespace Soya\Extend;
use Soya\Extend\View\ViewInterface;


class View extends \Soya{
    const CONF_NAME = 'view';
    const CONF_CONVENTION = [
        'PRIOR_INDEX' => 0,
        'DRIVER_CLASS_LIST' => [
            'Soya\\Extend\\View\\Smarty',
            'Soya\\Extend\\View\\Think',
        ],
        'DRIVER_CONFIG_LIST' => [
            [
                'SMARTY_DIR'        => PATH_FRAMEWORK.'Vendor/smarty3/libs/',
                'TEMPLATE_CACHE_DIR'    => PATH_RUNTIME.'View/',

                'SMARTY_CONF'       => [
                    //模板变量分割符号
                    'left_delimiter'    => '{',
                    'right_delimiter'   => '}',
                    //缓存开启和缓存时间
                    'caching'        => true,
                    'cache_lifetime'  => 15,
                ],
            ],
            []
        ],

        //模板文件后缀名
        'TEMPLATE_SUFFIX'   => 'html',
        //模板文件提示错误信息模板
        'TEMPLATE_EMPTY_PATH'   => 'notpl',
    ];

    /**
     * 调用本类display的方法的上下文环境
     * @var array
     */
    protected static $_context = null;

    /**
     * 类实例的驱动
     * @var ViewInterface
     */
    protected $_driver = null;

    public function __construct($identify){
        parent::__construct($identify);
    }

    /**
     * 保存控制器分配的变量
     * @param string $tpl_var
     * @param null $value
     * @param bool $nocache
     * @return void
     */
    public function assign($tpl_var,$value=null,$nocache=false){
        $this->_driver->assign($tpl_var,$value,$nocache);
    }

    /**
     * 获取模板引擎实例
     * @return ViewInterface
     */
    public function getDriver(){
        return $this->_driver;
    }

    /**
     * 显示模板
     * @param array $context 模板调用上下文环境，包括模块、控制器、方法和模板主题
     * @param null $cache_id
     * @param null $compile_id
     * @param null $parent
     * @return void
     */
    public function display(array $context, $cache_id = null, $compile_id = null,$parent = null){
        $template = self::parseTemplatePath($context);
        $this->_driver->setContext($context)->display($template,$cache_id,$compile_id,$parent);
    }

    /**
     * 解析资源文件地址
     * 模板文件资源位置格式：
     *      ModuleA/ModuleB@ControllerName/ActionName:themeName
     *
     * @param array|null $context 模板调用上下文环境，包括模块、控制器、方法和模板主题
     * @return array 类型由参数三决定
     */
    public static function parseTemplatePath($context){
        $config = self::getConfig();
        $path = PATH_BASE."Application/{$context['m']}/View/{$context['c']}/";
        isset($context['t']) and $path = "{$path}{$context['t']}/";
        $path = "{$path}{$context['a']}.{$config['TEMPLATE_SUFFIX']}";
        return $path;
    }
}
