<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/10/16
 * Time: 10:15 AM
 */

namespace Application\Wechat\Controller;
use Application\System\Common\Library\HomeController;

/**
 * Class AccountController 微信公众号管理
 * @package Application\Wechat\Controller
 */
class AccountController extends HomeController {

    public function lists(){
        $this->show();
    }

    public function stepFirst(){
        $this->show();
    }
    public function stepSecond(){
        $this->show();
    }
    public function stepThird(){
        $this->show();
    }

    public function add(){
        $this->show();
    }

    public function waitAudit(){
        $this->show();
    }
    public function checkRes(){
        $this->show();
    }


}