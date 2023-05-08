<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link href="/erp/res/<?php echo ($config['FactoryLogo']); ?>" rel="shortcut icon">
    <title><?php echo ($LayoutTitle); ?></title>
    <script src="/erp/res/vue.js"></script>
    <!-- jQuery -->
    <script src="/erp/res/jquery.min.js"></script>
    <!-- SweetAlert2 插件 -->
    <link rel="stylesheet" href="/erp/res/SweetAlert2/sweetalert2.min.css">
    <script src="/erp/res/SweetAlert2/sweetalert2.min.js"></script>
    <!-- ElementUI -->
    <link rel="stylesheet" href="/erp/res/ElementUI/index.css">
    <script src="/erp/res/ElementUI/index.js"></script>
    <!-- 阿里图标cdn -->
    <link rel="stylesheet" href="<?php echo C('ali_iconfont_cdn');?>">
    <!-- 函数库 -->
    <script src="/erp/res/function.js?time=<?php echo time();?>"></script>
    <!-- 后台样式 -->
    <link rel="stylesheet" href="/erp/res/admin.css?time=<?php echo time();?>">
</head>
<body class="admin-common">
    <div class="sidebar">
        <div style="margin: 0 0 0 9px;">
            <h2 style="padding: 40px 0 10px 0;color: #222;font-size: 26px;font-weight: bold;text-align: center;">
                <a>后台管理</a>
            </h2>
            <div class="top-container">
                欢迎您，
                <a><?php echo session('ERP_Admin_User.UserName');?></a>
                <br>
                <a style="cursor: pointer;" @click="clean()">清除缓存</a> |
                <a href="<?php echo U('Index/logout');?>">退出</a>
            </div>
            <script>
                new Vue({
                    el: '.top-container',
                    methods: {
                        clean: function () {
                            $.ajax({
                                url: '<?php echo U('Index/clean_api');?>',
                                success: function (respon) {
                                    var respon = eval('(' + respon + ')');
                                    if(respon.ret === '<?php echo C('succ_ret');?>'){
                                        swal({type: 'success',title: respon.msg});
                                    }else{
                                        swal({type: 'error',title: respon.msg});
                                    }
                                }
                            });
                        }
                    }
                });
            </script>
            <ul class="menu-container">
                <li v-for="v1 in menu1">
                    <a :href="v1.href1" class="nav-top-item no-submenu" :class="{'current':v1.href1 === CurHref}" v-if="v1.href1">
                        {{v1.title1}}
                    </a>
                    <a class="nav-top-item" :class="{'current':JSON.stringify(v1.menu2).indexOf(CurHref) !== -1}" v-else>
                        {{v1.title1}}
                    </a>
                    <ul v-if="v1.menu2">
                        <li v-for="v2 in v1.menu2">
                            <a :href="v2.href2" :class="{'current':v2.href2 === CurHref}">
                                {{v2.title2}}
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <script>
                new Vue({
                    el: '.menu-container',
                    data: {
                        menu1: [
                            {
                                title1: '项目配置',
                                href1: '<?php echo U('Index/config');?>'
                            },
                            {
                                title1: '图片配置',
                                href1: '<?php echo U('Index/imgConfig');?>'
                            },
                            {
                                title1: '联系方式管理',
                                menu2: [
                                    {
                                        title2: '联系方式列表',
                                        href2: '<?php echo U('Contact/lists');?>'
                                    },
                                    {
                                        title2: '添加联系方式',
                                        href2: '<?php echo U('Contact/add');?>'
                                    }
                                ]
                            },
                            {
                                title1: '后台用户管理',
                                menu2: [
                                    {
                                        title2: '用户列表',
                                        href2: '<?php echo U('User/lists');?>'
                                    },
                                    {
                                        title2: '添加用户',
                                        href2: '<?php echo U('User/add');?>'
                                    }
                                ]
                            }
                        ].concat(Number('<?php echo ($config['UseBoardGroup']); ?>')?[
                            {
                                title1: '团购纸板管理',
                                menu2: [
                                    {
                                        title2: '纸板列表',
                                        href2: '<?php echo U('Board/lists');?>'
                                    },
                                    {
                                        title2: '添加纸板',
                                        href2: '<?php echo U('Board/add');?>'
                                    },
                                    {
                                        title2: '纸板默认图片',
                                        href2: '<?php echo U('Board/DefaultPic');?>'
                                    },
                                    {
                                        title2: '已删除纸板列表',
                                        href2: '<?php echo U('Board/delLists');?>'
                                    }
                                ]
                            }
                        ]:[]).concat(Number('<?php echo ($config['UseBoxGroup']); ?>')?[
                            {
                                title1: '团购淘宝箱管理',
                                menu2: [
                                    {
                                        title2: '淘宝箱列表',
                                        href2: '<?php echo U('Box/lists');?>'
                                    },
                                    {
                                        title2: '添加淘宝箱',
                                        href2: '<?php echo U('Box/add');?>'
                                    },
                                    {
                                        title2: '淘宝箱默认图片',
                                        href2: '<?php echo U('Box/DefaultPic');?>'
                                    },
                                    {
                                        title2: '已删除淘宝箱列表',
                                        href2: '<?php echo U('Box/delLists');?>'
                                    }
                                ]
                            }
                        ]:[]),
                        CurHref: '<?php echo U();?>'
                    },
                    mounted: function () {
                        $('.menu-container li ul').hide();
                        $('.menu-container li a.current').parent().find('ul').slideToggle('slow');
                        $('.menu-container li a.nav-top-item').click(
                            function () {
                                $(this).parent().siblings().find('ul').slideUp('normal');
                                $(this).next().slideToggle('normal');
                                return false;
                            }
                        );
                        $('.menu-container li a.no-submenu').click(
                            function () {
                                window.location.href = this.href;
                                return false;
                            }
                        );
                        $('.menu-container li .nav-top-item').hover(
                            function () {
                                $(this).stop().animate({paddingRight: '25px'},200);
                            },
                            function () {
                                $(this).stop().animate({paddingRight: '15px'});
                            }
                        );
                    }
                });
            </script>
        </div>
    </div>
    <div class="main-content">
        <style>
    .el-tabs__item.is-active {
        color: #1a991d;
    }
    .el-tabs__item:hover {
        color: #1a991d;
    }
    .el-tabs__active-bar {
        background-color: #1a991d;
    }
</style>

<div id="VueBox">
    <el-tabs v-model="module" v-if="config">
        <el-tab-pane label="公共" name="公共">
            <form>
                <table>
                    <tbody>
                        <tr>
                            <td>厂商Id</td>
                            <td>
                                <input style="width: 100px;" v-model="config.FactoryId">
                                <p>2代不填写/3代填写</p>
                            </td>
                        </tr>
                        <tr>
                            <td>厂商名称</td>
                            <td>
                                <input style="width: 300px;" v-model="config.FactoryName">
                            </td>
                        </tr>
                        <!--<tr>
                            <td>厂商广告</td>
                            <td>
                                <input style="width: 300px;" v-model="config.FactoryAdver">
                            </td>
                        </tr>-->
                        <tr>
                            <td>ERP端口</td>
                            <td>
                                <input type="number" style="width: 100px;" v-model="config.ErpPort">
                                <p>作用于：内部直接入库，内部库存修改，内部订单试算，内部扫描装货</p>
                            </td>
                        </tr>
                        <tr>
                            <td>是否开放了外部80端口</td>
                            <td>
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.Open80Port">
                            </td>
                        </tr>
                        <tr v-if="Number(config.UseScan) || Number(config.UseWxPay)">
                            <td>微信公众号AppId</td>
                            <td>
                                <input style="width: 200px;" v-model="config.WxAppId">
                                <p>作用于：内部扫码，团购微信支付</p>
                            </td>
                        </tr>
                        <tr v-if="!Number(config.Open80Port) && (Number(config.UseScan) || Number(config.UseWxPay))">
                            <td>FRP80端口的域名</td>
                            <td>
                                <input style="width: 200px;" v-model="config.Frp80PortDomain">
                                <p>作用于：内部扫码，团购微信支付</p>
                            </td>
                        </tr>
                        <tr v-if="!Number(config.Open80Port) && (Number(config.UseScan) || Number(config.UseWxPay))">
                            <td>原来的域名</td>
                            <td>
                                <input style="width: 200px;" v-model="config.OriDomain">
                                <p>作用于：内部扫码，团购微信支付</p>
                            </td>
                        </tr>
                        <tr v-if="!Number(config.Open80Port) && (Number(config.UseScan) || Number(config.UseWxPay))">
                            <td>原来的端口</td>
                            <td>
                                <input type="number" style="width: 100px;" v-model="config.OriPort">
                                <p>作用于：内部扫码，团购微信支付</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </el-tab-pane>
        <el-tab-pane label="内部" name="内部">
            <form>
                <table>
                    <tbody>
                        <tr>
                            <td>用户权限</td>
                            <td>
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.Wap1Right">
                            </td>
                        </tr>
                        <tr>
                            <td>客户每日订单</td>
                            <td>
                                默认日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.WGetCusOrderBeginDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.WGetCusOrderEndDate">
                                <br><br>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.WGetCusOrderMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.WGetCusOrderMaxDate">
                            </td>
                        </tr>
                        <tr>
                            <td>ERP订单</td>
                            <td>
                                默认日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.Wap1GetOrdersBeginDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.Wap1GetOrdersEndDate">
                                <br><br>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.Wap1GetOrdersMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.Wap1GetOrdersMaxDate">
                            </td>
                        </tr>
                        <tr>
                            <td>统计下的ERP订单</td>
                            <td>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.StatisGetOrdersMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.StatisGetOrdersMaxDate">
                            </td>
                        </tr>
                        <tr>
                            <td>订单统计</td>
                            <td>
                                默认日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrderSumBeginDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrderSumEndDate">
                                <br><br>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrderSumMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrderSumMaxDate">
                            </td>
                        </tr>
                        <tr>
                            <td>退货统计</td>
                            <td>
                                默认日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrdReturnSumBeginDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrdReturnSumEndDate">
                                <br><br>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrdReturnSumMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrdReturnSumMaxDate">
                            </td>
                        </tr>
                        <tr>
                            <td>传单统计</td>
                            <td>
                                默认日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.GetSchSumBeginDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.GetSchSumEndDate">
                                <br><br>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.GetSchSumMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.GetSchSumMaxDate">
                            </td>
                        </tr>
                        <tr>
                            <td>库存统计</td>
                            <td>
                                默认日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrdStockBeginDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrdStockEndDate">
                                <br><br>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrdStockMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrdStockMaxDate">
                                <br><br>
                                在库超期天数&nbsp;&nbsp;&nbsp;
                                <input type="number" style="width: 100px;" v-model="config.GetOrdStockiRemainDay">
                                <br><br>
                                交货超期天数&nbsp;&nbsp;&nbsp;
                                <input type="number" style="width: 100px;" v-model="config.GetOrdStockiDiffDDay">
                            </td>
                        </tr>
                        <tr>
                            <td>生产分析总计</td>
                            <td>
                                默认日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.GetProInfoBeginDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.GetProInfoEndDate">
                                <br><br>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.GetProInfoMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.GetProInfoMaxDate">
                            </td>
                        </tr>
                        <tr>
                            <td>原纸采购</td>
                            <td>
                                默认日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.WGetPOMainBeginDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.WGetPOMainEndDate">
                                <br><br>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.WGetPOMainMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.WGetPOMainMaxDate">
                            </td>
                        </tr>
                        <tr v-if="!Number(config.Open80Port)">
                            <td>扫码功能</td>
                            <td>
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.UseScan">
                            </td>
                        </tr>
                        <tr>
                            <td>原纸出库</td>
                            <td>
                                默认出库日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.DoStockOutOpTime">
                                <br><br>
                                出库日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.DoStockOutMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.DoStockOutMaxDate">
                            </td>
                        </tr>
                        <tr>
                            <td>原纸入库</td>
                            <td>
                                默认入库日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.DoRStockInOpTime">
                                <br><br>
                                入库日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.DoRStockInMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.DoRStockInMaxDate">
                            </td>
                        </tr>
                        <tr>
                            <td>是否显示库区</td>
                            <td>
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.bMStockArea">
                            </td>
                        </tr>
                        <tr>
                            <td>库区控制</td>
                            <td>
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.bSAreaControl">
                            </td>
                        </tr>
                        <tr>
                            <td>直接入库</td>
                            <td>
                                默认入库日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.DirectInStockDate">
                                <br><br>
                                入库日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.DirectInStockMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.DirectInStockMaxDate">
                            </td>
                        </tr>
                        <tr>
                            <td>扫描装货（列表）</td>
                            <td>
                                默认日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.StowBeginDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.StowEndDate">
                                <br><br>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.StowMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.StowMaxDate">
                            </td>
                        </tr>
                        <tr>
                            <td>扫描装货（详情）</td>
                            <td>
                                订单号装货默认带出库存数&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.bPackAddODefSQ">
                            </td>
                        </tr>
                        <tr>
                            <td>订单试算</td>
                            <td>
                                自动获取客户是否默认加修边＆加面积&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.CalcAutoGetTrimAndAreaByCus">
                                <br><br>
                                自动获取默认的箱舌＆封箱调整&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.CalcAutoGetTonLenAndULen">
                                <br><br>
                                自动获取平方报价&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.CalcAutoGetdOriPrice">
                            </td>
                        </tr>
                        <tr>
                            <td>收款调账</td>
                            <td>
                                默认日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.RecAdjustBeginDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.RecAdjustEndDate">
                                <br><br>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.RecAdjustMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.RecAdjustMaxDate">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </el-tab-pane>
        <el-tab-pane label="外部" name="外部">
            <form>
                <table>
                    <tbody>
                        <tr>
                            <td>用户权限</td>
                            <td>
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.Wap0Right">
                            </td>
                        </tr>
                        <tr>
                            <td>微信订单</td>
                            <td>
                                默认日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.WeborderBeginDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.WeborderEndDate">
                                <br><br>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.WeborderMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.WeborderMaxDate">
                                <br><br>
                                默认删除原因&nbsp;&nbsp;&nbsp;
                                <input style="width: 300px;" v-model="config.WeborderDefaultDelRemark">
                                <p>用,隔开</p>
                            </td>
                        </tr>
                        <tr>
                            <td>ERP订单</td>
                            <td>
                                默认日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.Wap0GetOrdersBeginDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.Wap0GetOrdersEndDate">
                                <br><br>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.Wap0GetOrdersMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.Wap0GetOrdersMaxDate">
                            </td>
                        </tr>
                        <tr>
                            <td>每日订单</td>
                            <td>
                                默认日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrdersPBeginDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrdersPEndDate">
                                <br><br>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrdersPMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.GetOrdersPMaxDate">
                            </td>
                        </tr>
                        <tr>
                            <td>对账单</td>
                            <td>
                                默认日期&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.GetCusFreeMBBeginDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.GetCusFreeMBEndDate">
                                <br><br>
                                日期范围&nbsp;&nbsp;&nbsp;
                                <input style="width: 100px;" v-model="config.GetCusFreeMBMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.GetCusFreeMBMaxDate">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </el-tab-pane>
        <el-tab-pane label="下单" name="下单">
            <form>
                <table>
                    <tbody>
                        <tr>
                            <td>常用材质使用报价价格材质</td>
                            <td>
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.UseQuoBoard">
                            </td>
                        </tr>
                        <tr>
                            <td>板长范围</td>
                            <td>
                                <input type="number" style="width: 100px;" v-model="config.BuildMinLength">&nbsp;&nbsp;mm
                                &nbsp;~&nbsp;
                                <input type="number" style="width: 100px;" v-model="config.BuildMaxLength">&nbsp;&nbsp;mm
                            </td>
                        </tr>
                        <tr>
                            <td>板宽范围</td>
                            <td>
                                <input type="number" style="width: 100px;" v-model="config.BuildMinWidth">&nbsp;&nbsp;mm
                                &nbsp;~&nbsp;
                                <input type="number" style="width: 100px;" v-model="config.BuildMaxWidth">&nbsp;&nbsp;mm
                            </td>
                        </tr>
                        <tr>
                            <td>箱长范围</td>
                            <td>
                                <input type="number" style="width: 100px;" v-model="config.BuildMinBoxL">&nbsp;&nbsp;mm
                                &nbsp;~&nbsp;
                                <input type="number" style="width: 100px;" v-model="config.BuildMaxBoxL">&nbsp;&nbsp;mm
                            </td>
                        </tr>
                        <tr>
                            <td>箱宽范围</td>
                            <td>
                                <input type="number" style="width: 100px;" v-model="config.BuildMinBoxW">&nbsp;&nbsp;mm
                                &nbsp;~&nbsp;
                                <input type="number" style="width: 100px;" v-model="config.BuildMaxBoxW">&nbsp;&nbsp;mm
                            </td>
                        </tr>
                        <tr>
                            <td>箱高范围</td>
                            <td>
                                <input type="number" style="width: 100px;" v-model="config.BuildMinBoxH">&nbsp;&nbsp;mm
                                &nbsp;~&nbsp;
                                <input type="number" style="width: 100px;" v-model="config.BuildMaxBoxH">&nbsp;&nbsp;mm
                            </td>
                        </tr>
                        <tr>
                            <td>箱舌(mm)</td>
                            <td>
                                <input style="width: 300px;" v-model="config.BuildTonLen">
                                <p>用,隔开</p>
                            </td>
                        </tr>
                        <tr>
                            <td>封箱调整(mm)</td>
                            <td>
                                <input style="width: 300px;" v-model="config.BuildULen">
                                <p>用,隔开</p>
                            </td>
                        </tr>
                        <tr>
                            <td>自动获取默认的箱舌＆封箱调整</td>
                            <td>
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.BuildAutoGetTonLenAndULen">
                            </td>
                        </tr>
                        <tr>
                            <td>压线名称</td>
                            <td>
                                <input style="width: 300px;" v-model="config.BuildScoreName">
                                <p>用,隔开</p>
                            </td>
                        </tr>
                        <tr>
                            <td>纸板下单面积范围</td>
                            <td>
                                <input type="number" style="width: 100px;" v-model="config.BuildMinArea">&nbsp;&nbsp;㎡
                                &nbsp;~&nbsp;
                                <input type="number" style="width: 100px;" v-model="config.BuildMaxArea">&nbsp;&nbsp;㎡
                            </td>
                        </tr>
                        <tr>
                            <td>纸箱下单订单数范围</td>
                            <td>
                                <input type="number" style="width: 100px;" v-model="config.BuildMinOrdQty">
                                &nbsp;~&nbsp;
                                <input type="number" style="width: 100px;" v-model="config.BuildMaxOrdQty">
                            </td>
                        </tr>
                        <tr>
                            <td>默认交货日期</td>
                            <td>
                                <input style="width: 100px;" v-model="config.BuildDeliveryDate">
                            </td>
                        </tr>
                        <tr>
                            <td>交货日期范围</td>
                            <td>
                                <input style="width: 100px;" v-model="config.BuildMinDate">
                                &nbsp;~&nbsp;
                                <input style="width: 100px;" v-model="config.BuildMaxDate">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </el-tab-pane>
        <el-tab-pane label="团购" name="团购">
            <form>
                <table>
                    <tbody>
                        <tr>
                            <td>纸板团购功能</td>
                            <td>
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.UseBoardGroup">
                            </td>
                        </tr>
                        <tr v-if="Number(config.UseBoardGroup)">
                            <td>展示团购已结束的纸板</td>
                            <td>
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.ShowEdBoard">
                            </td>
                        </tr>
                        <tr v-if="Number(config.UseBoardGroup)">
                            <td>纸板特殊标识文字</td>
                            <td>
                                <input style="width: 100px;" v-model="config.BoardFlag">
                                <p>建议2个汉字</p>
                            </td>
                        </tr>
                        <tr>
                            <td>淘宝箱团购功能</td>
                            <td>
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.UseBoxGroup">
                            </td>
                        </tr>
                        <tr v-if="Number(config.UseBoxGroup)">
                            <td>展示团购已结束的淘宝箱</td>
                            <td>
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.ShowEdBox">
                            </td>
                        </tr>
                        <tr v-if="Number(config.UseBoxGroup)">
                            <td>淘宝箱特殊标识文字</td>
                            <td>
                                <input style="width: 100px;" v-model="config.BoxFlag">
                                <p>建议2个汉字</p>
                            </td>
                        </tr>
                        <tr v-if="Number(config.UseBoardGroup) || Number(config.UseBoxGroup)">
                            <td>微信支付功能</td>
                            <td>
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.UseWxPay">
                            </td>
                        </tr>
                        <tr v-if="Number(config.UseBoardGroup) || Number(config.UseBoxGroup)">
                            <td>支付宝支付功能</td>
                            <td>
                                <input type="checkbox" true-value="1" false-value="0" v-model="config.UseAliPay">
                            </td>
                        </tr>
                        <tr v-if="(Number(config.UseBoardGroup) || Number(config.UseBoxGroup)) && (Number(config.UseWxPay) || Number(config.UseAliPay))">
                            <td>有效支付时间（秒）</td>
                            <td>
                                <input type="number" style="width: 100px;" v-model="config.ValidPayTime">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </el-tab-pane>
        <el-tab-pane label="微信支付" name="微信支付" v-if="(Number(config.UseBoardGroup) || Number(config.UseBoxGroup)) && Number(config.UseWxPay)">
            <form>
                <table>
                    <tbody>
                        <tr>
                            <td>微信公众号AppSecret</td>
                            <td>
                                <input style="width: 300px;" v-model="config.WxAppSecret">
                            </td>
                        </tr>
                        <tr>
                            <td>微信商户号</td>
                            <td>
                                <input style="width: 200px;" v-model="config.WxMchId">
                            </td>
                        </tr>
                        <tr>
                            <td>微信商户支付密钥</td>
                            <td>
                                <input style="width: 300px;" v-model="config.WxKey">
                            </td>
                        </tr>
                        <tr>
                            <td>微信商户证书 apiclient_cert.pem 绝对路径</td>
                            <td>
                                <input style="width: 500px;" v-model="config.WxSslcertPath">
                                <p>建议与 htdocs 文件夹平级</p>
                            </td>
                        </tr>
                        <tr>
                            <td>微信商户证书 apiclient_key.pem 绝对路径</td>
                            <td>
                                <input style="width: 500px;" v-model="config.WxSslkeyPath">
                                <p>建议与 htdocs 文件夹平级</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </el-tab-pane>
        <el-tab-pane label="支付宝支付" name="支付宝支付" v-if="(Number(config.UseBoardGroup) || Number(config.UseBoxGroup)) && Number(config.UseAliPay)">
            <form>
                <table>
                    <tbody>
                        <tr>
                            <td>支付宝AppId</td>
                            <td>
                                <input style="width: 200px;" v-model="config.AliAppId">
                            </td>
                        </tr>
                        <tr>
                            <td>支付宝公钥</td>
                            <td>
                                <textarea rows="5" v-model="config.AliPublicKey"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>支付宝商户私钥</td>
                            <td>
                                <textarea rows="25" v-model="config.AliRsaPrivateKey"></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </el-tab-pane>
    </el-tabs>
    <input type="submit" class="btn" value="保存" style="margin: 20px 10px 0 0;" @click="saveConfig()">
    <input type="reset" class="btn" value="重置" @click="config = Object.assign({},config_)">
</div>

<script>
    new Vue({
        el: '#VueBox',
        data: {
            module: '公共',
            config: null,
            config_: null
        },
        methods: {
            saveConfig: function () {
                var _this = this;
                $.ajax({
                    url: '<?php echo U('saveConfig_api');?>',
                    type: 'post',
                    data: {config: _this.config},
                    //beforeSend: function () {},
                    success: function (respon) {
                        var respon = eval('(' + respon + ')');
                        if(respon.ret === '<?php echo C('succ_ret');?>'){
                            swal({type: 'success', title: respon.msg}).then(function () {
                                _this.getConfig();
                            });
                        }else{
                            swal({type: 'error', title: respon.msg});
                        }
                    }
                });
            },
            getConfig: function () {
                var _this = this;
                _this.config = null;
                _this.config_ = null;
                $.ajax({
                    url: '<?php echo U('getConfig_api');?>',
                    //beforeSend: function () {},
                    success: function (respon) {
                        _this.config = eval('(' + respon + ')');
                        _this.config_ = eval('(' + respon + ')');
                    }
                });
            }
        },
        mounted: function () {
            this.getConfig();
        }
    });
</script>

    </div>
</body>
</html>