<?php

/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/8/16
 * Time: 9:47 PM
 */
namespace Application\Wechat\Controller;
use Application\Wechat\Common\Library\MessageInterface;

/**
 * Class IndexController
 * @package Application\Wechat\Controller
 */
class IndexController {

    /**
     * 开发者的Token
     * @var string
     */
    protected $token = 'linzhv';

    /**
     * 微信入口
     * @param string $id 公众号ID
     */
    public function index($id){
        \Soya\dumpout($id);
        define('TOKEN', 'linzhv');
        if(isset($_GET['echostr'])){
            //valid
            if($this->checkSignature()){
                exit($_GET['echostr']);
            }
        }else{
            $message = new MessageInterface();
            $message->receive() and $message->response(function($type,$entity)use($message){
                $content = "消息类型是'$type':   \n消息体：";
                return $message->responseText($content.var_export($entity,true));
            });
        }
        exit();
    }

    /**
     * 检查签名
     * @return bool
     */
    private function checkSignature()     {
        $tmpArr = array($this->token, $_GET['timestamp'], $_GET['nonce']);
        sort($tmpArr, SORT_STRING);
        return sha1(implode($tmpArr)) === $_GET['signature'];
    }


}