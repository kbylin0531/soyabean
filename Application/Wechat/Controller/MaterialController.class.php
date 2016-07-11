<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/11/16
 * Time: 11:11 AM
 */

namespace Application\Wechat\Controller;
use Application\System\Common\Library\HomeController;
use Application\Wechat\Model\MaterialModel;
use Soya\Core\URI;
use Soya\Util\SEK;

/**
 * Class MaterialController 素材管理
 * @package Application\Wechat\Controller
 */
class MaterialController extends HomeController {

    private function getNav() {
        $nav = [];
        $act = strtolower ( REQUEST_ACTION );
        //& 前面为选择请，后面为属性，如果属性为空，则为innerHTML
        $res ['title'] = '图文素材';
        $res ['url'] =  URI::url( 'materialLists' );
        $res ['class'] = stripos ( $act, 'material' ) !== false  ? 'current' : '';
        $nav [] = $res;

        $res ['titletext'] = '图片素材';
        $res ['url'] = URI::url ( 'pictureLists' );
        $res ['class'] = stripos ( $act, 'picture' ) !== false ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '语音素材';
        $res ['url'] = URI::url ( 'voiceLists' );
        $res ['class'] = stripos ( $act, 'voice' ) !== false ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '视频素材';
        $res ['url'] = URI::url ( 'videoLists' );
        $res ['class'] = stripos ( $act, 'video' ) !== false ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '文本素材';
        $res ['url'] = URI::url ( 'textListsData' );
        $res ['class'] = stripos ( $act, 'text' ) !== false ? 'current' : '';
        $nav [] = $res;
        return $nav;
    }

    protected function show($template=null){
        $nav = $this->getNav();
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

    public function pictureLists(){
        $this->show();
    }

    public function textLists(){
        $this->show();
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