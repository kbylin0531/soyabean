<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/9/16
 * Time: 7:01 PM
 */

namespace Application\Wechat\Common\Library;
use Kbylin\System\Utils\Network;
use Soya\Util\SEK;

/**
 * Class MaterialInterface 素材管理接口
 * 注意：
 * 1、新增的永久素材也可以在公众平台官网素材管理模块中看到
 * 2、永久素材的数量是有上限的，请谨慎新增。图文消息素材和图片素材的上限为5000，其他类型为1000
 * 3、素材的格式大小等要求与公众平台官网一致。具体是，图片大小不超过2M，支持bmp/png/jpeg/jpg/gif格式，
 *    语音大小不超过5M，长度不超过60秒，支持mp3/wma/wav/amr格式
 *
 *
 * @package Application\Wechat\Common\Library
 */
class MaterialInterface extends Wechat{

    /**
     * 新增永久图文素材
     * {
     * "articles": [{
     *  "title": TITLE,
     *  "thumb_media_id": THUMB_MEDIA_ID, //图文消息的封面图片素材id（必须是永久mediaID）
     *  "author": AUTHOR,
     *  "digest": DIGEST, //图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
     *  "show_cover_pic": SHOW_COVER_PIC(0 / 1), //是否显示封面，0为false，即不显示，1为true，即显示
     *  "content": CONTENT, //图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS
     *  "content_source_url": CONTENT_SOURCE_URL //图文消息的原文地址，即点击“阅读原文”后的URL
     *  },
     *  //若新增的是多图文素材，则此处应还有几段articles结构
     *  ]
     * }
     *
     * 注意：在图文消息的具体内容中，将过滤外部的图片链接，开发者可以通过下述接口上传图片得到URL，放到图文内容中使用
     *
     * @param array $articles 文章列表
     * @return string 返回的即为新增的图文消息素材的media_id
     */
    public function addNews(array $articles){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token='.$this->getAccessToken();
        $items = [
            'title' ,
            'thumb_media_id' ,
            'author' ,
            'digest' ,
            'show_cover_pic' ,
            'content' ,
            'content_source_url' ,
        ];
        //检查有效性
        foreach ($articles as $article) {
            foreach ($items as $item){
                if(empty($article[$item])){
                    $this->error = "添加文章时'$item'不能为空！";
                    return false;
                }
            }
        }
        $result = Network::post4Json($url,$articles);
        if (empty($result['errcode'])) {
            return $result['media_id'];
        }else{
            $this->error = $result['errmsg'];
            return false;
        }
    }

    /**
     * 上传文章图片
     * @param array $media
     * @return bool |string 返回上传图片的url，可用于后续群发中，放置到图文消息中
     */
    public function addNewsImg($media){
        $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$this->getAccessToken();


        //form-data中媒体文件标识，有filename、filelength、content-type等信息
        $result = Network::post4Json($url,$media);
        if (empty($result['errcode'])) {
            return $result['url'];
        }else{
            $this->error = $result['errmsg'];
            return false;
        }
    }

    //媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
    const MEDIA_TYPE_IMAGE = 'image';
    const MEDIA_TYPE_VOICE = 'voice';
    const MEDIA_TYPE_VIDEO = 'video';
    const MEDIA_TYPE_THUMB = 'thumb';

    /**
     * 新增其他类(非news)型永久素材
     * @param string $type 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @param string $path 上传的文件路径
     * @param string $title 上传视频素材额额爱增加的标题信息
     * @param string $introduction 上传视频素材额额爱增加的标题信息
     * @return bool
     */
    public function addMaterial($type,$path,$title='',$introduction=''){
        $at = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token={$at}&type={$type}";
        $params = [
            'type'  => $type,
            'media' => '@'.realpath ($path), //form-data中媒体文件标识，有filename、filelength、content-type等信息
        ];
        $params ['type'] = $type;
        $params ['media'] = '@' . realpath ( $path );
        if ($type === self::MEDIA_TYPE_VIDEO) {
            $params ['description'] = [];
            $params ['description'] ['title'] = $title;
            $params ['description'] ['introduction'] = $introduction;
            $params ['description'] = SEK::toJson($params ['description'] );
        }

        $result = Network::post4Json($url,$params);
        if (empty($result['errcode'])) {
            return $result;
        }else{
            $this->error = $result['errmsg'];
            return false;
        }
    }














    public function updateNews(){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/update_news?access_token=';
    }
    /**
     * 获取素材总数
     * voice_count	语音总数量
     * video_count	视频总数量
     * image_count	图片总数量
     * news_count	图文总数量
     * @param string $type
     */
    public function getMaterialcount($type=null){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token=';
    }

    /**
     * 获取永久素材的列表
     * @param string $type 素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
     * @param int $limit 返回素材的数量，取值在1到20之间
     * @param int $offset 从全部素材的该偏移位置开始返回，0表示从第一个素材 返回
     */
    public function batchgetMaterial($type,$limit=20,$offset=0){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=';

    }

}