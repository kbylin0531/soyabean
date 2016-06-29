<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/29
 * Time: 21:14
 */
namespace Application\System\Common\Library;


use Soya\Core\Storage;
use Soya\Util\SEK;

class ModuleButler {

    /**
     * 加载模块目录下的
     * 配置目录在模块目录下的'Common/Config'
     * @param string $name 配置名称,多个名称以'/'分隔
     * @param string $type 配置类型,默认为php
     * @return array 配置为空或者找不到配置时返回空数组
     */
    public static function loadConfig($name,$type=SEK::CONF_TYPE_PHP){
        $place = SEK::getCallPlace(SEK::CALL_ELEMENT_FILE,SEK::CALL_PLACE_SELF);
        $targetdir = dirname($place[SEK::CALL_ELEMENT_FILE]);
        $temp = null;
        while(true){
            $assetsDir = "{$targetdir}\\Common\\Config";//如果存在这个目录,说明抵达了这个文件
            $storage = Storage::getInstance();
            if($storage->has($assetsDir) === Storage::IS_DIR){
                $file = "{$assetsDir}\\{$name}.".$type;
                return SEK::parseConfigFile($file);
            }
            //抵到了根目录的清空下
//            dump("{$targetdir}\\Controller",$targetdir);
            if($targetdir === $temp) break;
            $temp = $targetdir;
            $targetdir = dirname($targetdir);
        }
        return [];
    }

}