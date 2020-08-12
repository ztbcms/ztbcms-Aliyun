<?php

namespace Aliyun\Controller;

use Aliyun\Model\AliyunPhoneBindModel;
use Aliyun\Service\AliyunPhoneService;
use Common\Controller\Base;

/**
 * 回调地址
 * Class NotifyController
 * @package Aliyun\Controller
 */
class NotifyController extends Base
{

    /**
     * 呼叫结束后话单报告回调
     */
    public function virtualPhoneSecretReport()
    {
        $content = file_get_contents("php://input");
        file_put_contents(APP_PATH.'../../statics/log1.json',$content);
        $content = json_decode($content, true);
        file_put_contents(APP_PATH.'../../statics/log2.json',$content);

        $AliyunPhoneService = new AliyunPhoneService();
        $AliyunPhoneBindModel = new AliyunPhoneBindModel();
        foreach ($content as $k => $v){
            $AliyunPhoneBindFind = $AliyunPhoneBindModel->where([
                'phone_no_a|phone_no_b' => $v['phone_no'],
                'bind_status' => '1',
                'secret_no' => $v['secret_no']
            ])->find();

            if($AliyunPhoneBindFind) {
                //当存在绑定关系的时候进行解绑操作
                $AliyunPhoneService->unbindSubscription($AliyunPhoneBindFind['subs_id'],$AliyunPhoneBindFind['secret_no']);
            }
        }

        $res['code'] = 0;
        $res['msg'] = '成功';
        $this->ajaxReturn($res);
    }
}