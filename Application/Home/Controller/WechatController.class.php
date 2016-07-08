<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/8/16
 * Time: 10:14 PM
 */

namespace Application\Home\Controller;

/**
 * Class WechatController
 *
 * 微信交互控制器
 * 主要获取和反馈微信平台的数据
 *
 * @package Application\Home\Controller
 */
class WechatController {

    private $_token = '';

    private $_data = [];

    public function index() {
        // 删除微信传递的token干扰
        unset ( $_REQUEST ['token'] );

    }


}