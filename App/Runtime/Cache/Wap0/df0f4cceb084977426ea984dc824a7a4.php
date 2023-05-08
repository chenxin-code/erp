<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <link href="/erp/res/<?php echo ($config['FactoryLogo']); ?>" rel="shortcut icon">
    <title><?php echo ($LayoutTitle); ?></title>
    <script src="/erp/res/vue.js"></script>
    <!-- jQuery -->
    <script src="/erp/res/jquery.min.js"></script>
    <!-- jQuery WeUI -->
    <link rel="stylesheet" href="/erp/res/jqweui/css/weui.min.css">
    <link rel="stylesheet" href="/erp/res/jqweui/css/jquery-weui.min.css">
    <script src="/erp/res/jqweui/js/jquery-weui.min.js"></script>
    <!-- mint-ui -->
    <link rel="stylesheet" href="/erp/res/mint-ui/style.css">
    <script src="/erp/res/mint-ui/index.js"></script>
    <!-- ElementUI -->
    <link rel="stylesheet" href="/erp/res/ElementUI/index.css">
    <script src="/erp/res/ElementUI/index.js"></script>
    <!-- 阿里图标cdn -->
    <link rel="stylesheet" href="<?php echo C('ali_iconfont_cdn');?>">
    <!-- common 样式 -->
    <link rel="stylesheet" href="/erp/res/common.css?time=<?php echo time();?>">
    <!-- 自定义组件 -->
    <script src="/erp/res/component.js?time=<?php echo time();?>"></script>
    <!-- 函数库 -->
    <script src="/erp/res/function.js?time=<?php echo time();?>"></script>
</head>
<body>
<script src="/erp/res/qrcode.min.js"></script>

<div id="VueBox">
    <wap0-header
            flag="<?php echo ($HeaderFlag); ?>"
            index_url="<?php echo U('Index1/index');?>"
            logout_url="<?php echo U('logout_api');?>"
            use_board_group="<?php echo ($config['UseBoardGroup']); ?>"
            use_box_group="<?php echo ($config['UseBoxGroup']); ?>"
            open_80port="<?php echo ($config['Open80Port']); ?>"
            use_wxpay="<?php echo ($config['UseWxPay']); ?>"
            server_name="<?php echo ($_SERVER['SERVER_NAME']); ?>"
            frp_80port_domain="<?php echo ($config['Frp80PortDomain']); ?>"
            logout_ori_url="<?php echo str_replace($config['OriDomain'],$config['OriDomain'].':'.$config['OriPort'],U('logout_api@'.$config['OriDomain']));?>"
            logout_frp_80port_url="<?php echo U('logout_api@'.$config['Frp80PortDomain']);?>">
    </wap0-header>
    <div class="menu-top-container">
        <div class="flag">
            账号&nbsp;<span style="color: #1aad19;"><?php echo session('ERP_Wap0_User.UserName');?></span>
        </div>
        <div class="btn-box">
            <div class="btn" @click="window.location.href = '<?php echo U('change_pwd');?>'">
                <i class="iconfont icon-iconfontmima"></i>&nbsp;更改密码
            </div>
            <div class="btn" @click="document.body.classList.add('body-lock');showQrcode = true">
                <i class="iconfont icon-erweima2"></i>&nbsp;登录二维码
            </div>
        </div>
    </div>
    <transition name="fullpage1">
        <div class="qrcode-fullpage" v-show="showQrcode">
            <div id="qrcode"></div>
            <p>当前账号&nbsp;<span style="color: #3cc51f;"><?php echo session('ERP_Wap0_User.UserName');?></span>&nbsp;登录二维码</p>
            <div class="close-btn" @click="document.body.classList.remove('body-lock');showQrcode = false">关闭</div>
        </div>
    </transition>
    <menu-box :lists="lists" right="<?php echo ($config['Wap0Right']); ?>" ur_name="<?php echo ($URName); ?>" empty_img="/erp/res/empty.jpg"></menu-box>
</div>

<script>
    new Vue({
        el: '#VueBox',
        data: {
            showQrcode: false,
            qrcode: null,
            lists: [
                {URName: '纸板下单',title: '简单纸板下单',href: '<?php echo U('Build/s');?>',iconClass: 'icon-mobancaidan',},
                {URName: '纸板下单',title: '纸箱纸板下单',href: '<?php echo U('Build/c');?>',iconClass: 'icon-zhixiang',},
                {URName: '纸箱下单',title: '纸箱下单',href: '<?php echo U('Build/x');?>',iconClass: 'icon-zhixiang1',},
                {URName: '纸板下单@纸箱下单@淘宝箱下单',title: '微信订单',href: '<?php echo U('Weborder/lists');?>',iconClass: 'icon-shouji',},
                {URName: '纸板下单@纸箱下单',title: '常用订单',href: '<?php echo U('Usedorder/lists');?>',iconClass: 'icon-dingdan1',},
                {URName: '纸板下单',title: '常用材质',href: '<?php echo U('Usedboard/lists');?>',iconClass: 'icon-caizhi',},
                {URName: 'ERP订单',title: 'ERP订单',href: '<?php echo U('Order/GetOrders');?>',iconClass: 'icon-erp',},
                {URName: '每日订单',title: '每日订单',href: '<?php echo U('Order1/GetOrdersP');?>',iconClass: 'icon-meiri',},
                {URName: '对账单',title: '对账单',href: '<?php echo U('Order/GetCusFreeMB');?>',iconClass: 'icon-duizhang',},
                {URName: '报价查询',title: '报价价格',href: '<?php echo U('Quo/GetQuoPriceByCus');?>',iconClass: 'icon-jiagechaxun',},
                {URName: '报价查询',title: '报价规则',href: '<?php echo U('Quo/GetQuoRuleByCus');?>',iconClass: 'icon-tichengguize',},
                {URName: '信用余额',title: '信用余额',href: '<?php echo U('Cred/WGetCusAmt');?>',iconClass: 'icon-xinyongyue',},
            ],
        },
        mounted: function () {
            this.qrcode = new QRCode('qrcode');
            this.qrcode.makeCode('<?php echo U('Login/login','',true,true);?>?UserName=<?php echo session('ERP_Wap0_User.UserName');?>&secret=<?php echo md5(session('ERP_Wap0_User.UserName').C('md5_salt'));?>');
        }
    });
</script>

</body>
</html>