<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/10/16
 * Time: 10:15 AM
 */

namespace Application\Wechat\Controller;
use Application\System\Common\Library\HomeController;
use Application\System\Member\Common\Logic\LoginLogic;
use Application\Wechat\Model\AccountModel;
use Soya\Core\URI;
use Soya\Extend\Logger;
use Soya\Extend\Response;
use Soya\Extend\Session;

/**
 * Class AccountController 微信公众号管理
 * @package Application\Wechat\Controller
 */
class AccountController extends HomeController {

    public function lists(){
        $uid = LoginLogic::getInstance()->getLoginInfo('id');

        Logger::getInstance()->write($uid);

        $accountModel = new AccountModel();
        $list = $accountModel->getAccountList($uid);
        if(false === $list){
            //TODO:记录出错信息
//            $error = $accountModel->error();
            $list = [];
        }
        $this->assign('list',$list);
        $this->show();
    }

    public function stepFirst($id=null,$name=null,$origin_id=null,$wechat=null,$token=null,$type=0){
        if(IS_METHOD_POST){
            empty($name) and Response::failed('公众号名称不能为空！');
            empty($origin_id) and Response::failed('原始ID不能为空！');
            empty($wechat) and Response::failed('微信号不能为空！');
            $session = Session::getInstance();
            if(intval($id) >= 0){
                //添加
                $accountModel = new AccountModel();
                $result = $accountModel->updateAccount($id,[
                    'name'  => $name,
                    'origin_id' => $origin_id,
                    'wechat'    => $wechat,
                    'token'     => $token,
                    'type'     => $type,
                ]);
                $session->set('account_id',$id);
                $session->set('account_token',$token);
                if(false !== $result){//修改前和修改后的数据一样时返回 0
                    Response::ajaxBack([
                        'type'  => 1,
                    ]);
                }else{
                    Response::ajaxBack([
                        'type'  => 0,
                        'message'   => '公众号修改失败！',
                    ]);
                }
            }else{
                //修改
                $uid = LoginLogic::getInstance()->getLoginInfo('id');
                $accountModel = new AccountModel();
                $result = $accountModel->createAccount([
                    'uid'   => $uid,
                    'name'  => $name,
                    'origin_id' => $origin_id,
                    'wechat'    => $wechat,
                    'token'     => $token,
                    'type'     => $type,
                ]);
                if($result){
                    $session->set('account_id',$result);
                    $session->set('account_token',$token);
                    Response::ajaxBack([
                        'type'  => 2,
                    ]);
                }else{
                    Response::ajaxBack([
                        'type'  => 0,
                        'message'   => '公众号添加失败！',
                    ]);
                }
            }
        }
        if(isset($id)){
            $accountModel = new AccountModel();
            $info = $accountModel->getAccountById($id);
        }
        empty($info) and $info = [
            'id'        => '-1',
            'name'      => '',
            'origin_id' => '',
            'wechat'    => '',
            'token'     => '',
            'type'      => 0,
        ];
        $this->assign($info);
        $this->show();
    }
    public function stepSecond(){
        $session = Session::getInstance();
        $id = $session->get('account_id');
        $token = $session->get('account_token');
        $url = URI::getBasicUrl(null,null,true)."/wechat/{$id}";

        $session->set('account_url',$url);
        $this->assign([
            'url'   => $url,
            'token' => $token,
        ]);

        $this->show();
    }

    /**
     * @param string|null $appid
     * @param string|null $secret
     * @param string|null $encodingaeskey
     */
    public function stepThird($appid=null,$secret=null,$encodingaeskey=null){
        $session = Session::getInstance();
        $accountModel = new AccountModel();
        $id = $session->get('account_id');
        if(IS_METHOD_POST){
            empty($appid) and Response::failed('应用ID不能为空！');
            empty($secret) and Response::failed('应用密钥不能为空！');
            $result = $accountModel->updateAccount($id,[
                'appid'         => $appid,
                'appsecret'     => $secret,
                'encodingaeskey'=> $encodingaeskey,
            ]);
            if($result){
                $session = Session::getInstance();
                $session->delete('account_id');
                $session->delete('account_token');
                Response::ajaxBack([
                    'type'  => 1,
                    'message'   => 'accound_id = '.$id,
                ]);
            }else{
                Response::ajaxBack([
                    'type'  => 0,
                    'message'   => '公众号修改失败！',
                ]);
            }
        }

        if(intval($id) >= 0){
            $info = $accountModel->getAccountById($id);
        }
        if(empty($info)){
            $info = [
                'appid'         => '',
                'appsecret'     => '',
                'encodingaeskey'=> '',
            ];
        }
        $this->assign('info',$info);

//        \Soya\dumpout($info);

        $this->show();
    }

    /**
     * 添加公众号
     */
    public function add(){
        $this->show();
    }

    /**
     * 取消公众号绑定
     * @param int $id 公众号ID
     */
    public function del($id){

    }

    public function waitAudit(){
        $this->show();
    }
    public function checkRes(){
        $this->show();
    }


}