<?php
return [
	'DEFAULT_CONTROLLER'    =>  'Index1', // 默认控制器名称
	'DEFAULT_ACTION'        =>  'index', // 默认操作名称

    'LAYOUT_ON'             =>  true, // 是否启用布局
    'LAYOUT_NAME'           =>  'Wap0/View/layout', // 当前布局名称

    'TMPL_ACTION_ERROR' => './res/error.html',

    //万能验证码
    'UnivCode' => '7504436989',

    //必须在80端口下才能访问的url
    'Must80PortUrl' => [
        'Index/logout_api',//解决只能退出1个的bug
    ],

];
