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
<!-- 引入 swiper 插件 -->
<script src="/erp/res/swiper/swiper.min.js"></script>
<link rel="stylesheet" href="/erp/res/swiper/swiper.min.css">

<div id="VueBox">
    <wap0-header flag="<?php echo ($HeaderFlag); ?>"></wap0-header>
    <div class="swiper-container" style="margin-top: 40px;">
        <div class="swiper-wrapper">
            <div class="swiper-slide" v-for="(v,k) in AdverPic">
                <img :src="'/erp/res/' + v" style="width: 100%;">
            </div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
    <div style="margin: 10px 0;text-align: center;display: flex;">
        <a href="<?php echo U('Index/menu');?>" style="flex: 1;">
            <img src="/erp/res/kehu.jpg" style="width: 50%;">
            <div style="line-height: 30px;color: #666;font-size: 15px;">我是客户</div>
        </a>
        <a href="<?php echo U('Wap1/Index/menu');?>" style="flex: 1;">
            <img src="/erp/res/yuangong.jpg" style="width: 50%;">
            <div style="line-height: 30px;color: #666;font-size: 15px;">我是员工</div>
        </a>
        <a href="<?php echo U('contact');?>" style="flex: 1;">
            <img src="/erp/res/contact.jpg" style="width: 50%;">
            <div style="line-height: 30px;color: #666;font-size: 15px;">联系方式</div>
        </a>
    </div>
    <div style="margin-top: 5px;display: flex;" v-if="UseBoardGroup">
        <div style="margin-right: 1px;flex: 1;position: relative;">
            <img src="/erp/res/<?php echo ($config['BoardGroupPic']); ?>" style="width: 100%;cursor: pointer;" @click="window.location.href = '<?php echo U('Group/Board/lists');?>'">
            <div style="line-height: 16px;color: #fff;position: absolute;top: 0;right: 0;">
                <div style="padding: 0 3px;font-size: 12px;background-color: #d51938;float: right;">纸板团购</div>
            </div>
        </div>
        <div style="margin-left: 1px;flex: 1;position: relative;">
            <img src="/erp/res/<?php echo ($config['FlagBoardGroupPic']); ?>" style="width: 100%;cursor: pointer;" @click="window.location.href = '<?php echo U('Group/Board/FlagLists');?>'">
            <div style="line-height: 16px;color: #fff;position: absolute;top: 0;right: 0;">
                <div style="padding: 0 3px;font-size: 12px;background-color: #d51938;float: right;">纸板团购（<?php echo ($config['BoardFlag']); ?>）</div>
            </div>
        </div>
    </div>
    <div :style="{'margin-top':UseBoardGroup?'-5px':'5px'}" style="display: flex;" v-if="UseBoxGroup">
        <div style="margin-right: 1px;flex: 1;position: relative;">
            <img src="/erp/res/<?php echo ($config['BoxGroupPic']); ?>" style="width: 100%;cursor: pointer;" @click="window.location.href = '<?php echo U('Group/Box/lists');?>'">
            <div style="line-height: 16px;color: #fff;position: absolute;top: 0;right: 0;">
                <div style="padding: 0 3px;font-size: 12px;background-color: #d51938;float: right;">淘宝箱团购</div>
            </div>
        </div>
        <div style="margin-left: 1px;flex: 1;position: relative;">
            <img src="/erp/res/<?php echo ($config['FlagBoxGroupPic']); ?>" style="width: 100%;cursor: pointer;" @click="window.location.href = '<?php echo U('Group/Box/FlagLists');?>'">
            <div style="line-height: 16px;color: #fff;position: absolute;top: 0;right: 0;">
                <div style="padding: 0 3px;font-size: 12px;background-color: #d51938;float: right;">淘宝箱团购（<?php echo ($config['BoxFlag']); ?>）</div>
            </div>
        </div>
    </div>
    <div style="margin: 0 3px;padding-top: 10px;color: #999;font-size: 14px;text-align: center;border-top: 1px solid #999;">
        杭州利鹏科技有限公司&nbsp;提供技术支持
        <br>联系电话：<a href="tel:18768443628" style="color: #999;">18768443628</a>
    </div>
</div>

<script>
    new Vue({
        el: '#VueBox',
        data: {
            AdverPic: eval('(' + '<?php echo ($AdverPic); ?>' + ')'),
            UseBoardGroup: Number('<?php echo ($config['UseBoardGroup']); ?>'),
            UseBoxGroup: Number('<?php echo ($config['UseBoxGroup']); ?>')
        },
        mounted: function () {
            new Swiper('.swiper-container',{
                autoHeight: true,
                autoplay: {
                    delay: 5000,
                    stopOnLastSlide: false,
                    disableOnInteraction: true
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                }
            });
        }
    });
</script>

</body>
</html>