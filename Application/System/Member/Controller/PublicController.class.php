<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/29
 * Time: 21:33
 */
namespace Application\System\Member\Controller;
use Application\System\Common\Library\CommonController;
use Application\System\Member\Common\Logic\LoginLogic;
use Soya\Core\URI;
use Soya\Extend\Response;
use Soya\Util\UserAgent;

/**
 * Class PublicController 公共可以访问的控制器
 * @package Application\System\Member\Controller
 */
class PublicController extends CommonController {
    /**
     * @param $username
     * @param $password
     * @param bool $remember
     */
    public function login($username=null,$password=null,$remember=false){
        if(IS_METHOD_POST){
            $result = LoginLogic::getInstance()->login($username,$password,$remember);
//            \Soya\dumpout($result,__CONTROLLER__.'/login#'.urlencode($result));
            if(is_string($result)){
                URI::redirect(__CONTROLLER__.'/login#'.urlencode($result));
            }
            $this->redirect('/Admin/Index/index');
            exit();
        }
        $this->display();
    }

    /**
     * 注销登录
     */
    public function logout(){
        LoginLogic::getInstance()->logout();
        $this->redirect(__CONTROLLER__.'/login');
    }



}