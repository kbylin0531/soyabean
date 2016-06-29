<?php

/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/6/29
 * Time: 21:00
 */
namespace Application\System\Member\Model;
use Soya\Extend\Model;

class MemberModel extends Model {

    protected $tablename = 'sy_member';

    public function getUserInfo($username){
        $result = $this->fields('username,password')->where(['username'=>$username])->find();
        return $result;
    }

}