<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/9/16
 * Time: 7:04 PM
 */

namespace Application\Wechat\Common\Library;

/**
 * Class MenuInterface 自定义菜单创建接口
 * @package Application\Wechat\Common\Library
 */
class MenuInterface {
    /**
     * 自定义菜单创建
     * @var string
     */
    protected $create_url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=';
    /**
     * 自定义菜单查询接口
     * @var string
     */
    protected $get_url    = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=';
    /**
     * 自定义菜单删除接口
     * @var string
     */
    protected $delete_url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=ACCESS_TOKEN';


    //------------------- 个性化菜单：不同的团体看到不同的菜单 -----------------------------//
    protected $delconditional_create_url = 'https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token=';
    protected $delconditional_delete_url = 'https://api.weixin.qq.com/cgi-bin/menu/delconditional?access_token=';
    protected $delconditional_trymatch_url = 'https://api.weixin.qq.com/cgi-bin/menu/trymatch?access_token=';


}