<?php

namespace Aliyun\CronScript;

use Aliyun\Model\AliyunConfigModel;
use Aliyun\Service\AliyunPhoneService;
use Cron\Base\Cron;

/**
 * 虚拟号码保护定时任务
 * Class VirtualphoneCron
 * @package Aliyun\CronScript
 */
class VirtualphoneCron extends Cron {

    /**
     * 执行任务回调
     *
     * @param string $cronId
     */
    public function run($cronId) {
        $AliyunConfigModel = new AliyunConfigModel();
        $aliyun = $AliyunConfigModel->select();
        foreach ($aliyun as $k => $v){
            try {
                $AliyunPhoneService = new AliyunPhoneService($v['id']);

                //定时解绑号码
                $AliyunPhoneService->cronUnbindSubscription();

            } catch (\Exception $exception) {
                \Think\Log::record("执行计划任务事例 VirtualphoneCron.class.php，发生错误：".$exception->getMessage());
                continue;
            }
        }

    }
}