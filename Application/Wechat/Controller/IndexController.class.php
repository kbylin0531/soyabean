<?php

/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/8/16
 * Time: 9:47 PM
 */
namespace Application\Wechat\Controller;
use Application\Wechat\Common\Library\Wechat;


class IndexController {

    protected $AppID = 'wxd10144c82235ff4f';
    protected $AppSecret = 'f8d6a3fd9f35404521353956714aa165';

    public function index(){
        define("TOKEN", "linzhv");
        $wechatObj = new Wechat();
        isset($_GET['echostr'])?$wechatObj->valid():$wechatObj->responseMsg();

    }


    public function valid()     {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()     {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new \Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

}