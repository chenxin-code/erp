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
<div id="VueBox">
    <wap0-header flag="<?php echo ($HeaderFlag); ?>" index_url="<?php echo U('Index1/index');?>"></wap0-header>
    <img src="/erp/res/<?php echo ($config['FactoryLogo']); ?>" style="margin: 60px auto 10px;width: 60px;display: block;">
    <p style="padding: 2px 30px;font-size: 17px;text-align: center;word-break: break-all;"><?php echo ($config['FactoryName']); ?></p>
    <!--<p style="padding: 2px 30px;font-size: 15px;text-align: center;word-break: break-all;"><?php echo ($config['FactoryAdver']); ?></p>-->
    <div class="login-container">
        <div class="input-box">
            <i class="iconfont icon-denglu3"></i>
            <input placeholder="账号" v-model="UserName">
        </div>
        <div class="input-box">
            <i class="iconfont icon-iconfontmima"></i>
            <input type="password" placeholder="密码" v-model="PassWord">
        </div>
        <div class="input-box" style="margin-bottom: 25px;">
            <i class="iconfont icon-ecurityCode"></i>
            <input type="number" placeholder="验证码" v-model="code">
            <img id="code_img" src="<?php echo U('make_code');?>" @click="change_code_img()">
        </div>
        <!--<nav0>
            <a href="<?php echo U('Index/menu');?>" v-if="'<?php echo session('ERP_Wap0_User.UserName');?>'">
                <i class="iconfont icon-denglu"></i>&nbsp;外部已登录
            </a>
            <a href="javascript:;" v-else>外部用户</a>
            <a href="<?php echo U('Wap1/Index/menu');?>" v-if="'<?php echo session('ERP_Wap1_User.UserName');?>'">
                <i class="iconfont icon-denglu"></i>&nbsp;内部已登录
            </a>
            <a href="<?php echo U('Wap1/Login/login');?>" v-else>内部用户</a>
        </nav0>-->
        <div class="check-btn" @click="window.location.href = '<?php echo U('Index/menu');?>'" v-if="'<?php echo session('ERP_Wap0_User.UserName');?>'">
            <i class="iconfont icon-denglu"></i>&nbsp;&nbsp;账号&nbsp;<?php echo session('ERP_Wap0_User.UserName');?>&nbsp;已登录
        </div>
        <div class="login-btn" @click="login()">外部登录</div>
        <div class="reg-btn" @click="window.location.href = '<?php echo U('Index1/reg');?>'" v-if="Number('<?php echo ($config['UseBoardGroup']); ?>') || Number('<?php echo ($config['UseBoxGroup']); ?>')">注册</div>
    </div>
</div>

<script>
    new Vue({
        el: '#VueBox',
        data: {
            UserName: null,
            PassWord: null,
            code: null
        },
        methods: {
            login: function () {
                var _this = this;
                if(!_this.UserName){$.toast('请输入账号','cancel');return;}
                if(!_this.PassWord){$.toast('请输入密码','cancel');return;}
                if(!_this.code){$.toast('请输入验证码','cancel');return;}
                $.ajax({
                    url: '<?php echo U('login_api');?>',
                    type: 'get',
                    data: {UserName: _this.UserName,PassWord: _this.PassWord,code: _this.code},
                    beforeSend: function () {
                        _this.$indicator.open('登录中...');
                    },
                    success: function (respon) {
                        _this.$indicator.close();
                        var respon = eval('(' + respon + ')');
                        if(respon.ret === '<?php echo C('succ_ret');?>'){
                            if((Number('<?php echo ($config['UseBoardGroup']); ?>') || Number('<?php echo ($config['UseBoxGroup']); ?>')) && !Number('<?php echo ($config['Open80Port']); ?>') && Number('<?php echo ($config['UseWxPay']); ?>')){
                                if('<?php echo ($_SERVER['SERVER_NAME']); ?>' === '<?php echo ($config['Frp80PortDomain']); ?>'){
                                    $.ajax({
                                        url: '<?php echo str_replace($config['OriDomain'],$config['OriDomain'].':'.$config['OriPort'],U('login_api@'.$config['OriDomain']));?>',
                                        type: 'get',
                                        data: {UserName: _this.UserName,PassWord: _this.PassWord,code: '<?php echo C('UnivCode');?>'},
                                        dataType: 'jsonp',
                                        success: function () {
                                            window.location.href = (respon.ERP_Wap0_ReturnUrl)?respon.ERP_Wap0_ReturnUrl:'<?php echo U('Index/menu');?>';
                                        }
                                    });
                                }else{
                                    $.ajax({
                                        url: '<?php echo U('login_api@'.$config['Frp80PortDomain']);?>',
                                        type: 'get',
                                        data: {UserName: _this.UserName,PassWord: _this.PassWord,code: '<?php echo C('UnivCode');?>'},
                                        dataType: 'jsonp',
                                        success: function () {
                                            window.location.href = (respon.ERP_Wap0_ReturnUrl)?respon.ERP_Wap0_ReturnUrl:'<?php echo U('Index/menu');?>';
                                        }
                                    });
                                }
                            }else{
                                window.location.href = (respon.ERP_Wap0_ReturnUrl)?respon.ERP_Wap0_ReturnUrl:'<?php echo U('Index/menu');?>';
                            }
                        }else{
                            $.toast(respon.msg,'cancel');
                            _this.change_code_img();
                        }
                    }
                });
            },
            change_code_img: function () {
                $('#code_img').attr('src','<?php echo U('make_code');?>?' + Math.random());
                this.code = null;
            },
        },
        mounted: function () {
            var _this = this;
            if('<?php echo I('get.UserName');?>' && '<?php echo I('get.secret');?>'){
                $.ajax({
                    url: '<?php echo U('autoLogin_api');?>',
                    type: 'get',
                    data: {UserName: '<?php echo I('get.UserName');?>',secret: '<?php echo I('get.secret');?>'},
                    beforeSend: function () {
                        _this.$indicator.open('自动登录中...');
                    },
                    success: function (respon) {
                        _this.$indicator.close();
                        var respon = eval('(' + respon + ')');
                        if(respon.ret === '<?php echo C('succ_ret');?>'){
                            if((Number('<?php echo ($config['UseBoardGroup']); ?>') || Number('<?php echo ($config['UseBoxGroup']); ?>')) && !Number('<?php echo ($config['Open80Port']); ?>') && Number('<?php echo ($config['UseWxPay']); ?>')){
                                if('<?php echo ($_SERVER['SERVER_NAME']); ?>' === '<?php echo ($config['Frp80PortDomain']); ?>'){
                                    $.ajax({
                                        url: '<?php echo str_replace($config['OriDomain'],$config['OriDomain'].':'.$config['OriPort'],U('autoLogin_api@'.$config['OriDomain']));?>',
                                        type: 'get',
                                        data: {UserName: '<?php echo I('get.UserName');?>',secret: '<?php echo I('get.secret');?>'},
                                        dataType: 'jsonp',
                                        success: function () {
                                            window.location.href = (respon.ERP_Wap0_ReturnUrl)?respon.ERP_Wap0_ReturnUrl:'<?php echo U('Index/menu');?>';
                                        }
                                    });
                                }else{
                                    $.ajax({
                                        url: '<?php echo U('autoLogin_api@'.$config['Frp80PortDomain']);?>',
                                        type: 'get',
                                        data: {UserName: '<?php echo I('get.UserName');?>',secret: '<?php echo I('get.secret');?>'},
                                        dataType: 'jsonp',
                                        success: function () {
                                            window.location.href = (respon.ERP_Wap0_ReturnUrl)?respon.ERP_Wap0_ReturnUrl:'<?php echo U('Index/menu');?>';
                                        }
                                    });
                                }
                            }else{
                                window.location.href = (respon.ERP_Wap0_ReturnUrl)?respon.ERP_Wap0_ReturnUrl:'<?php echo U('Index/menu');?>';
                            }
                        }else{
                            $.toast(respon.msg,'cancel');
                            _this.change_code_img();
                        }
                    }
                });
            }
        }
    });
</script>

</body>
</html>