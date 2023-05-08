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
<!-- 引入 swiper 插件 -->
<script src="/erp/res/swiper/swiper.min.js"></script>
<link rel="stylesheet" href="/erp/res/swiper/swiper.min.css">
<!-- vue无缝滚动组件 -->
<script src="/erp/res/vue-seamless-scroll.js"></script>

<style>
    .sep-line {
        background-color: #f5f5f5;
        height: 10px;
        border-top: 1px solid #e5e5e5;
    }
    .title-box {
        padding-left: 15px;
        line-height: 50px;
        font-size: 18px;
        border-bottom: 1px solid #e5e5e5;
    }
    .range-price-item {
        padding: 10px 15px;
        font-size: 15px;
        display: flex;
        position: relative;
    }
    .range-price-item::after {
        content: '';
        height: 1px;
        background-color: #c8c7cc;
        transform: scaleY(0.35);
        position: absolute;
        bottom: 0;
        left: 8px;
        right: 0;
    }
    .range-price-item:last-child::after {
        display: none;
    }
    .range-price-item > span {
        word-wrap: break-word;
        text-overflow: ellipsis;
        overflow: hidden;
    }
    .progress-bar {
        margin: 10px 15px;
        height: 35px;
        background-color: #fff;
        border: 1px solid #fc91b1;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
    }
    .progress-bar .liquid {
        height: 100%;
        background-color: #fedee8;
        border-radius: 20px;
    }
    .progress-bar .descr {
        width: 100%;
        line-height: 35px;
        color: #e60044;
        font-size: 15px;
        text-align: center;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        position: absolute;
        top: 0;
    }
    .buyer-order-header {
        background-color: #f6f6f6;
        border-bottom: 1px solid #e5e5e5;
        overflow: hidden;
    }
    .buyer-order-header > div {
        box-sizing: border-box;
        line-height: 35px;
        width: 25%;
        text-align: center;
        font-size: 14px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        float: left;
    }
    .buyer-order-body {
        max-height: 150px;
        background-color: #fff;
        padding: 5px 0;
        overflow: hidden;
    }
    .buyer-order-body .item {
        line-height: 26px;
        font-size: 13px;
        color: #333;
        overflow: hidden;
    }
    .buyer-order-body .item > span {
        box-sizing: border-box;
        padding: 0 8px;
        width: 25%;
        text-align: center;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        float: left;
    }
    .bottom-btn {
        line-height: 45px;
        width: 100%;
        color: #fff;
        font-size: 22px;
        text-align: center;
        cursor: pointer;
        position: fixed;
        bottom: 0;
        z-index: 1;
    }
</style>

<div id="VueBox">
    <group-header flag="<?php echo ($HeaderFlag); ?>" index_url="<?php echo U('Wap0/Index1/index');?>" menu_url="<?php echo U('Wap0/Index/menu');?>"></group-header>
    <div class="swiper-container" style="margin-top: 40px;">
        <div class="swiper-wrapper">
            <div class="swiper-slide" v-for="v in detail.Pic">
                <img style="width: 100%;" :src="'/erp/res/' + v">
            </div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
    <div style="padding: 7px 15px 0;font-size: 18px;word-break: break-all;">
        <span style="color: #e01835;">{{detail.BoardId}}</span><span v-if="detail.Title">,{{detail.Title}}</span>
    </div>
    <span style="padding-left: 15px;font-size: 22px;color: #de1935;">¥{{detail.Price}}/㎡</span>
    <span style="font-size: 14px;color: #cacaca;text-decoration: line-through;">¥{{detail.MarketPrice}}/㎡</span>
    <div v-if="Number(detail.IsRangePrice)">
        <div class="sep-line"></div>
        <div class="title-box">区间价</div>
        <div class="range-price-item" v-for="v in detail.RangePrice">
            <span style="flex: 3;color: #888;">{{v.From}}&nbsp;㎡&nbsp;~&nbsp;{{v.To}}&nbsp;㎡</span>
            <span style="flex: 1;color: #e01835;text-align: right;">¥{{v.Price}}/㎡</span>
        </div>
    </div>
    <div class="sep-line"></div>
    <vue2-countdown
            :start-time="detail.BeginTime"
            :end-time="detail.EndTime"
            :current-time="'<?php echo time();?>'"
            :tip-text="'距团购开始'"
            :tip-text-end="'距团购结束'"
            :end-text="'团购已结束'"
            v-on:start_callback="State = 'ing'"
            v-on:end_callback="State = 'ed'"
            style="padding: 15px;background-color: #fff;font-size: 18px;">
    </vue2-countdown>
    <div class="sep-line"></div>
    <div class="title-box">总客户限量&nbsp;{{detail.Total}}&nbsp;㎡</div>
    <div class="progress-bar">
        <div class="liquid" :style="'width: ' + detail.SalePercent + '%;'"></div>
        <div class="descr">已抢&nbsp;{{detail.SalePercent}}%&nbsp;&nbsp;剩余&nbsp;{{detail.Surp1}}&nbsp;㎡</div>
    </div>
    <div v-if="Number(detail.isLogin)">
        <div class="sep-line"></div>
        <div class="title-box">单客户限量&nbsp;{{detail.CusMax}}&nbsp;㎡</div>
        <div class="progress-bar">
            <div class="liquid" :style="'width: ' + detail.CusPercent + '%;'"></div>
            <div class="descr">你已购&nbsp;{{detail.CusPercent}}%&nbsp;&nbsp;剩余&nbsp;{{detail.Surp2}}&nbsp;㎡</div>
        </div>
    </div>
    <div v-if="!$.isEmptyObject(BuyerOrder)">
        <div class="sep-line"></div>
        <div class="title-box">买家订单</div>
        <div class="buyer-order-header">
            <div>采购商</div>
            <div>下单面积(㎡)</div>
            <div>下单金额(元)</div>
            <div>节省金额(元)</div>
        </div>
        <vue-seamless-scroll class="buyer-order-body" :data="BuyerOrder">
            <div class="item" v-for="v in BuyerOrder">
                <span>{{v.CusId}}</span>
                <span>{{v.Area}}</span>
                <span>{{v.Cost}}</span>
                <span>{{v.SaveCost}}</span>
            </div>
        </vue-seamless-scroll>
    </div>
    <div v-if="Descr">
        <div class="sep-line"></div>
        <div class="title-box">产品描述</div>
        <div style="padding: 5px 15px;word-break: break-all;" v-html="Descr"></div>
    </div>
    <div class="sep-line" style="margin-bottom: 65px;"></div>
    <div class="bottom-btn" style="background-color: grey;" @click="$.alert('团购未开始','')" v-if="State === 'tobe'">团购未开始</div>
    <div class="bottom-btn" style="background-color: #e01835;" @click="BuildWay()" v-else-if="State === 'ing'">立即抢购</div>
    <div class="bottom-btn" style="background-color: grey;" @click="$.alert('团购已结束','')" v-else>团购已结束</div>
</div>

<script>
    new Vue({
        el: '#VueBox',
        data: {
            detail: eval('(' + '<?php echo ($detail); ?>' + ')'),
            State: 'tobe',
            BuyerOrder: eval('(' + '<?php echo ($BuyerOrder); ?>' + ')'),
            Descr: '<?php echo ($Descr); ?>'
        },
        methods: {
            BuildWay: function () {
                $.actions({
                    title:  '选择下单方式',
                    actions: [
                        {
                            text: '简单纸板',
                            className: 'color-primary',
                            onClick: function () {
                                window.location.href = '<?php echo U('Buildboard/s');?>?Id=<?php echo ($_GET['Id']); ?>';
                            }
                        },
                        {
                            text: '纸箱纸板',
                            className: 'color-primary',
                            onClick: function () {
                                window.location.href = '<?php echo U('Buildboard/c');?>?Id=<?php echo ($_GET['Id']); ?>';
                            }
                        }
                    ]
                });
            }
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