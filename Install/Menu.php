<?php

return array(
    array(
        //父菜单ID，NULL或者不写系统默认，0为顶级菜单
        "parentid" => 0,
        //地址，[模块/]控制器/方法
        "route" => "Aliyun/%/%",
        //类型，1：权限认证+菜单，0：只作为菜单
        "type" => 1,
        //状态，1是显示，0不显示（需要参数的，建议不显示，例如编辑,删除等操作）
        "status" => 1,
        //名称
        "name" => "阿里云服务",
        //备注
        "remark" => "阿里云服务",
        //子菜单列表
        "child" => [
            [
                "route"  => "Aliyun/Aliyunconfig/confing",
                "type"   => 1,
                "status" => 1,
                "name"   => "基础配置",
                "child"  => []
            ],
            [
                "route"  => "Aliyun/Virtualphone/%",
                "type"   => 1,
                "status" => 1,
                "name"   => "号码保护模块",
                "child"  => [
                    [
                        "route"  => "Aliyun/Virtualphone/confing",
                        "type"   => 1,
                        "status" => 1,
                        "name"   => "基础设置",
                    ],
                    [
                        "route"  => "Aliyun/Virtualphone/bindList",
                        "type"   => 1,
                        "status" => 1,
                        "name"   => "绑定管理",
                    ],
                ]
            ]
        ],
    ),
);
