<?php

namespace Aliyun\Controller;

use Aliyun\Model\AliyunConfigModel;
use Aliyun\Service\AliyunConfigService;
use Common\Controller\AdminBase;

/**
 * 阿里云公共配置
 * Class AliyunconfigController
 * @package Aliyun\Controller
 */
class AliyunconfigController extends AdminBase
{

    /**
     * 阿里云公共配置
     */
    public function confing()
    {
        if (IS_AJAX) {
            $post = I('post.');
            $res = AliyunConfigService::addEditConfig($post);
            $this->ajaxReturn($res);
        } else {

            $AliyunConfigModel = new AliyunConfigModel();
            $info = $AliyunConfigModel->find();

            $this->assign('info',$info);
            $this->display();
        }
    }

}