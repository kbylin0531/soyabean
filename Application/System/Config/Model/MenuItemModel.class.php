<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/29
 * Time: 21:32
 */

namespace Application\System\Config\Model;
use Soya\Extend\Model;

/**
 * Class MenuItemModel 菜单项管理
 * @package Application\Admin\System\Model
 */
class MenuItemModel extends Model {

    protected $tablename = 'sy_menu_item';

    /**
     * update the menu item by id
     * @param $id
     * @param $title
     * @param $icon
     * @param $href
     * @return bool
     */
    public function updateMenuItemById($id,$title,$icon,$href){
        return $this->fields([
            'title' => $title,
            'icon'  => $icon,
            'value' => $href,
        ])->where('id = '.intval($id))->update();
    }

    /**
     * delete menu item by id
     * @param $id
     * @return bool
     */
    public function deleteMenuItemById($id){
//        $id = intval($id);
//        $feather = '"id";i:'.$id;
//        $result = $this->where("[value] like '%".$feather."%' ")->select();
//        dumpout($result,$this->error());
        return $this->where('id = '.$id)->delete();
    }

    /**
     * 创建菜单项目
     * @param $id
     * @param $title
     * @param string $href
     * @param string $icon
     * @return bool
     */
    public function createMenuItem($id,$title,$href,$icon) {
        $result = $this->fields([
            'id'        => $id, // 前台保证唯一
            'title'     => $title,
            'value'     => $href,
            'icon'      => $icon,
        ])->create();
        return $result;
    }

    /**
     * 获取菜单项列表
     * @param bool $idaskey 是否将id作为键
     * @return array|bool
     */
    public function listMenuItems($idaskey=false){
        $items = $this->select();
        if($idaskey and $items){
            $temp = [];
            foreach ($items as $key=>$item){
                $temp[$item['id']] = $item;
                unset($temp[$item['id']]['id']);
            }
            $items =$temp;
        }
        return $items;
    }

    public function hasMenuItemById($id){
        return $this->where('id = '.intval($id))->count();
    }

    public function updateMenuItem($id,$title,$href,$icon){
        return $this->fields([
            'title'     => $title,
            'value'     => $href,
            'icon'      => $icon,
        ])->where('id='.intval($id))->update();
    }


}