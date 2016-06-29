<?php
/**
 * Created by phpStorm.
 * User: linzh
 * Date: 2016/6/20
 * Time: 16:52
 */

namespace Soya\Util;


class UserAgent {

    /**
     * 浏览器类型
     */
    const AGENT_IE      = 'ie';
    const AGENT_FIRFOX  = 'firefox';
    const AGENT_CHROME  = 'chrome';
    const AGENT_OPERA   = 'opera';
    const AGENT_SAFARI  = 'safari';
    const AGENT_UNKNOWN = 'unknown';

    /**
     * 获取浏览器类型
     * @return string
     */
    public static function getBrowser(){
        if (empty($_SERVER['HTTP_USER_AGENT'])){    //当浏览器没有发送访问者的信息的时候
            return 'unknow';
        }
        $agent=$_SERVER["HTTP_USER_AGENT"];
        if(strpos($agent,'MSIE')!==false || strpos($agent,'rv:11.0')) //ie11判断
            return self::AGENT_IE;
        else if(strpos($agent,'Firefox')!==false)
            return self::AGENT_FIRFOX;
        else if(strpos($agent,'Chrome')!==false)
            return self::AGENT_CHROME;
        else if(strpos($agent,'Opera')!==false)
            return self::AGENT_OPERA;
        else if((strpos($agent,'Chrome')==false)&&strpos($agent,'Safari')!==false)
            return self::AGENT_SAFARI;
        else
            return self::AGENT_UNKNOWN;
    }
    /**
     * 获取浏览器版本
     * @return string
     */
    public static function getBrowserVer(){
        if (empty($_SERVER['HTTP_USER_AGENT'])){    //当浏览器没有发送访问者的信息的时候
            return self::AGENT_UNKNOWN;
        }
        $agent= $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/MSIE\s(\d+)\..*/i', $agent, $regs))
            return $regs[1];
        elseif (preg_match('/FireFox\/(\d+)\..*/i', $agent, $regs))
            return $regs[1];
        elseif (preg_match('/Opera[\s|\/](\d+)\..*/i', $agent, $regs))
            return $regs[1];
        elseif (preg_match('/Chrome\/(\d+)\..*/i', $agent, $regs))
            return $regs[1];
        elseif ((strpos($agent,'Chrome')==false) and preg_match('/Safari\/(\d+)\..*$/i', $agent, $regs))
            return $regs[1];
        else
            return self::AGENT_UNKNOWN;
    }

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    public static function getClientIP($type = 0,$adv=false) {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if($adv){
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//透过代理的正式IP
                $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos    =   array_search('unknown',$arr);
                if(false !== $pos) unset($arr[$pos]);
                $ip     =   trim($arr[0]);
            }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip     =   $_SERVER['HTTP_CLIENT_IP'];
            }elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip     =   $_SERVER['REMOTE_ADDR'];
            }
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {//客户端IP，如果是通过代理访问则返回代理IP
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }


    /**
     * 确定客户端发起的请求是否基于SSL协议
     * @return bool
     */
    public static function isHttps(){
        return (isset($_SERVER['HTTPS']) and ('1' == $_SERVER['HTTPS'] or 'on' == strtolower($_SERVER['HTTPS']))) or
        (isset($_SERVER['SERVER_PORT']) and ('443' == $_SERVER['SERVER_PORT']));
    }

}