<?php
return [
    'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'menu', // 默认操作名称

    'LAYOUT_ON'             =>  true, // 是否启用布局
    'LAYOUT_NAME'           =>  'Wap1/View/layout', // 当前布局名称

    'TMPL_ACTION_ERROR' => './res/error.html',

    //万能验证码
    'UnivCode' => '5201487931',

    //必须在80端口下才能访问的url
    //扫码页面及其接口
    'Must80PortUrl' => [
        'Paper/DoStockOut',
        'Paper/DoStockOut_api',
        'Paper/GetInfoByDoStockOut_api',
        'Paper/DoRStockIn',
        'Paper/DoRStockIn_api',
        'Paper/GetInfoByDoRStockIn_api',
        'Paper/DirectInStock',
        'Paper/GetLastSchArea_api',
        'Paper/GetOrdSchArea_api',
        'Paper/GetOrdInInfo_api',
        'Stow/detail',
        'Stow/GetPDNDetail_api',
        'Stow/GetStockArea_api',
        'Stow/GetOrdPackInfo_api',
        'Stock/MStockDetailR',
        'Stock/GetStockDetail_api',
        'Stow/GetOrdPackInfo_api',
        'Index/logout_api',//解决只能退出1个的bug
    ],

    // FactoryId 为空字符串时不能访问的url页面
    'MustFactoryIdUrl' => [
        'Paper/DirectInStock',
        'Stow/lists',
        'Stow/detail',
        'Stock/MStockDetailR',
        'Stock/deliveryArea',
        'Frec/RecAdjust',
        'Frec/CusContact',
    ],

];
