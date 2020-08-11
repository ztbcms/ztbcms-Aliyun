<?php

namespace Aliyun\Controller;

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
     * 调用接口BindAxb添加AXB号码的绑定关系。
     * （绑定AXB号码前，请先明确您的业务场景中AXB三元组的A角色和B角色。例如，在打车应用场景中，A可以是乘客角色，B是司机角色；房产类业务场景中，A可能是用户，B是房产中介。）
     */
    public function sendBindAxb()
    {
        $PhoneNoA = I('PhoneNoA', '', 'trim');
        $PhoneNoB = I('PhoneNoB', '', 'trim');
        $Expiration = I('Expiration','','trim');

        $PhoneNoA = '13168329238';
        $PhoneNoB = '15218981224';

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
        $SubsId = I('SubsId','','trim');
        $SecretNo = I('SecretNo','','trim');

        $AliyunPhoneService = new AliyunPhoneService();
        $res = $AliyunPhoneService->unbindSubscription($SubsId,$SecretNo);
        $this->ajaxReturn($res);
    }

}