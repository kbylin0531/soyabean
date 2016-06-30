<?php

/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/30
 * Time: 21:02
 */
namespace Application\System\Member\Common\Logic;
use Application\System\Member\Model\MemberModel;
use Soya\Extend\Cookie;
use Soya\Extend\Logic;
use Soya\Extend\Session;
use Soya\Util\Encrypt\Base64;

class LoginLogic extends Logic {

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


    /**
     * @param string $username
     * @param null $password
     * @param bool $remember
     * @return bool|string 返回的string代表着错误的信息，返回true表示登陆成功
     */
    public function login($username,$password,$remember){
        $model = new MemberModel();
        $usrinfo = $model->checkLogin($username,$password);
        if(false === $usrinfo){
            return $model->error();
        }

        //set session,browser must enable cookie
        if($remember){
            $sinfo = serialize($usrinfo);
            $cookie = Base64::encrypt($sinfo, self::$key);
            Cookie::getInstance()->set(self::$key, $cookie,7*24*3600);//one week
        }
        Session::getInstance()->set(self::$key, $usrinfo);
        return true;
    }

    /**
     * 注销登陆
     * @return void
     */
    public function logout(){
        Session::getInstance()->clear(self::$key);
        Cookie::getInstance()->clear(self::$key);
    }

}