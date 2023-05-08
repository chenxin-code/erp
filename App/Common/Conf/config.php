<?php
return array_merge([
    'DB_TYPE' => 'sqlsrv', // 数据库类型

    'URL_PATHINFO_DEPR' => '/',//配置URL生成的分隔符，对SEO更好
    'URL_HTML_SUFFIX' => '',//关闭URL伪静态功能   开发阶段最好使用此行代码  上线后可将此行代码注释掉
    'URL_CASE_INSENSITIVE' => true,//实现URL访问不区分大小写
    'URL_MODEL' => 2,//实现U()函数能自动去掉index.php
    'DEFAULT_FILTER' => 'trim,htmlspecialchars',//默认参数过滤方法 用于I()函数

    //规范起见，默认的数据库驱动类设置了字段名强制转换为小写
    //如果你的数据表字段名采用大小写混合方式的话，需要在配置文件中增加如下配置
    'DB_PARAMS' => [\PDO::ATTR_CASE => \PDO::CASE_NATURAL],

    'DEFAULT_MODULE'        =>  'Wap0',  // 默认模块

    'MODULE_DENY_LIST'      =>  ['Runtime'],

    'SESSION_OPTIONS'      =>  ['expire' => 86400 * 365], // session 过期时间

    'TMPL_LAYOUT_ITEM' => '{__LAYOUT_CONTENT__}',//layout模板布局的内容替换标识【原来TP默认为{__CONTENT__}】

    //'TMPL_EXCEPTION_FILE'   =>  APP_PATH.'Common/View/404.html',// 异常页面的模板文件
    //'ERROR_PAGE'            =>  APP_PATH.'Common/View/404.html', // 错误定向页面

    'md5_salt' => 'leaper666',//md5加密的盐值
    //'crypt_key' => '',//加密解密函数的密钥

    'succ_ret' => '1',//(string)666,
    'fail_ret' => '0',//(string)rand(667,999),

    'TMPL_PARSE_STRING' => ['__RES__' => __ROOT__.'/res'],

    //客订单号后缀长度【不允许中途改变，取值建议2或3或4】
    'CusPoNoSuffixLength' => 3,

    //产品无图时的默认显示图  防止程序bug
    'ProductNoPicDefaultPic'  =>  'zwtp.png',

    //账号,客户编号正则校验
    'UserNamePattern' => '^[A-Za-z0-9]{2,10}$',//大写字母,小写字母,数字,长度2-10位
    'CusIdPattern' => '^[A-Z0-9]{2,6}$',//大写字母,数字,长度2-6位

    //压线信息正则校验
    //整数或.5的小数： /^\d+([.]{1}[5]{1}){0,1}$/
    'ScoreInfoPatternPHP' => '^\d+([.]{1}[5]{1}){0,1}(\+\d+([.]{1}[5]{1}){0,1})+$',//php
    'ScoreInfoPatternJS' => '^\\\d+([.]{1}[5]{1}){0,1}(\\\+\\\d+([.]{1}[5]{1}){0,1})+$',//js

    //xxxxxxxx日期格式正则校验
    'xxxxxxxxDatePattern' => '@^(?:(?:(?:(?:(?:1[6-9]|[2-9]\d)(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:0229))|(?:(?:(?:1[6-9]|[2-9]\d)\d{2})(?:(?:(?:0[13578]|1[02])31)|(?:(?:0[13-9]|1[0-2])(?:29|30))|(?:(?:0[1-9])|(?:1[0-2]))(?:0[1-9]|1\d|2[0-8]))))$@',
    //xxxx-xx-xx日期格式正则校验
    'xxxx-xx-xxDatePattern' => '@^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$@',

    //每日订单里的订单状态
    'OrderStatus' => ['未排产','排产中','生产中','生产完成','装车中','已送货','已签收'],
    //每日订单里的特殊订单状态
    'OrderSPStatus' => ['已送货','已签收'],

    //最新阿里图标cdn
    'ali_iconfont_cdn' => '//at.alicdn.com/t/font_473504_0b29ohydgtyq.css',

    //联系方式图标
    'ContactIcon' => ['icon-dianhua','icon-weixin','icon-QQ','icon-dizhi1'],

    //微信JSSDK【扫码功能】
    'WXJSSDK_CDN' => 'http://res.wx.qq.com/open/js/jweixin-1.2.0.js',

//    'iView_CDN' => [
//        'CSS' => '//unpkg.com/iview/dist/styles/iview.css',
//        'JS' => '//unpkg.com/iview/dist/iview.min.js',
//    ],

    'Wap0URName' => [
        '纸板下单' => [
            'Build/s',
            'Build/s_api',
            'Build/bcheck_s_api',
            'Build/calcArea_api',
            'Build/c',
            'Build/c_api',
            'Build/bcheck_c_api',
            'Build/getFormula_api',
            'Build/AutoGetTonLenAndULen_api',
            'Weborder/lists',
            'Weborder/lists_api',
            'Weborder/used_api',
            'Weborder/delete_api',
            'Weborder/detail_api',
            'Weborder/detail2_api',
            'Usedorder/lists',
            'Usedorder/lists_api',
            'Usedorder/cancel_api',
            'Usedboard/lists',
            'Usedboard/lists_api',
            'Usedboard/save_api',
        ],
        '纸箱下单' => [
            'Build/x',
            'Build/x_api',
            'Build/bcheck_x_api',
            'Weborder/lists',
            'Weborder/lists_api',
            'Weborder/used_api',
            'Weborder/delete_api',
            'Weborder/detail_api',
            'Weborder/detail2_api',
            'Usedorder/lists',
            'Usedorder/lists_api',
            'Usedorder/cancel_api',
        ],
        '淘宝箱下单' => [
            'Weborder/lists',
            'Weborder/lists_api',
            //'Weborder/used_api',
            'Weborder/delete_api',
            'Weborder/detail_api',
            'Weborder/detail2_api',
            //'Usedorder/lists',
            //'Usedorder/lists_api',
            //'Usedorder/cancel_api',
        ],
        'ERP订单' => [
            'Order/GetOrders',
            'Order/GetOrders_api',
            'Order/detail_api',
        ],
        '每日订单' => [
            'Order1/GetOrdersP',
            'Order1/GetOrderCount_api',
            'Order1/GetOrdersP_api',
            'Order1/GetCusDateInfo_api',
        ],
        '对账单' => [
            'Order/GetCusFreeMB',
            'Order/GetCusFreeMB_api',
            'Order/detail_api',
        ],
        '报价查询' => [
            'Quo/GetQuoPriceByCus',
            'Quo/GetQuoRuleByCus',
            'Quo/GetQuoPriceByCus_api',
            'Quo/GetQuoRuleByCus_api',
        ],
        '信用余额' => [
            'Cred/WGetCusAmt',
            'Cred/WGetCusAmt_api',
        ],
    ],
    'Wap1URName' => [
        '用户管理' => [
            'User/manage',
            'User/getWebUser0_api',
            'User/getWebUser1_api',
            'User/getQrcode_api',
            'User/getURName_api',
            'User/saveURName_api',
        ],
        'ERP订单' => [
            'Order/GetOrders',
            'Order/GetOrders_api',
            'Order/CusPicker_api',
            'Order/detail_api',
        ],
        '客户每日订单' => [
            'Order1/WGetCusOrder',
            'Order1/GetOrdersP',
            'Order1/WGetCusOrder_api',
            'Order1/WGetUserOrder_api',
            'Order1/GetOrderCount_api',
            'Order1/GetOrdersP_api',
            'Order1/GetCusDateInfo_api',
            'Order1/CusPicker_api',
        ],
        '客户信用余额' => [
            'Cred/WGetCusAmt',
            'Cred/WGetCusAmt_',
            'Cred/WGetCusAmt_api',
            'Cred/WGetCusAmt__api',
            'Cred/CusPicker_api',
        ],
        '原纸采购' => [
            'Paper1/WGetPOMain',
            'Paper1/WGetPOMain_api',
            'Paper1/WGetPODetail_api',
        ],
        '原纸收货' => [
            'Paper1/WGetPOIn',
            'Paper1/WGetPOIn2_api',
            'Paper1/WGetPOInDetail_api',
        ],
        '原纸出库' => [
            'Paper/DoStockOut',
            'Paper/DoStockOut_api',
            'Paper/GetInfoByDoStockOut_api',
        ],
        '原纸入库' => [
            'Paper/DoRStockIn',
            'Paper/DoRStockIn_api',
            'Paper/GetInfoByDoRStockIn_api',
        ],
        '直接入库' => [
            'Paper/DirectInStock',
            'Paper/GetLastSchArea_api',
            'Paper/GetOrdSchArea_api',
            'Paper/GetOrdInInfo_api',
        ],
        '原纸库存' => [
            'Paper/GetSStock',
            'Paper/GetSStock_api',
        ],
        '安全库存' => [
            'Paper/GetSafeStockQ',
            'Paper/GetSafeStockQ_api',
        ],
        '订单统计' => [
            'Statis/GetOrderSum',
            'Statis/GetOrderSum_api',
            'Statis/OnlyShowCharts_GetOrderSum',
            'Statis/GetOrders',
            'Statis/GetOrders_api',
            'Order/detail_api',
        ],
        '退货统计' => [
            'Statis/GetOrdReturnSum',
            'Statis/GetOrdReturnSum_api',
            'Statis/OnlyShowCharts_GetOrdReturnSum',
            'Statis/GetOrders',
            'Statis/GetOrders_api',
            'Order/detail_api',
        ],
        '传单统计' => [
            'Statis/GetSchSum',
            'Statis/GetSchSum_api',
            'Statis/OnlyShowCharts_GetSchSum',
        ],
        '库存统计' => [
            'Statis/GetOrdStock',
            'Statis/GetOrdStock_api',
            'Statis/OnlyShowCharts_GetOrdStock',
        ],
        '生产分析总计' => [
            'Statis/GetProInfo',
            'Statis/GetProInfo_api',
            'Statis/OnlyShowCharts_GetProInfo',
        ],
        '扫描装货' => [
            'Stow/lists',
            'Stow/detail',
            'Stow/lists_api',
            'Stow/GetPDNDetail_api',
            'Stow/GetStockArea_api',
            'Stow/GetOrdPackInfo_api',
        ],
        '库存修改' => [
            'Stock/MStockDetailR',
            'Stock/GetStockDetail_api',
            'Stow/GetOrdPackInfo_api',
        ],
        '库区面积' => [
            'Stock/deliveryArea',
            'Stock/deliveryArea_api',
        ],
        '订单试算' => [
            'Calc/index',
            'Calc/CusPicker_api',
            'Calc/BoardPicker_api',
            'Calc/BoxPicker_api',
            'Calc/AutoGetTrimAndAreaByCus_api',
            'Calc/AutoGetTonLenAndULen_api',
        ],
        '收款调账' => [
            'Frec/RecAdjust',
            'Frec/RecAdjust_api',
            'Frec/CusPicker_api',
        ],
        '客户往来统计' => [
            'Frec/CusContact',
            'Frec/CusContact_api',
            'Frec/CusPicker_api',
        ],
    ],

    //项目默认配置
    'WebConfig' => [
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////  公共  //////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        'FactoryId' => 'LY',//厂商Id  2代不填写/3代填写
        'FactoryLogo' => 'FactoryLogo.jpg',
        'AdverPic' => '',
        'BoardGroupPic' => 'BoardGroupPic.jpg',
        'FlagBoardGroupPic' => 'FlagBoardGroupPic.jpg',
        'BoxGroupPic' => 'BoxGroupPic.jpg',
        'FlagBoxGroupPic' => 'FlagBoxGroupPic.jpg',
        'FactoryName' => '重庆利永纸制品包装有限公司',//厂商名称
        //'FactoryAdver' => '专业瓦楞纸板生产制造与销售',//厂商广告
        'ErpPort' => 38081,//ERP端口【作用于：内部直接入库，内部库存修改，内部订单试算，内部扫描装货】
        'WxAppId' => 'wx8f9065d8c32ab018',//微信公众号AppId【作用于：内部扫码，团购微信支付】
        'Open80Port' => 0,//是否开放了外部80端口  开放1/屏蔽0
        //下面3行配置 + 内部扫码功能，在屏蔽了80端口的情况下才有意义
        'Frp80PortDomain' => 'luodangfrp2.leaper.ltd',//FRP80端口的域名【作用于：内部扫码，团购微信支付】
        'OriDomain' => 'test.leaper.ltd',//原来的域名【作用于：内部扫码，团购微信支付】
        'OriPort' => 801,//原来的端口【作用于：内部扫码，团购微信支付】
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////  内部  //////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        'Wap1Right' => 1,//内部用户权限  开启1/关闭0
        //客户每日订单
        'WGetCusOrderBeginDate' => '-10 day',
        'WGetCusOrderEndDate' => 'now',
        'WGetCusOrderMinDate' => '-1 year',
        'WGetCusOrderMaxDate' => 'now',
        //ERP订单
        'Wap1GetOrdersBeginDate' => '-1 day',
        'Wap1GetOrdersEndDate' => '+7 day',
        'Wap1GetOrdersMinDate' => '-3 year',
        'Wap1GetOrdersMaxDate' => '+1 month',
        //统计下的ERP订单
        'StatisGetOrdersMinDate' => '-3 year',
        'StatisGetOrdersMaxDate' => '+1 month',
        //订单统计
        'GetOrderSumBeginDate' => 'now',
        'GetOrderSumEndDate' => 'now',
        'GetOrderSumMinDate' => '-2 year',
        'GetOrderSumMaxDate' => '+1 month',
        //退货统计
        'GetOrdReturnSumBeginDate' => '-1 month',
        'GetOrdReturnSumEndDate' => 'now',
        'GetOrdReturnSumMinDate' => '-3 year',
        'GetOrdReturnSumMaxDate' => '+1 month',
        //传单统计
        'GetSchSumBeginDate' => 'now',
        'GetSchSumEndDate' => 'now',
        'GetSchSumMinDate' => '-2 year',
        'GetSchSumMaxDate' => '+1 month',
        //库存统计
        'GetOrdStockBeginDate' => '-1 month',
        'GetOrdStockEndDate' => 'now',
        'GetOrdStockMinDate' => '-2 year',
        'GetOrdStockMaxDate' => '+1 month',
        'GetOrdStockiRemainDay' => 0,//在库超期天数
        'GetOrdStockiDiffDDay' => 0,//交货超期天数
        //生产分析总计
        'GetProInfoBeginDate' => 'now',
        'GetProInfoEndDate' => 'now',
        'GetProInfoMinDate' => '-2 year',
        'GetProInfoMaxDate' => '+1 month',
        //原纸采购
        'WGetPOMainBeginDate' => '-3 month',
        'WGetPOMainEndDate' => 'now',
        'WGetPOMainMinDate' => '-3 year',
        'WGetPOMainMaxDate' => 'now',
        //扫码功能  开启1/关闭0
        'UseScan' => 0,
        //原纸出库
        'DoStockOutOpTime' => 'now',//出库日期
        'DoStockOutMinDate' => 'now',
        'DoStockOutMaxDate' => '+3 day',
        //原纸入库
        'DoRStockInOpTime' => 'now',//入库日期
        'DoRStockInMinDate' => 'now',
        'DoRStockInMaxDate' => '+3 day',
        //是否显示库区  是1/否0
        'bMStockArea' => 0,
        //库区控制  开启1/关闭0
        'bSAreaControl' => 0,
        //直接入库
        'DirectInStockDate' => 'now',//入库日期
        'DirectInStockMinDate' => '-1 day',
        'DirectInStockMaxDate' => 'now',
        //扫描装货（列表）
        'StowBeginDate' => '-1 day',
        'StowEndDate' => 'now',
        'StowMinDate' => '-6 month',
        'StowMaxDate' => 'now',
        //扫描装货（详情）
        'bPackAddODefSQ' => 1,//订单号装货默认带出库存数  开启1/关闭0
        //订单试算
        'CalcAutoGetTrimAndAreaByCus' => 1,//自动获取客户是否默认加修边＆加面积  开启1/关闭0
        'CalcAutoGetTonLenAndULen' => 1,//自动获取默认的箱舌＆封箱调整  开启1/关闭0
        'CalcAutoGetdOriPrice' => 1,//自动获取平方报价  开启1/关闭0
        //收款调账
        'RecAdjustBeginDate' => '-1 month',
        'RecAdjustEndDate' => 'now',
        'RecAdjustMinDate' => '-1 year',
        'RecAdjustMaxDate' => 'now',
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////  外部  //////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        'Wap0Right' => 1,//外部用户权限  开启1/关闭0
        //微信订单
        'WeborderBeginDate' => '-1 day',
        'WeborderEndDate' => '+7 day',
        'WeborderMinDate' => '-1 year',
        'WeborderMaxDate' => '+1 month',
        'WeborderDefaultDelRemark' => '信息填写错误,重复下单,不想要了,试一试能否下单',//默认删除原因【用,隔开】
        //ERP订单
        'Wap0GetOrdersBeginDate' => '-1 day',
        'Wap0GetOrdersEndDate' => '+7 day',
        'Wap0GetOrdersMinDate' => '-1 year',
        'Wap0GetOrdersMaxDate' => '+1 month',
        //每日订单
        'GetOrdersPBeginDate' => '-10 day',
        'GetOrdersPEndDate' => 'now',
        'GetOrdersPMinDate' => '-1 year',
        'GetOrdersPMaxDate' => 'now',
        //对账单
        'GetCusFreeMBBeginDate' => '-1 month',
        'GetCusFreeMBEndDate' => 'now',
        'GetCusFreeMBMinDate' => '-1 year',
        'GetCusFreeMBMaxDate' => '+1 month',
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////  下单  //////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        'UseQuoBoard' => 1,//常用材质使用报价价格材质  开启1/关闭0
        'BuildMinLength' => 500,
        'BuildMaxLength' => 5000,
        'BuildMinWidth' => 100,
        'BuildMaxWidth' => 3000,
        'BuildMinBoxL' => 100,
        'BuildMaxBoxL' => 1000,
        'BuildMinBoxW' => 100,
        'BuildMaxBoxW' => 1000,
        'BuildMinBoxH' => 100,
        'BuildMaxBoxH' => 1000,
        'BuildTonLen' => '0,30,35,40,45,50,55',//箱舌【用,隔开】
        'BuildULen' => '0,2,3,4,5,6,7,8',//封箱调整【用,隔开】
        'BuildAutoGetTonLenAndULen' => 1,//自动获取默认的箱舌＆封箱调整  开启1/关闭0
        'BuildScoreName' => '无压线,压线1,压线2,压线3,压线4',//压线名称
        'BuildMinArea' => 50,
        'BuildMaxArea' => 10000,
        'BuildMinOrdQty' => 10,
        'BuildMaxOrdQty' => 5000,
        'BuildDeliveryDate' => '+1 day',//交货日期
        'BuildMinDate' => '+1 day',
        'BuildMaxDate' => '+7 day',
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////  团购  //////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        'UseBoardGroup' => 0,//纸板团购功能  开启1/关闭0
        'BoardDefaultPic' => 'BoardDefaultPic.jpg',//纸板默认图片
        'ShowEdBoard' => 1,//展示团购已结束的纸板  展示1/隐藏0
        'BoardFlag' => '爆款',//纸板特殊标识文字【建议2个汉字】
        'UseBoxGroup' => 0,//淘宝箱团购功能  开启1/关闭0
        'BoxDefaultPic' => 'BoxDefaultPic.jpg',//淘宝箱默认图片
        'ShowEdBox' => 1,//展示团购已结束的淘宝箱  展示1/隐藏0
        'BoxFlag' => '精品',//淘宝箱特殊标识文字【建议2个汉字】
        'UseWxPay' => 0,//微信支付功能  开启1/关闭0
        'UseAliPay' => 0,//支付宝支付功能  开启1/关闭0
        'ValidPayTime' => 86400,//有效支付时间（秒）
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////  微信支付  //////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
        'WxAppSecret' => 'e70064a5bcdff6d7c8f09106dbdb4873',//微信公众号AppSecret
        'WxMchId' => '1496235982',//微信商户号
        'WxKey' => 'abcdefghijklmnopqrstuvwxyz123456',//微信商户支付密钥
        'WxSslcertPath' => '',//微信商户证书 apiclient_cert.pem 绝对路径【建议与 htdocs 文件夹平级】
        'WxSslkeyPath' => '',//微信商户证书 apiclient_key.pem 绝对路径【建议与 htdocs 文件夹平级】
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////  支付宝支付  /////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        'AliAppId' => '2019052365360173',
        //支付宝公钥，账户中心->密钥管理->开放平台密钥，找到添加了支付功能的应用，根据你的加密类型，查看支付宝公钥
        'AliPublicKey' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwDxw3AvvEzoayXSfYM3S3pmQ2eQgwwJT+S3Atf9usBLY/OTYdltFu3RHN9OYuUJKy55GtiwqBhlnT+pcJzuKtr5dQyzm+bZIWxxF396+yhwEUq2yhER38HT/Jay7IkYOW8wOOhAjqgAwavICTDcJyOvSO0zWwHn970EcBrD2Z0ut9VVTsty91IgSsN9s50ksAfoiWqbGEOgNEZ6b1Xg4ldxr4MZLmzrXG06MXY4YJwaNblg89KRFb1XMa/74l+JXEkR351Z7zbD/1Dpq8GdQEes5snhOOF2DlBpVMmeFXsvvFBWt7JSjWjUvpXG8x3RIgIEbIevayjlR4MfYa49tcwIDAQAB',
        //支付宝商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310
        'AliRsaPrivateKey' => 'MIIEogIBAAKCAQEAoDSAEgKPsawyO1zGyzBx+z7PO01VFw1pVZ6cKaorCPZvXC6U J3b+ApW+8vaG/3B35LjxLMYyEbabl816lXnTKN/1GBw0nBIf29j9pmgrxRy4RG+Q yBoKPn+vnAtz+Ph+F9D6/uH0f2RllJnQOK3Ht1lWkPCJbkhRL4uTIOCQ/vdez0CN MCbOBeDARAp7Sn872vzA0BQecNssw9cqxDqTdFsJkPZ6fL1vVzc3gRceukV67Flr jD4JHup11e+u6OBjgLeISdcTGDkgNfgmsIPkXMIg8He/nvLNHp2Mzd25ns1tyPtR NOAZBXmIn1fihGdW8ccqC5j9aDpDY2FVegK9RQIDAQABAoIBAHlVKcX6IKA8vVKO QpUzHLBfco4EVLR/2M4asUUw9yTzl4WmVVjKWzjT9umGYtnlcThOBYKWnOcjeRXw bq30tUNY0wSun/2wEGbuIbU9YiEITlqucGXMqwOIqxSw6+WdNcqN4PrXYNh2ziRM lhHHM+IeDFz7f+qBZNNwjz6wNcCToCFb/i2g9ImXNYjunRn26sRlphYrlEzSfuK5 EETL59Cr7pl7PchdkjToA8YJlY73PvgDqvHu9n6pewneZdOXvfJu5YoKcBjQlan8 ObkDTDDrr3RR9TglY+ZogO5iK0HmGn3u/loxQaJX9SaEizkAGh2GIh61crtbB91o J6ccbGUCgYEA1JIxTZfyUpPI7T6jvWmkiUkqjPrahsE9JwM6aAIo1Jzg5cTEWhgW 5ZBi17s02jgiAfnPyt2SWTAWGnsJos1roZdqbRYQL+hUN62k8mrO50tnRxkh4pwZ 1y4GTvrf93rOeyQmIrPc+N8NubkubDlJd3VpZqRkZ3ZarXNyFPAivQsCgYEAwO97 kmB13ruYlu23dXJLdz6kOtaCzY47nGATrPUZJL+uVsoJGzt9m/m+qXxhXVGoXBLT jW+ebLm7TCC9G4C9NZi2tZTVbRihMOl8RAhof+FSg6V+sZP4aBr+snMW4MLgHlgu sPNG3fgbht1n5ZPGqBGbVP5/PDvQj3EGe1rkwO8CgYBoFonz39J1ow4BNeoBxugd nWkrjW5SC++A2fDQmzYZHhR2DkrDfwY/NNthnba8oD3uz79zK9oMuCIqp1LJmGL6 xmUDs4kLnG0YsnuRhMF5uo+A3gqcpyl0F4JZTDk9OrI+1C2kUoSCjN5wsf/MWKTj zOdmrlrH2sl+I4iaPhgUjwKBgFLM9H2HhE6IuB3d+2QeHyEX8yeFNDG/nvmnigoq ThYlZU69+laDm467CgZ26NpB1z1cnwoOIzGKLEWprz8Eay97UL5xF39wmI0DR19z NFXdf6ob9lmDpZIYM8Kl/eWL/N9I/RDX90v/1OB2WMaAOzuaAs3hQv24tfM5kIyU H6RlAoGAReeEeaxtxpiCT6UvevMJsteb2wcS+blO/lMDQD1+RZ95geO9uwWUqkjx v+zFxUjxaOcXanQ9kW6BznR58VAXhXzofQsfY3i4H0WrwUBJhGq1fCQnwShyt1h6 wXUyk95tr/d6BcLv6AI3AUDN9X6fKGNl0FM8lmLJNEXmKw5G2Qo=',



    ],
],include('db.php'));
//parse_ini_file('db.ini')
