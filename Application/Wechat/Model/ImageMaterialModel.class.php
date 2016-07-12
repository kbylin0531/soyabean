<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/12/16
 * Time: 3:45 PM
 */

namespace Application\Wechat\Model;
use Soya\Extend\Model;

class ImageMaterialModel extends Model {

    protected $tablename = 'sy_material_image';
    private $accountid = null;

    public function __construct($aid) {
        parent::__construct();
        $this->accountid = intval($aid);
    }

    public function getImage($id){


    }

    public function createImage(){

    }

    public function countImage(){
        return $this->where('aid ='.$this->accountid)->count();
    }
    public function selectImage($offset=0,$limit=10){
        $where = 'aid = '.$this->accountid;
        $result = $this->where($where)->limit($limit,$offset)->order('ctime desc')->select();
        if(false === $result){
            return false;
        }else{
            return $result;
        }
    }

    public function deleteImage(){

    }

    public function updateImage(){

    }

}