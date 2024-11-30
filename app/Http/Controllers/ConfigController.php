<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function getConfig(Request $request)
    {
        //修改配置可能需要浏览器强制刷新以清除缓存
        $config = [
            "isCaptchaOn" => true,
            "dictionary" => [
                "sysLoginLog.status" => [
                    [
                        "label" => "登录成功",
                        "value" => 1,
                        "cssTag" => "success"
                    ],
                    [
                        "label" => "退出成功",
                        "value" => 2,
                        "cssTag" => "info"
                    ],
                    [
                        "label" => "注册",
                        "value" => 3,
                        "cssTag" => ""
                    ],
                    [
                        "label" => "登录失败",
                        "value" => 0,
                        "cssTag" => "danger"
                    ]
                ],
                "common.yesOrNo" => [
                    [
                        "label" => "是",
                        "value" => 1,
                        "cssTag" => ""
                    ],
                    [
                        "label" => "否",
                        "value" => 0,
                        "cssTag" => "danger"
                    ]
                ],
                "common.status" => [
                    [
                        "label" => "启用",
                        "value" => "ENABLED",
                        "cssTag" => ""
                    ],
                    [
                        "label" => "禁用",
                        "value" => "DISABLED",
                        "cssTag" => "danger"
                    ]
                ],
                "sysUser.gender" => [
                    [
                        "label" => "男",
                        "value" => "MALE",
                        "cssTag" => ""
                    ],
                    [
                        "label" => "女",
                        "value" => "FEMALE",
                        "cssTag" => ""
                    ],
                    [
                        "label" => "未知",
                        "value" => "UNKNOWN",
                        "cssTag" => ""
                    ]
                ],
                "sysOperationLog.businessType" => [
                    [
                        "label" => "其他操作",
                        "value" => 0,
                        "cssTag" => "info"
                    ],
                    [
                        "label" => "添加",
                        "value" => 1,
                        "cssTag" => ""
                    ],
                    [
                        "label" => "修改",
                        "value" => 2,
                        "cssTag" => ""
                    ],
                    [
                        "label" => "删除",
                        "value" => 3,
                        "cssTag" => "danger"
                    ],
                    [
                        "label" => "授权",
                        "value" => 4,
                        "cssTag" => ""
                    ],
                    [
                        "label" => "导出",
                        "value" => 5,
                        "cssTag" => "warning"
                    ],
                    [
                        "label" => "导入",
                        "value" => 6,
                        "cssTag" => "warning"
                    ],
                    [
                        "label" => "强退",
                        "value" => 7,
                        "cssTag" => "danger"
                    ],
                    [
                        "label" => "清空",
                        "value" => 8,
                        "cssTag" => "danger"
                    ]
                ],
                "sysOperationLog.status" => [
                    [
                        "label" => "成功",
                        "value" => 1,
                        "cssTag" => ""
                    ],
                    [
                        "label" => "失败",
                        "value" => 0,
                        "cssTag" => "danger"
                    ]
                ],
                "sysMenu.isVisible" => [
                    [
                        "label" => "显示",
                        "value" => 1,
                        "cssTag" => ""
                    ],
                    [
                        "label" => "隐藏",
                        "value" => 0,
                        "cssTag" => "danger"
                    ]
                ],
                "sysUser.status" => [
                    [
                        "label" => "正常",
                        "value" => 1,
                        "cssTag" => null
                    ],
                    [
                        "label" => "禁用",
                        "value" => 2,
                        "cssTag" => null
                    ],
                    [
                        "label" => "冻结",
                        "value" => 3,
                        "cssTag" => null
                    ]
                ],
                "sysNotice.noticeType" => [
                    [
                        "label" => "通知",
                        "value" => 1,
                        "cssTag" => "warning"
                    ],
                    [
                        "label" => "公告",
                        "value" => 2,
                        "cssTag" => "success"
                    ]
                ],
                "sysNotice.status" => [
                    [
                        "label" => "正常",
                        "value" => 1,
                        "cssTag" => ""
                    ],
                    [
                        "label" => "关闭",
                        "value" => 0,
                        "cssTag" => "danger"
                    ]
                ],
            ]
        ];

        return $this->jsonOk($config);
    }
}
