<?php
/**
 * Created by PhpStorm.
 * User: lnzhv
 * Date: 7/11/16
 * Time: 8:46 PM
 */

namespace Application\Wechat\Model;
use Soya\Extend\Model;

class TextMoterialModel extends Model {

    protected $tablename = 'sy_material_text';

    protected $accoutid = null;

    public function __construct($id){
        parent::__construct();
        $this->accoutid = intval($id);
    }

    /**
     * 添加文本素材
     * @param string $content 文本内容
     * @return int 文本内容ID
     */
    public function createText($content){
        return $this->fields([
            'aid'       => $this->accoutid,
            'content'   => $content,
        ])->create();
    }
    public function countTextList(){
        return $this->where('aid ='.$this->accoutid)->count();
    }

    /**
     * 获取文本内容
     * @param int $id
     * @param mixed $replacement
     * @return string|false 返回文本内容
     */
    public function getTextList($offset=0,$limit=10,$id=null,$replacement=null){
        $aid = $this->accoutid;
        if(!$id){
            //获取全部
            $result = $this->where(" aid = {$aid} ")->limit($limit,$offset)->select();
        }else{
            $id = intval($id);
            $result = $this->where(" id = {$id} and aid = {$aid} ")->find();
        }
        if(false === $result){
            return false;
        }elseif(!$result){
            return $replacement;
        }else{
            return $result;
        }
    }

    /**
     * 修改文本内容
     * @param string $id
     * @param string $content
     * @return bool mysql判断是否更新成功依据内容是否发生变化，只要不出现错误都可以认为是修改成功的
     */
    public function updateText($id,$content){
        $aid = $this->accoutid;
        $id = intval($id);
        $result = $this->fields(['content'=> $content])->where(" id = {$id} and aid = {$aid} ")->update();
        if(false === $result){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 删除文本内容
     * @param int $id
     * @return bool
     */
    public function deleteText($id){
        $aid = $this->accoutid;
        $id = intval($id);
        $result = $this->where(" id = {$id} and aid = {$aid} ")->delete();
        if(false === $result){
            return false;
        }else{
            return true;
        }
    }

}