<?php

namespace Aliyun\Service;

use System\Service\BaseService;

class AliyunConfigService extends BaseService
{

    /**
     * 添加或者编辑阿里云配置
     * @param array $post
     * @return array
     */
    static function addEditConfig($post = []){
        $id = $post['id'];
        if($id) {
            $post['edit_time'] = time();
            return self::update('aliyun_config',$id,$post);
        } else {
            return self::create('aliyun_config',$post);
        }
    }


}