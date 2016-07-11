<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/11/16
 * Time: 12:52 PM
 */

namespace Application\Wechat\Common\Library;


use Application\Wechat\Model\AccountModel;
use Kbylin\System\Utils\Network;
use Soya\Core\Exception;
use Soya\Extend\Logger;
use Soya\Extend\Session;

class Wechat {
    /**
     * 访问的账户ＩＤ
     * @var int
     */
    private $account_id = 0;
    /**
     * 访问的账户信息
     * @var array
     */
    private $account_info = [];
    /**
     * Token
     * @var string
     */
    private $token = '';
    /**
     * 微信access_token
     * @var string
     */
    private $access_token = '';
    /**
     * 过期时间
     * @var int
     */
    private $access_token_expire = 0;

    /**
     * Wechat constructor.
     * @param int $id 请求的账号ID
     */
    public function __construct($id){
        $this->account_id = $id;
    }

    /**
     * 验证消息真实性
     * @return bool
     */
    public function checkSignature() {
        $tmpArr = [$this->getToken(), $_GET['timestamp'], $_GET['nonce']];
        sort($tmpArr, SORT_STRING);
        return sha1(implode($tmpArr)) === $_GET['signature'];
    }

    /**
     * 错误信息
     * @var string
     */
    protected $error = null;

    public function getError(){
        return $this->error;
    }

    /**
     * 获取账户ID
     * @return int
     */
    public function getAccountId(){
        return $this->account_id;
    }

    /**
     * 获取用户信息
     * @return array
     * @throws Exception
     */
    public function getAccountInfo(){
        if(empty($this->account_info)){
            $model = AccountModel::getInstance();
            $this->account_info = $model->getAccountById($this->account_id);
            if(false === $this->account_info){
                Logger::getInstance()->write($model->error());
                Exception::throwing('获取账户信息！');
            }
        }
        return $this->account_info;
    }

    /**
     * 获取token信息
     * @return string
     */
    public function getToken(){
        if(empty($this->token)){
            $info = $this->getAccountInfo();
            $this->token = $info['token'];
        }
        return $this->token;
    }

    /**
     * 获取access_token
     * @return mixed
     */
    public function getAccessToken(){
        if(!$this->access_token or (REQUEST_TIME > $this->access_token_expire)){
            $info = $this->getAccountInfo();
            if(empty($info['access_token']) or empty($info['access_token_expire'])){
                $info = $this->_requestAccessToken($info['appid'],$info['appsecret']);
                $model = AccountModel::getInstance();
                $result = $model->updateAccount($this->account_id,$info);
                if(false === $result){
                    Exception::throwing($model->error());
                }
            }
            $this->access_token = $info['access_token'];
            $this->access_token_expire = $info['access_token_expire'];
            return $this->getAccessToken();//再一次判断
        }
        return $this->access_token;
    }

    /**
     * 请求获取access_token
     * @param string $appid
     * @param string $appsecret
     * @return string
     */
    protected function _requestAccessToken($appid,$appsecret){
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
        $jsoninfo = Network::get4Json($url);
        return [
            'access_token'          =>$jsoninfo['access_token'],
            'access_token_expire'   => REQUEST_TIME + intval($jsoninfo['expires_in']),
        ];
    }


}