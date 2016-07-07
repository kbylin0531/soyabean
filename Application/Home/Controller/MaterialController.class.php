<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/7/16
 * Time: 6:10 PM
 */

namespace Application\Home\Controller;

use Application\System\Common\Library\HomeController;

/**
 * Class MaterialController 素材管理
 * @package Application\Home\Controller
 */
class MaterialController extends HomeController
{
    public function lists()
    {
        $this->show('material_lists');
    }

    public function doAdd()
    {
    }

    function material_lists()
    {
        $this->show();
    }

    function add_material()
    {
        $this->display();
    }

    function del_material_by_id()
    {
    }

    function del_material_by_groupid()
    {
    }

    function material_data()
    {
        $this->display();
    }

    function get_news_by_group_id()
    {
    }

    // 与微信同步
    function syc_news_to_wechat()
    {
    }

    // 获取图文素材url
    function _news_url()
    {
    }

    function syc_news_from_wechat()
    {
    }

    function _thumb_media_id()
    {
    }

    function _image_media_id($cover_id)
    {
    }

    function _download_imgage($media_id, $picUrl = '', $dd = NULL)
    {
    }

    function news_detail()
    {
        $this->display();
    }

    /**
     * ********************************图片素材*************************************************
     */
    function picture_lists()
    {
        $this->display();
    }

    function add_picture()
    {
    }

    function del_picture()
    {
    }

    function picture_data()
    {
        $this->display();
    }

    // 根据id获取图片素材,设置欢迎语用到
    function ajax_picture_by_id()
    {
    }

    // 上传图片素材
    function syc_image_to_wechat()
    {
    }

    // 下载图片
    function syc_image_from_wechat()
    {
    }

    /**
     * ********************************音频素材*************************************************
     */

    function do_down_file()
    {
    }

    // 根据id获取图片素材,设置欢迎语用到
    function ajax_voice_by_id()
    {
    }

    function voice_lists()
    {
        $this->display();
    }

    function voice_data()
    {
        $this->display();
    }

    function voice_add()
    {
        $this->display('add');
    }

    function voice_del()
    {
    }

    function voice_edit()
    {
        $this->display('Addons:edit');
    }

    // 下载音频
    function _voice_download($media_id, $cover_url)
    {
    }

    function syc_voice_to_wechat()
    {
    }

    /**
     * ********************************视频素材*************************************************
     */
    function video_lists()
    {
        $this->display();
    }

    // 根据id获取图片素材,设置欢迎语用到
    function ajax_video_by_id()
    {
    }

    function video_data()
    {
        $this->display();
    }

    function video_add()
    {
        $this->display('Addons:add');
    }

    function video_del()
    {
    }

    function syc_video_to_wechat()
    {
    }

// 下载音频：未实现
    function _video_download($media_id, $cover_url)
    {
    }

    /**
     * *******************多媒体共用***********************
     */
    function syc_file_from_wechat()
    {
    }

// 上传视频、语音素材
    function _get_file_media_id($file_id, $type = 'voice', $title = '', $introduction = '')
    {
    }

    /**
     * ********************************文本素材*************************************************
     */
    function text_lists()
    {
//        $this->display ( 'text_lists_data' );
        $this->display('Addons:lists');
    }

// 根据id获取文本素材,设置欢迎语用到
    function ajax_text_by_id()
    {
    }

    function text_add()
    {
        $this->display('Addons:add');
    }

    function text_del()
    {
    }

    function text_edit()
    {
        $this->display('Addons:edit');
    }

    function get_content_by_id()
    {
    }

    function check_file_size($fileId, $limSize, $strExt = 'mp3,wma,wav,amr', $checkExt = 1)
    {
    }


}