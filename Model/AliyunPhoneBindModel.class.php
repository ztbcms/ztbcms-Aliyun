<?php

namespace Aliyun\Model;

use Common\Model\RelationModel;

/**
 * 阿里云号码保护绑定表
 * Class AliyunPhoneBindModel
 * @package Aliyun\Model
 */
class AliyunPhoneBindModel extends RelationModel
{

    protected $tableName = 'aliyun_phone_bind';

    const YES_BIND = 1;  //已绑定
    const FAIL_BIND = 2; //绑定失败
    const UN_BIND = 3; //已解绑

}