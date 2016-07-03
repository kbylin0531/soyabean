<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/7/2
 * Time: 15:16
 */

namespace Application\Admin\Controller;
use Application\System\Common\Library\AdminController;
use Application\System\Config\Model\MenuModel;
use Soya\Extend\Response;

/**
 * Class SystemController 关闭系统设置
 * @package Application\Admin\Controller
 */
class SystemController extends AdminController {

    public function menu(){
        $this->show();
    }

    /**
     * get the menu-config list
     */
    public function getMenus(){
        $menuModel = new MenuModel();
        $config  = [
            'header'    => $menuModel->getHeaderMenu(),
            'sidebar'   => $menuModel->getSidebarMenu(),
        ];


//        \Soya\dumpout(json_encode($menuModel->getHeaderMenu()));
        false === $config and Response::failed('Failed to get menu config!'.$menuModel->error());
        Response::ajaxBack($config);//直接返回文本
    }

}