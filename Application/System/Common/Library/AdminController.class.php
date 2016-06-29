<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/29
 * Time: 21:10
 */

namespace Application\System\Common\Library;

use Soya\Util\SEK;

class AdminController extends CommonController{

    public function __construct(){
        parent::__construct(null);
        if(!$this->checkLoginStatus()){
            $this->go('/Admin/PublicAction/PageLogin');
        }
    }

    /**
     * @param string|null $template 如果是null,将自动获取调用本方法的名称并去掉开头的Page前缀
     * @param null $cache_id
     * @param null $compile_id
     * @param null $parent
     */
    protected function displayManagement($template = null, $cache_id = null, $compile_id = null, $parent = null){
        //加载模块和菜单
        $this->assign('infos',json_encode([
            'cdn'   => $this->getCDN(),//加载CDN
            'page'  => $this->getPageInfo(),
            'user'  => $this->getUserInfo(),
        ]));

        //获取调用自己的函数
        null === $template and $template = SEK::getCallPlace(SEK::CALL_ELEMENT_FUNCTION,SEK::CALL_PLACE_FORWARD)[SEK::CALL_ELEMENT_FUNCTION];
        $this->display($template /* substr($template,4) 第五个字符开始 */, $cache_id , $compile_id, $parent);
    }

    /**
     * 加载CDN方案
     * @return array
     */
    private function getCDN(){
        $solution = ModuleButler::loadConfig('cdn');
        return $solution['solution_list'][$solution['active_index']];
    }

    protected function getUserInfo(){
        return ModuleButler::loadConfig('_user');
    }
    /**
     * 分配管理员页面信息
     */
    protected function assignAdminPage(){
        $this->assignUserInfoList();
        $this->assignModulesList();
        $this->assignActionsList();
    }

    /**
     * 加载页面参数
     * @return array
     */
    private function getPageInfo(){
        $memuModel = new MenuModel();
        $pageinfo = [
            //head部分
            'title' => 'KbylinFramework',
            'coptright' => ' 2014 © YZ',
            //body部分
            'logo'  => 'Dazz',
        ];
        $menu = [
            'menuitem_id'   => 560,//for finding his parent
            'header_menu'   => $memuModel->getHeaderMenuConfig(),
            'sidebar_menu'  => $memuModel->getSidebarMenuConfig(),
        ];
        return array_merge($pageinfo, $menu);
    }

    /**
     * 分配用户信息列表
     */
    protected function assignUserInfoList(){}

    /**
     *  分配模块信息列表
     */
    protected function assignModulesList(){}

    /**
     * 分配模块下的操作信息列表
     */
    protected function assignActionsList(){}



}