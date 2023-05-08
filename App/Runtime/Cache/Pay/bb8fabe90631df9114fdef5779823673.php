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
    .apply-box {
        margin: 40px 18px;
        padding: 15px 0;
    }
    .apply-box .pane {
        margin-left: 10px;
        padding: 1px 2px;
        font-size: 12px;
        border-radius: 2px;
    }
    .apply-box .pane.s1 {
        color: #4b0;
        border: 1px solid #4b0;
    }
    .apply-box .pane.s2 {
        color: #fff;
        background-color: #4b0;
    }
    .apply-box textarea {
        margin: 15px 0;
        padding: 10px 15px;
        height: 160px;
        width: 100%;
        font-size: 16px;
        border: 1px solid rgba(0,0,0,0.2);
        border-radius: 3px;
        box-sizing: border-box;
    }
    .apply-box textarea::-webkit-input-placeholder {
        color: #b3b3b3;
    }
    .apply-box textarea:-moz-placeholder {
        color: #b3b3b3;
    }
    .apply-box textarea::-moz-placeholder {
        color: #b3b3b3;
    }
    .apply-box textarea:-ms-input-placeholder {
        color: #b3b3b3;
    }
    .apply-box button {
        padding: 10px 0;
        width: 100%;
        font-size: 17px;
        color: #fff;
        background-color: #4b0;
        border: 1px solid #0a0;
        cursor: pointer;
    }
    .apply-box button:hover {
        background-color: #179b16;
    }
</style>

<div id="VueBox">
    <pay-header flag="<?php echo ($HeaderFlag); ?>" index_url="<?php echo U('Wap0/Index1/index');?>" menu_url="<?php echo U('Wap0/Index/menu');?>"></pay-header>
    <div class="apply-box">
        <span v-if="PayAmount">
            退款金额：<span style="color: red;">¥{{PayAmount}}</span>
        </span>
        <span class="pane s1" v-if="Apply === '1' && Refund === '0'">申请退款中</span>
        <span class="pane s2" v-if="Apply === '1' && Refund === '1'">已退款</span>
        <textarea v-model="ApplyReason" placeholder="须填写退款原因" :disabled="Apply === '1'"></textarea>
        <button @click="apply()" v-if="Apply === '0'">提交</button>
    </div>
</div>

<script>
    new Vue({
        el: '#VueBox',
        data: {
            CusPoNo: '<?php echo ($CusPoNo); ?>',
            PayAmount: null,
            Apply: null,
            ApplyReason: null,
            Refund: null
        },
        methods: {
            getApplyInfo: function () {
                var _this = this;
                $.ajax({
                    url: '<?php echo U('getApplyInfo_api');?>',
                    type: 'get',
                    data: {CusPoNo: _this.CusPoNo},
                    beforeSend: function () {
                        _this.$indicator.open();
                    },
                    success: function (respon) {
                        _this.$indicator.close();
                        var respon = eval('(' + respon + ')');
                        _this.PayAmount = respon.PayAmount;
                        _this.Apply = respon.Apply;
                        _this.ApplyReason = respon.ApplyReason;
                        _this.Refund = respon.Refund;
                    }
                });
            },
            apply: function () {
                var _this = this;
                if(!_this.ApplyReason){$.toast('请填写退款原因','text');return;}
                $.ajax({
                    url: '<?php echo U('apply_api');?>',
                    type: 'post',
                    data: {
                        CusPoNo: _this.CusPoNo,
                        ApplyReason: _this.ApplyReason
                    },
                    beforeSend: function () {
                        _this.$indicator.open();
                    },
                    success: function (respon) {
                        _this.$indicator.close();
                        var respon = eval('(' + respon + ')');
                        if(respon.ret === '<?php echo C('succ_ret');?>'){
                            $.alert(respon.msg,'',function () {
                                _this.getApplyInfo();
                            });
                        }else{
                            $.toast(respon.msg,'forbidden');
                        }
                    }
                });
            }
        },
        mounted: function () {
            this.getApplyInfo();
        }
    });
</script>

</body>
</html>