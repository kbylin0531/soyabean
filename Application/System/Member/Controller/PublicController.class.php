<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/29
 * Time: 21:33
 */
namespace Application\System\Member\Controller;
use Application\System\Common\Library\CommonController;
use Application\System\Member\Model\MemberModel;
use Soya\Extend\Cookie;
use Soya\Util\Encrypt\Base64;
use Soya\Extend\Session;

/**
 * Class PublicController 公共可以访问的控制器
 * @package Application\System\Member\Controller
 */
class PublicController extends CommonController {

    private static $key = '_userinfo_';
    /**
     * check the current user login status
     * @return bool
     */
    public function isLogin(){
        $session = Session::getInstance();
        $status = $session->get(self::$key);//return null if not set
        if(!$status){
            $cookie = Cookie::getInstance()->get(self::$key);
            if($cookie){
                $usrinfo = unserialize(Base64::decrypt($cookie, self::$key));
                $session->set(self::$key, $usrinfo);
                return true;
            }
        }
        return $status?true:false;
    }

    public function logout(){
        Session::getInstance()->clear(self::$key);
        Cookie::getInstance()->clear(self::$key);
        $this->redirect(__CONTROLLER__.'/PageLogin');
    }

    /**
     * @param $username
     * @param $password
     * @param bool $remember
     */
    public function login($username=null,$password=null,$remember=false){
        if(IS_METHOD_POST){
            $model = new MemberModel();
            $usrinfo = $model->getUserInfo($username);

            if(!$usrinfo) $this->redirect(__CONTROLLER__.'/PageLogin#'.urlencode("用户'{$username}'不存在"));

            if(false === stripos($usrinfo['roles'], 'A')){
                $this->redirect(__CONTROLLER__.'/PageLogin#'.urlencode('暂时不允许非管理员用户登录!'));
            }

            if (md5(rtrim($usrinfo['password'])) === $password){
                //set session,browser must enable cookie
                if($remember){
                    $sinfo = serialize($usrinfo);
                    $cookie = Base64::encrypt($sinfo, self::$key);
                    Cookie::getInstance()->set(self::$key, $cookie,7*24*3600);//one week
                }
                Session::getInstance()->set(self::$key, $usrinfo);
                $this->go('Admin/System/Menu/Management');
            }else{
                $this->redirect(__CONTROLLER__.'/PageLogin#'.urlencode('密码不正确'));
            }
            exit();
        }
        $this->display();
    }



}