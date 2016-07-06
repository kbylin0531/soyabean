<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/7/1
 * Time: 12:40
 */

namespace Application\Home\Controller;
use Application\System\Common\Library\CommonController;
use Application\System\Member\Common\Logic\LoginLogic;

/**
 * Class IndexController
 * @package Application\Home\Controller
 */
class IndexController extends CommonController{
    /**
     * IndexController constructor.
     * @param null $identify
     */
    public function __construct($identify=null){
        define('REQUEST_PATH','/'.REQUEST_MODULE.'/'.REQUEST_CONTROLLER.'/'.REQUEST_ACTION);
    }

    protected function __checkLogin(){
        if(!LoginLogic::getInstance()->isLogin()){
            $this->go('/Home/User/login');
        }
    }

    public function index(){
        echo 'Hello Soya!';
    }


}