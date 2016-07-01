<?php

/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/29
 * Time: 21:31
 */
namespace Application\System\Config\Model;
use Soya\Core\Exception;
use Soya\Extend\Model;
use Soya\Util\SEK;

/**
 * Class AdminMenuModel 系统菜单管理
 * @package Application\Admin\Model
 */
class MenuModel extends Model{

    protected $tablename = 'sy_menu';

    /**
     * 添加侧边栏菜单
     * @param array $info
     * @return bool
     */
    public function createSidedMenu(array $info){
        $data = [
            'title' => null,
            'value' => '',
            'icon'  => null,
            'order' => null,
            'status'    => null,
        ];
        is_array($info['value']) and $info['value'] = @serialize($info['value']);
        SEK::merge($data,$info);
        SEK::filter($data,[null,false]);
        return $this->fields($data)->create();
    }

    /**
     * 修改侧边栏菜单项目
     * @param array $info
     * @return bool
     */
    public function updateSideMenu(array $info){
        if(!isset($info)) {
            $this->error = '未设置ID项';
        }
        $data = [
            'title' => null,
            'value' => null,
            'icon'  => null,
            'order' => null,
            'status'=> null,
        ];
        is_array($info['value']) and $info['value'] = @serialize($info['value']);
        SEK::merge($data,$info);
        SEK::filter($data,[null,false]);
        $id = $info['id'];
        unset($info['id']);
        return $this->fields($info)->where('id = '.intval($id))->update();
    }

    public function deleteSideMenu($id){
        $result = $this->fields('value')->where('id = '.intval($id))->find();
        if(false === $result){
            return false;
        }elseif(empty($result)){
            $this->error = "ID为'{$id}'的配置项不存在";
            return false;
        }else{
            $result = @unserialize($result['value']);
            if(false !== $result){
                if(empty($result)){
                    return $this->fields([
                        'status'    => 0,
                    ])->where('id = '.intval($id))->update();
                }else{
                    $this->error = "无法删除ID为'{$id}'的非空配置项，";
                    return false;
                }
            }
            return false;
        }
    }

    /**
     * 获取全部的菜单项目
     * @param bool $idaskey
     * @return array|bool
     */
    public function selectSideMenu($idaskey=false){
        $list = $this->where('status = 1')->select();
        if($list){
            $temp = [];
            foreach ($list as &$item){
                $value = @unserialize($item['value']);
                if(false === $value){
                    //无法反序列化，保持不变
                    \Soya::trace(['无法反序列化菜单项的值',$item['value']]);
                    continue;
                }
                $item['value'] = $value;
                if($idaskey){
                    $id = $item['id'];
                    unset($item['id']);
                    $temp[$id] = $item;
                }
            }
            if($idaskey) return $temp;
        }
        return $list;
    }

    /**
     * 获取顶部菜单设置
     * @return array|false 错误发生时返回false
     */
    public function getHeaderMenuConfig(){
        $config = $this->selectSideMenu(true);
        if($config){
            $header = $config[1]['value'];
            $this->sortMenu($header,$config);
            return $header;
        }
        return false;
    }

    /**
     * 整理菜单配置
     * @param $header
     * @param $others
     */
    private function sortMenu(&$header,&$others){
        if(!isset($header['id'])){//是列表
            foreach ($header as &$item){
                if(isset($item['id'])){
                    $this->sortMenu($item,$others);
                }
                if(isset($item['children'])){
                    $this->sortMenu($item['children'],$others);
                }
            }
        }else{//是单个菜单项目
            if(isset($header['id'])){
                $id = $header['id'];
                isset($others[$id]) and $header = array_merge($header,$others[$id]);
            }
        }
    }

    /**
     * @return array|bool
     */
    public function getSidebarMenuConfig(){
        $configs = $this->where('id <> 1')->select();

        if($configs){
            return $this->applyMenuItem($configs);
        }
        return false;
    }

    /**
     * get all menu config with arranged
     * @return array|bool return array if success while false on failed
     */
    public function getMenuConfig(){
        $configs = $this->select();
        if(false === $configs or empty($configs)){
            return false;
        }else{
            return $this->applyMenuItem($configs);
        }
    }

    /**
     * 将菜单项配置应用到菜单配置中
     * @param array $menuconf 菜单项配置
     * @return array|bool
     */
    private function applyMenuItem(array $menuconf){
        $newconf = [];
        if($menuconf){
            $menuItemModel = new MenuItemModel();
            $items = $menuItemModel->selectMenuItem(true);

            \Soya\dumpout($items);
            if(empty($items)) return false;
//            dumpout($configs,$items);
            foreach ($menuconf as &$config){
                $parent = $config['parent'];
                $title  = $config['title'];

                if(!empty($config['value'])){
                    $config = unserialize($config['value']);
                    $this->_arrangeMenu($config, $items);
                }else{
                    $config = [];
                }
                $newconf[$parent] = [
                    'title' => $title,
                    'config'=> $config,
                ];
            }
        }
        return $newconf;
    }

    /**
     * apply menuitem to menu config
     * @param array $config
     * @param array $items
     */
    private function _arrangeMenu(array &$config,array $items){
        foreach ($config as &$configitem){
            $id = $configitem['id'];
            if(!isset($items[$id])) continue;
            $configitem = array_merge($configitem,$items[$id]);
            if(isset($configitem['children'])){
                $this->_arrangeMenu($configitem['children'],$items);
            }
        }
    }

    /**
     * @param array $sideset
     * @param int $id
     * @return bool
     */
    public function setSideMenu($sideset,$id){
        if(is_string($sideset)) $sideset = json_decode($sideset);
        is_array($sideset) or Exception::throwing('Menu setting should be array/string(json)');

        $config = $this->_travelThrough($sideset);
        if(empty($config)){
            $config = '[]';
        }else{
            $config = serialize($config);
        }

        $where = 'parent = '.intval($id);
        //check if exist
        $result = $this->where($where)->count();
        if(false === $result){
            return false;
        }
        if($result){
            return $this->fields([
                'value' => $config,
            ])->where($where)->update();
        }else{
            return $this->fields([
                'value'     =>  $config,
                'parent'    =>  $id,
            ])->create();
        }
    }

    /**
     * @param array $topset
     * @return bool
     */
    public function setTopMenu($topset){
        if(is_string($topset)) $topset = json_decode($topset);
        is_array($topset) or Exception::throwing('Menu setting should be array/string(json)');

        $config = $this->_travelThrough($topset);
        if(empty($config)){
            $config = '[]';
        }else{
            $config = serialize($config);
        }
//        dumpout($config);;
        return $this->fields([
            'value' => $config,
        ])->where('id = 1')->update();
    }

    /**
     * @param array $topset
     * @return array
     */
    private function _travelThrough(array $topset){
        $result = [];
        foreach ($topset as $object){
            $item = [];
            $item['id'] = $object->id;
            if(isset($object->children)){
                $item['children'] = $this->_travelThrough($object->children);
            }
            $result[] = $item;
        }
        return $result;
    }

}