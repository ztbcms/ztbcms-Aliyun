<?php

namespace Aliyun\Controller;

use Aliyun\Model\AliyunPhoneConfigModel;
use Common\Controller\AdminBase;

use Aliyun\Service\AliyunPhoneService;


/**
 * 虚拟号码管理
 * Class VirtualphoneController
 * @package Aliyun\Controller
 */
class VirtualphoneController extends AdminBase
{

    /**
     * 基础设置
     */
    public function confing()
    {
        if (IS_AJAX) {
            $post = I('post.');
            $res = AliyunPhoneService::addEditConfig($post);
            $this->ajaxReturn($res);
        } else {
            $AliyunPhoneConfigModel = new AliyunPhoneConfigModel();
            $info = $AliyunPhoneConfigModel->find();

            $this->assign('info', $info);
            $this->display();
        }
    }

    /**
     * 获取绑定记录
     */
    public function bindList()
    {
        if (IS_AJAX) {
            $page = I('page', '1', 'trim');
            $limit = I('limit', '20', 'trim');
            $where = [];

            $bind_status = I('bind_status','','trim');
            if(is_numeric($bind_status)) $where['bind_status'] = $bind_status;

            $start_time = I('start_time','','trim');
            $end_time = I('end_time','','trim');
            if (!empty($start_time) && !empty($end_time)) {
                $start_time = strtotime($start_time);
                $end_time = strtotime($end_time) + 86399;
                $where['add_time'] = array(array('GT', $start_time), array('LT', $end_time), 'AND');
            }

            $phone_no_a = I('phone_no_a','','trim');
            if($phone_no_a) $where['phone_no_a'] = ['like',['%'.$phone_no_a.'%']];

            $phone_no_b = I('phone_no_b','','trim');
            if($phone_no_b) $where['phone_no_b'] = ['like',['%'.$phone_no_b.'%']];

            $secret_no = I('secret_no','','trim');
            if($secret_no) $where['secret_no'] = ['like',['%'.$secret_no.'%']];

            $sort_time = I('sort_time','desc','trim');
            if($sort_time) $sort = 'add_time '.$sort_time;
            if(!$sort) $sort = 'add_time desc';

            $res = AliyunPhoneService::getBindList($where,$sort,$page,$limit);
            $this->ajaxReturn($res);
        } else {
            $this->display();
        }
    }

    /**
     * 获取绑定详情
     */
    public function bindDetails(){
        if(IS_AJAX) {

        } else {
            $this->display();
        }
    }

    /**
     * 调用接口BindAxb添加AXB号码的绑定关系。
     * （绑定AXB号码前，请先明确您的业务场景中AXB三元组的A角色和B角色。例如，在打车应用场景中，A可以是乘客角色，B是司机角色；房产类业务场景中，A可能是用户，B是房产中介。）
     */
    public function sendBindAxb()
    {
        $PhoneNoA = I('PhoneNoA', '', 'trim');
        $PhoneNoB = I('PhoneNoB', '', 'trim');
        $Expiration = I('Expiration', '', 'trim');

        $AliyunPhoneService = new AliyunPhoneService();
        $res = $AliyunPhoneService->sendBindAxb(
            $PhoneNoA, $PhoneNoB, $Expiration
        );
        $this->ajaxReturn($res);
    }

    /**
     * 调用接口UnbindSubscription解除号码的绑定关系。
     */
    public function unbindSubscription()
    {
        $SubsId = I('SubsId', '', 'trim');
        $SecretNo = I('SecretNo', '', 'trim');

        $AliyunPhoneService = new AliyunPhoneService();
        $res = $AliyunPhoneService->unbindSubscription($SubsId, $SecretNo);
        $this->ajaxReturn($res);
    }

    /**
     * 获取虚拟号码
     */
    public function getBindAxb()
    {
        $PhoneNoA = I('PhoneNoA', '', 'trim');
        $PhoneNoB = I('PhoneNoB', '', 'trim');

        $AliyunPhoneService = new AliyunPhoneService();
        $res = $AliyunPhoneService->getBindAxb($PhoneNoA, $PhoneNoB);
        $this->ajaxReturn($res);
    }

}