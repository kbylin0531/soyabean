<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/29
 * Time: 21:20
 */

namespace Application\System\Common\Library;
use Soya\Extend\Controller;
use Soya\Extend\Response;

abstract class CommonController extends Controller {

    public function PageIconSelection(){
        $this->display();
    }

    protected function go($path,$base=__ROOT__){
        $this->redirect($base.$path);
    }

}