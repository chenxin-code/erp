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
    .timeline {
        margin-bottom: 40px;
        padding: 22px 25px;
    }
    .timeline .item {
        position: relative;
    }
    .timeline .item .head {
        content: '';
        width: 10px;
        height: 10px;
        background-color: #1aad19;
        border-radius: 99px;
        position: absolute;
        left: 1px;
        top: 4px;
        z-index: 1;
    }
    .timeline .item:first-child .head {
        width: 20px;
        height: 20px;
        left: -4px;
        top: 5px;
    }
    .timeline .item .head .checked {
        display: none;
        width: 20px;
        height: 20px;
        position: absolute;
        left: 0;
        top: 0;
    }
    .timeline .item:first-child .head .checked {
        display: block;
    }
    .timeline .item .head .checked.weui-icon-success-no-circle::before {
        margin: 0;
        font-size: 14px;
        color: #fff;
        position: absolute;
        left: 3px;
        top: 3px;
    }
    .timeline .item .tail {
        content: '';
        height: 100%;
        width: 2px;
        background-color: #1aad19;
        position: absolute;
        left: 5px;
        top: 5px;
    }
    .timeline .item:last-child .tail {
        display: none;
    }
    .timeline .item .content {
        padding: 0 0 25px 30px;
        word-break: break-all;
    }
    .timeline .item .content p:nth-child(1) {
        color: #666;
        font-weight: normal;
    }
    .timeline .item .content p:nth-child(2) {
        color: #888;
        font-size: 13px;
    }
    .timeline .item:first-child .content p {
        color: #04be02;
    }
    /*******************************************/
    .bottom-btn-box {
        width: 100%;
        padding: 0 10px;
        background-color: #f5f7fa;
        box-sizing: border-box;
        position: fixed;
        bottom: 0;
        z-index: 2;
    }
    .bottom-btn-box::before {
        content: '';
        height: 1px;
        background-color: #c8c7cc;
        transform: scaleY(0.3);
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
    }
    .bottom-btn-box .btn {
        margin: 8px 5px;
        padding: 0 10px;
        font-size: 16px;
        text-align: center;
        border: 0.5px solid #999;
        border-radius: 2px;
        cursor: pointer;
        float: right;
    }
    .bottom-btn-box .btn:active {
        color: #fff;
        background-color: #4b0;
        border-color: #0a0;
    }
</style>

<div id="VueBox">
    <pay-header flag="<?php echo ($HeaderFlag); ?>" index_url="<?php echo U('Wap0/Index1/index');?>" menu_url="<?php echo U('Wap0/Index/menu');?>"></pay-header>
    <div class="product-card" style="margin-top: 40px;" @click="window.location.href = '<?php echo U('Group/Board/detail');?>?Id=' + wgo.WebProductId" v-if="wo.BoardId && !wo.MatNo">
        <div class="item1">
            <img :src="'/erp/res/' + wgo.FirstPic">
        </div>
        <div class="item2">
            <div class="title">
                <span style="color: #e01835;">{{wo.BoardId}}</span><span v-if="wgo.Title">,{{wgo.Title}}</span>
            </div>
            <div class="descr">
                下单价格&nbsp;<span style="color: #e01835;">¥{{wgo.Price}}/㎡</span>&nbsp;<span style="color: #999;text-decoration: line-through;">¥{{wgo.MarketPrice}}/㎡</span>
            </div>
            <div class="descr">
                下单金额&nbsp;<span style="color: #e01835;">¥{{wgo.Cost}}</span>
            </div>
            <div class="descr">
                节省金额&nbsp;<span style="color: #e01835;">¥{{wgo.SaveCost}}</span>
            </div>
        </div>
    </div>
    <div class="product-card" style="margin-top: 40px;" @click="window.location.href = '<?php echo U('Group/Box/detail');?>?Id=' + wgo.WebProductId" v-else-if="!wo.BoardId && wo.MatNo">
        <div class="item1">
            <img :src="'/erp/res/' + wgo.FirstPic">
        </div>
        <div class="item2">
            <div class="title">
                <span style="color: #e01835;">{{wo.MatNo}}</span><span v-if="wgo.Title">,{{wgo.Title}}</span>
            </div>
            <div class="descr">
                下单价格&nbsp;<span style="color: #e01835;">¥{{wgo.Price}}/个</span>&nbsp;<span style="color: #999;text-decoration: line-through;">¥{{wgo.MarketPrice}}/个</span>
            </div>
            <div class="descr">
                下单金额&nbsp;<span style="color: #e01835;">¥{{wgo.Cost}}</span>
            </div>
            <div class="descr">
                节省金额&nbsp;<span style="color: #e01835;">¥{{wgo.SaveCost}}</span>
            </div>
        </div>
    </div>
    <div class="timeline">
        <div class="item" v-for="v in timeline">
            <div class="head">
                <i class="checked weui-icon-success-no-circle"></i>
            </div>
            <div class="tail"></div>
            <div class="content">
                <p>{{v.descr}}</p>
                <p>{{v.datetime}}</p>
            </div>
        </div>
    </div>
    <div class="bottom-btn-box">
        <div class="btn" @click="window.location.href = '<?php echo U('way');?>?CusPoNo=' + CusPoNo" v-if="wp.Paid === '0' && wo.IsDel === '0'">
            <span v-if="wp.PayDeadlineTime < '<?php echo time();?>'">
                <i class="iconfont icon-chaoshi3"></i>&nbsp;付款已超时
            </span>
            <span v-else>
                <i class="iconfont icon-fukuan"></i>&nbsp;付款
            </span>
        </div>
        <div class="btn" @click="window.location.href = '<?php echo U('apply');?>?CusPoNo=' + CusPoNo" v-if="wp.Paid === '1' && wo.Checked === '0' && wp.Apply === '0'">
            <i class="iconfont icon-shenqingdan"></i>&nbsp;申请退款
        </div>
        <div class="btn" @click="window.location.href = '<?php echo U('apply');?>?CusPoNo=' + CusPoNo" v-if="wp.Paid === '1' && wo.Checked === '0' && wp.Apply === '1' && wp.Refund === '0'">
            <i class="iconfont icon-shiliangzhinengduixiang"></i>&nbsp;申请退款中
        </div>
        <div class="btn" @click="window.location.href = '<?php echo U('apply');?>?CusPoNo=' + CusPoNo" v-if="wp.Paid === '1' && wo.Checked === '0' && wp.Refund === '1'">
            <i class="iconfont icon-tuikuan"></i>&nbsp;已退款
        </div>
    </div>
</div>

<script>
    new Vue({
        el: '#VueBox',
        data: {
            CusPoNo: '<?php echo ($CusPoNo); ?>',
            wo: eval('(' + '<?php echo ($wo); ?>' + ')'),
            wgo: eval('(' + '<?php echo ($wgo); ?>' + ')'),
            wp: eval('(' + '<?php echo ($wp); ?>' + ')'),
            timeline: eval('(' + '<?php echo ($timeline); ?>' + ')')
        }
    });
</script>

</body>
</html>