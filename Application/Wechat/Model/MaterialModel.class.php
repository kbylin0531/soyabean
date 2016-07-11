<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/11/16
 * Time: 12:48 PM
 */

namespace Application\Wechat\Model;
use Application\Wechat\Common\Library\MaterialInterface;
use Soya\Extend\Model;

/**
 * Class MaterialModel 素材管理
 * @package Application\Wechat\Model
 */
class MaterialModel extends Model {

    protected $tablename = '';

    /**
     * 获取文本素材列表
     */
    public function selectText(){
    }

    /**
     * 添加文本素材
     */
    public function addText(){}

    /**
     * 删除文本素材
     */
    public function deleteText(){}

    /**
     * 修改文本素材
     */
    public function updateText(){}

}