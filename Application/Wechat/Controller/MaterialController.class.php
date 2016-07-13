<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/11/16
 * Time: 11:11 AM
 */

namespace Application\Wechat\Controller;
use Application\System\Common\Library\HomeController;
use Application\Wechat\Model\ImageMaterialModel;
use Application\Wechat\Model\MaterialModel;
use Application\Wechat\Model\TextMoterialModel;
use Soya\Core\URI;
use Soya\Extend\Page;
use Soya\Extend\Response;
use Soya\Extend\Session;
use Soya\Extend\Uploader;
use Soya\Util\SEK;

/**
 * Class MaterialController 素材管理
 * @package Application\Wechat\Controller
 */
class MaterialController extends HomeController {

    private function getNav($aid) {
        $nav = [];
        $act = strtolower ( REQUEST_ACTION );
        //& 前面为选择请，后面为属性，如果属性为空，则为innerHTML
        $res ['title'] = '图文素材';
        $res ['url'] =  URI::url( 'materialLists' ,['aid'=>$aid]);
        $res ['class'] = stripos ( $act, 'material' ) !== false  ? 'current' : '';
        $nav [] = $res;

        $res ['titletext'] = '图片素材';
        $res ['url'] = URI::url ( 'imageLists',['aid'=>$aid] );
        $res ['class'] = stripos ( $act, 'picture' ) !== false ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '语音素材';
        $res ['url'] = URI::url ( 'voiceLists' ,['iad'=>$aid]);
        $res ['class'] = stripos ( $act, 'voice' ) !== false ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '视频素材';
        $res ['url'] = URI::url ( 'videoLists' ,['aid'=>$aid]);
        $res ['class'] = stripos ( $act, 'video' ) !== false ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '文本素材';
        $res ['url'] = URI::url ( 'textLists' ,['id'=>$aid]);//TODO:
        $res ['class'] = stripos ( $act, 'text' ) !== false ? 'current' : '';
        $nav [] = $res;
        return $nav;
    }

    protected function show($template=null){
        $nav = $this->getNav(1);
        //nav.html
        $this->assign('nav',$nav);
        $this->assign('sub_nav',[]);
        $this->assign('normal_tips','');

        null === $template and $template = SEK::backtrace(SEK::ELEMENT_FUNCTION,SEK::PLACE_FORWARD);
        parent::show($template);
    }

    public function materialLists(){
        $this->show();
    }


    public function add(){
        $this->assign('list_data',[]);
        $this->show();
    }

    public function addMaterial(){
        $this->show();
    }

    public function materialData(){
        $this->show();
    }

    public function newsDetail(){
        $this->show();
    }

    public function pictureData(){
        $this->show();
    }

    public function imageLists($aid,$page=1){
        $model = new ImageMaterialModel($aid);
        $list = $model->selectImage((intval($page)-1)*10,10);
        $page = (new Page($model->countImage(),10,[
            'page'  => $page,
            'id'    => $aid,
        ]))->show('',true);
        $this->assign('page',$page);
        $this->assign ( 'list_data',  $list );
        $this->assign('aid',$aid);
        $this->show();
    }

    public function imageAdd(){
        /* 返回标准数据 */
        $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

        /* 调用文件上传组件上传文件 */
        $Picture = new Uploader();
        $info = $Picture->upload('/wechat/image');

        /* 记录图片信息 */
        if($info){
            $return['status'] = 1;
            $return = array_merge($info['download'], $return);
        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }
    }
    public function _uploadImage(){
        $this->show();
    }

    /**
     * 获取文本列表
     * @param int $id account_id
     * @param string $search search key
     * @param int $page
     */
    public function textLists($id,$search='',$page=1){
        $textModel = new TextMoterialModel($id);
        $list = $textModel->getTextList((intval($page)-1)*10,10,$search);//offset = 0,limit = 10
        $this->assign('data_list',$list);
        $total = $textModel->countTextList();

        $page = (new Page($total,10,[
            'page'  => $page,
            'id'    => $id,
        ]))->show('',true);
        $this->assign('page',$page);
        $this->assign('id',$id);//aid
        $this->assign('search',$search);
        $this->show();
    }
    public function textAdd($id,$content=''){
        if(IS_METHOD_POST and $id){
            $textModel = new TextMoterialModel($id);
            $result = $textModel->createText($content);
            $result?Response::success('success to add !'):Response::failed('failed to add!');
        }
        $this->assign('id',$id);
        $this->show();
    }

    public function textEdit($aid,$id,$content=''){
        $textModel = new TextMoterialModel($aid);
        if(IS_METHOD_POST){
            $result = $textModel->updateText($id,$content);
            $result?Response::success('success to update !'):Response::failed('failed to update!');
        }
        $info = $textModel->getText($id);
        $this->assign('aid',$aid);
        $this->assign('id',$id);
        $this->assign('content',$info['content']);
        $this->show('textAdd');
    }
    public function textRemove($aid,$id){
        $textModel = new TextMoterialModel($aid);
        $result = $textModel->deleteText($id);
        $result?Response::success('success to remove !'):Response::failed('failed to remove!');
    }

    public function videoData(){
        $this->show();
    }

    public function videoLists(){
        $this->show();
    }

    public function voiceData(){
        $this->show();
    }

    public function voiceLists(){
        $this->show();
    }

}