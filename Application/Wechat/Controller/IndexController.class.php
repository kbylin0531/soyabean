<?php

/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/8/16
 * Time: 9:47 PM
 */
namespace Application\Wechat\Controller;
use Application\Wechat\Common\Library\MessageInterface;
use Application\Wechat\Model\AccountModel;
use Soya\Extend\Logger;

/**
 * Class IndexController
 * @package Application\Wechat\Controller
 */
class IndexController {

    /**
     * 公众号信息
     * @var array
     */
    protected $info = null;

    /**
     * 微信入口
     * @param string $id 公众号ID
     */
    public function index($id){
        $info = $this->getAccountInfo($id);

        \Soya::closeTrace();

        if(false !== $info){
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
        }
//        //debug
//        $tmpArr = array($this->info['token'], $_GET['timestamp'], $_GET['nonce']);
//        sort($tmpArr, SORT_STRING);
//        $log =Logger::getInstance();
//        $log->write(var_export($this->info,true));
//        $log->write($_GET['signature']);
//        $log->write($tmpArr);
//        $log->write($_GET['echostr']);
//        $log->write(sha1(implode($tmpArr)));
        exit();

    }

    /**
     * 获取公众号信息
     * @param string $id 公众号ID
     * @return array|false
     */
    private function getAccountInfo($id){
        $accountModel = new AccountModel();
        return $this->info = $accountModel->getAccountById($id);
    }

    /**
     * 检查签名
     * @return bool
     */
    private function checkSignature() {
        $tmpArr = array($this->info['token'], $_GET['timestamp'], $_GET['nonce']);
        sort($tmpArr, SORT_STRING);
        return sha1(implode($tmpArr)) === $_GET['signature'];
    }

}