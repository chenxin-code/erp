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
<style>
    body {
        background-color: #f2f2f2;
    }
    .way-item {
        padding: 7px 10px 7px 14px;
        height: 50px;
        background-color: #fff;
        cursor: pointer;
        position: relative;
    }
    .way-item::before {
        content: '';
        display: inline-block;
        margin-top: -6px;
        height: 8px;
        width: 8px;
        border-width: 1.5px 1.5px 0 0;
        border-color: #c8c8cd;
        border-style: solid;
        transform: matrix(0.71, 0.71, -0.71, 0.71, 0, 0);
        position: absolute;
        top: 50%;
        right: 15px;
    }
    .way-item::after {
        content: '';
        height: 1px;
        background-color: #c8c7cc;
        transform: scaleY(0.35);
        position: absolute;
        bottom: 0;
        left: 5px;
        right: 0;
    }
    .way-item:last-child::after {
        left: 0;
    }
    .way-item img {
        margin: 5px 15px 5px 0;
        width: 40px;
        height: 40px;
        vertical-align: middle;
    }
    .way-item span {
        line-height: 50px;
        font-size: 16px;
    }
</style>

<div id="VueBox">
    <pay-header flag="<?php echo ($HeaderFlag); ?>" index_url="<?php echo U('Wap0/Index1/index');?>" menu_url="<?php echo U('Wap0/Index/menu');?>"></pay-header>
    <div style="margin: 40px 0 12px;padding: 25px 0 15px 0;background-color: #fff;position: relative;">
        <div style="padding: 0 10px;font-size: 14px;color: #696969;text-align: center;word-break: break-all;"><?php echo ($orderName); ?></div>
        <div style="margin-top: 5px;font-size: 28px;color: #1aad19;text-align: center;">¥<?php echo ($PayAmount); ?></div>
        <vue2-countdown
                :start-time="<?php echo ($PayDeadlineTime); ?>"
                :end-time="<?php echo ($PayDeadlineTime); ?>"
                :current-time="'<?php echo time();?>'"
                :tip-text="'有效时间剩余'"
                :end-text="'已超出有效时间'"
                v-on:end_callback="valid = false"
                style="margin-top: 10px;font-size: 15px;text-align: center;">
        </vue2-countdown>
        <div style="padding: 0 3px;font-size: 12px;color: #fff;background-color: #4b0;position: absolute;top: 0;right: 0;" v-if="Number('<?php echo ($OneCentPay); ?>')">1分钱支付</div>
    </div>
    <div class="way-item" @click="pay('wx')" v-if="Number('<?php echo ($config['UseWxPay']); ?>')">
        <img src="/erp/res/wx.png">
        <span>微信支付</span>
    </div>
    <div class="way-item" @click="pay('ali')" v-if="Number('<?php echo ($config['UseAliPay']); ?>')">
        <img src="/erp/res/ali.png">
        <span>支付宝支付</span>
    </div>
</div>

<script>
    new Vue({
        el: '#VueBox',
        data: {
            valid: true
        },
        methods: {
            pay: function (way) {
                if(this.valid){
                    if(way === 'wx'){
                        if(navigator.userAgent.toLowerCase().indexOf('micromessenger') === -1){
                            //非微信浏览器
                            window.location.href = '<?php echo U('Wx/native');?>?CusId=<?php echo ($CusId); ?>&CusPoNo=<?php echo ($CusPoNo); ?>&secret=<?php echo ($secret); ?>';
                        }else{
                            //微信浏览器
                            window.location.href = '<?php echo U('Wx/jsapi');?>?CusId=<?php echo ($CusId); ?>&CusPoNo=<?php echo ($CusPoNo); ?>&secret=<?php echo ($secret); ?>';
                        }
                    }else if(way === 'ali'){
                        window.location.href = '<?php echo U('Ali/build');?>?CusId=<?php echo ($CusId); ?>&CusPoNo=<?php echo ($CusPoNo); ?>&secret=<?php echo ($secret); ?>';
                    }
                }else{
                    $.alert('已超出有效时间','');
                }
            }
        }
    });
</script>

</body>
</html>