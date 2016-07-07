<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/7/16
 * Time: 6:10 PM
 */

namespace Application\Home\Controller;
use Application\System\Common\Library\HomeController;
use Soya\Core\URI;
use Soya\Extend\Page;

/**
 * Class MaterialController 素材管理
 * @package Application\Home\Controller
 */
class MaterialController extends HomeController {

    private function getNav() {
        $nav = [];
        $act = strtolower ( REQUEST_ACTION );
//        $param = array('mdm'=>I('mdm'));
        $res ['title'] = '图文素材';
        $res ['url'] =  URI::url( 'materialLists' );
        $res ['class'] = $act == 'materialLists' ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '图片素材';
        $res ['url'] = URI::url ( 'picture_lists' );
        $res ['class'] = strpos ( $act, 'picture' ) !== false ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '语音素材';
        $res ['url'] = URI::url ( 'voice_lists' );
        $res ['class'] = strpos ( $act, 'voice' ) !== false ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '视频素材';
        $res ['url'] = URI::url ( 'video_lists' );
        $res ['class'] = strpos ( $act, 'video' ) !== false ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '文本素材';
        $res ['url'] = URI::url ( 'text_lists' );
        $res ['class'] = strpos ( $act, 'text' ) !== false ? 'current' : '';
        $nav [] = $res;

        return $nav;
    }

    public function add(){
        $this->show();
    }

    public function addMaterial(){
        $this->show();
    }

    public function materialData(){
        $this->show();
    }

    public function materialLists(){
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

    public function textListsData(){
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