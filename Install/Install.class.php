<?php

namespace Aliyun\Install;

use Aliyun\Model\AliyunConfigModel;
use Aliyun\Model\AliyunPhoneConfigModel;
use Libs\System\InstallBase;

class Install extends InstallBase {

	//模块地址
	private $path = NULL;

	public function __construct() {
		$this->path = APP_PATH . 'Aliyun/';
	}

	//安装前进行处理
	public function run() {
		return true;
	}

	//基本安装结束后的回调
	public function end() {
	    $AliyunConfigModel = new AliyunConfigModel();
	    $AliyunPhoneConfigModel = new AliyunPhoneConfigModel();
        $isCount = $AliyunConfigModel->count();
        if(!$isCount) {
            //阿里云信息配置表
            $aliyun_id = $AliyunConfigModel->add([
                'add_time' => time(),
                'edit_time' => time()
            ]);

            //阿里云虚拟号码配置表
            $AliyunPhoneConfigModel->add([
                'aliyun_id' => $aliyun_id,
                'expiration' => '300',
                'add_time' => time(),
                'edit_time' => time()
            ]);
        }
		return true;
	}
}
