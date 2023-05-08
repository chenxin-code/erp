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
<!-- 引入 mescroll 插件 -->
<script src="/erp/res/mescroll/mescroll.min.js"></script>
<link rel="stylesheet" href="/erp/res/mescroll/mescroll.min.css">

<style>
    .mescroll {
        height: auto;
        position: fixed;
        top: 80px;
        bottom: 0;
    }
    .mescroll-totop {
        bottom: 10px;
        z-index: 1;
    }
    /*****************************************************/
    .mint-indicator-wrapper {
        z-index: 2;
    }
    /*****************************************************/
    .pay-more {
        width: 44px;
        height: 44px;
        line-height: 44px;
        text-align: center;
        background-color: #708090;
        border-radius: 50%;
        opacity: 0.8;
        cursor: pointer;
        position: fixed;
        bottom: 31px;
        z-index: 1;
        animation: pay-more-flag-move 1s forwards;
    }
    @keyframes pay-more-flag-move {
        from {
            left: -50px;
        }
        to {
            left: 10px;
        }
    }
    .pay-more .iconfont {
        font-size: 30px;
        color: #fff;
    }
    .pay-more .str {
        width: 60px;
        height: 20px;
        line-height: 20px;
        font-size: 12px;
        text-align: center;
        color: #fff;
        background-color: #708090;
        border-radius: 8px;
        white-space: nowrap;
        transform: scale(0.8);
        transform-origin: left top;
        position: absolute;
        top: 48px;
        left: -2px;
    }
    /*****************************************************/
    .wap0-weborder-detail-fullpage {
        padding-bottom: 50px;
        background-color: #fff;
        overflow-y: auto;
        transition: transform 0.3s;
        position: absolute;
        top: 40px;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 2;
    }
    .wap0-weborder-detail-fullpage .base-info {
        padding: 5px 10px;
    }
    .wap0-weborder-detail-fullpage .base-info .item {
        line-height: 2.5;
        color: #000;
        font-size: 14px;
        text-align: right;
        overflow: hidden;
        position: relative;
    }
    .wap0-weborder-detail-fullpage .base-info .item::after {
        content: '';
        height: 1px;
        background-color: #c8c7cc;
        transform: scaleY(0.3);
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
    }
    .wap0-weborder-detail-fullpage .base-info .item .label {
        margin-right: 50px;
        float: left;
    }
    .wap0-weborder-detail-fullpage .base-info .item .value {
        word-wrap: break-word;
        overflow: hidden;
    }
    .wap0-weborder-detail-fullpage .close-btn {
        width: 100%;
        line-height: 40px;
        color: #fff;
        font-size: 18px;
        text-align: center;
        background-color: #1aad19;
        cursor: pointer;
        position: fixed;
        bottom: 0;
    }
    .wap0-weborder-detail-fullpage .close-btn:active {
        background-color: #179b16;
    }
    /*****************************************************/
    .paid0-order-fullpage {
        padding-bottom: 50px;
        background-color: #fff;
        overflow-y: auto;
        transition: transform 0.3s;
        position: fixed;
        top: 40px;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 2;
    }
    .paid0-order-fullpage .item {
        padding: 12px 15px 12px 50px;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        cursor: pointer;
        position: relative;
    }
    .paid0-order-fullpage .item.dis {
        color: #999;
    }
    .paid0-order-fullpage .item::after {
        content: '';
        height: 1px;
        background-color: #c8c7cc;
        transform: scaleY(0.35);
        position: absolute;
        bottom: 0;
        left: 8px;
        right: 0;
    }
    .paid0-order-fullpage .item:last-child::after {
        left: 0;
    }
    .paid0-order-fullpage .item label {
        width: 16px;
        height: 16px;
        border: 1px solid #979797;
        border-radius: 3px;
        cursor: pointer;
        transform: translateY(-50%);
        position: absolute;
        top: 50%;
        left: 15px;
    }
    .paid0-order-fullpage .item.dis label {
        background-color: #f2f1f1;
    }
    .paid0-order-fullpage .item.selected label:after {
        content: '';
        width: 4px;
        height: 11px;
        border: solid #1a991d;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
        position: absolute;
        right: 5px;
    }
    .paid0-order-fullpage .item.dis::before {
        content: '已超时';
        padding: 0 3px;
        font-size: 13px;
        color: grey;
        border: 1px solid grey;
        border-radius: 5px;
        transform: translateY(-50%) rotate(-25deg);
        position: absolute;
        top: 50%;
        right: 90px;
        z-index: 1;
    }
    .paid0-order-fullpage .item .img-box {
        height: 40px;
    }
    .paid0-order-fullpage .item .img-box > img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        transform: translateY(-50%);
        position: absolute;
        top: 50%;
    }
    .paid0-order-fullpage .item .product-box {
        height: 50px;
        line-height: 50px;
        position: relative;
    }
    .paid0-order-fullpage .item .product-box .img-box {
        margin: 5px 10px 0 0;
        position: absolute;
    }
    .paid0-order-fullpage .item .product-box .img-box > img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    .paid0-order-fullpage .item .product-box .title {
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 95%;
        padding: 0 60px 0 50px;
        box-sizing: border-box;
        text-decoration: none;
        cursor: pointer;
        float: left;
        position: relative;
    }
    .paid0-order-fullpage .item .product-box .cost {
        font-size: 14px;
        position: absolute;
        right: 0;
    }
    .paid0-order-fullpage .item.selected .product-box .cost {
        color: #1aad19;
    }
    .paid0-order-fullpage .bottom-box {
        width: 100%;
        height: 40px;
        line-height: 40px;
        font-size: 17px;
        text-align: center;
        display: flex;
        position: fixed;
        bottom: 0;
        z-index: 2;
    }
    .paid0-order-fullpage .bottom-box::before {
        content: '';
        height: 1px;
        background-color: #c8c7cc;
        transform: scaleY(0.35);
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
    }
    .paid0-order-fullpage .bottom-box .act {
        flex: auto;
        background-color: #fff;
        cursor: pointer;
    }
    .paid0-order-fullpage .bottom-box .act:nth-child(1) {
        margin-left: 15px;
    }
    .paid0-order-fullpage .bottom-box .act > label:nth-child(1) {
        width: 15px;
        height: 15px;
        border: 1px solid #979797;
        border-radius: 3px;
        cursor: pointer;
        float: left;
        position: relative;
        top: 11px;
    }
    .paid0-order-fullpage .bottom-box .act.dis > label:nth-child(1) {
        background-color: #f2f1f1;
    }
    .paid0-order-fullpage .bottom-box .act.checked > label:nth-child(1):after {
        content: '';
        width: 4px;
        height: 10px;
        border: solid #1a991d;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
        position: absolute;
        right: 4px;
    }
    .paid0-order-fullpage .bottom-box .act > label:nth-child(2) {
        margin-left: 5px;
        font-size: 14px;
        cursor: pointer;
        float: left;
    }
    .paid0-order-fullpage .bottom-box .act.dis > label:nth-child(2) {
        color: #999;
    }
    .paid0-order-fullpage .bottom-box .act.checked > label:nth-child(2) {
        color: #1a991d;
    }
    .paid0-order-fullpage .bottom-box .total-cost {
        flex: auto;
        font-size: 14px;
        background-color: #fff;
    }
    .paid0-order-fullpage .bottom-box .save-btn {
        width: 25%;
        font-size: 14px;
        color: #fff;
        background-color: #1aad19;
        cursor: pointer;
    }
    .paid0-order-fullpage .bottom-box .save-btn:active {
        background-color: #179b16;
    }
    .paid0-order-fullpage .bottom-box .close-btn {
        width: 25%;
        font-size: 14px;
        color: #000;
        background-color: #f2f1f1;
        cursor: pointer;
    }
    .paid0-order-fullpage .bottom-box .close-btn:active {
        background-color: #d3d3d3;
    }
</style>

<div id="VueBox">
    <wap0-header
            flag="<?php echo ($HeaderFlag); ?>"
            index_url="<?php echo U('Index1/index');?>"
            menu_url="<?php echo U('Index/menu');?>"
            logout_url="<?php echo U('Index/logout_api');?>"
            use_board_group="<?php echo ($config['UseBoardGroup']); ?>"
            use_box_group="<?php echo ($config['UseBoxGroup']); ?>"
            open_80port="<?php echo ($config['Open80Port']); ?>"
            use_wxpay="<?php echo ($config['UseWxPay']); ?>"
            server_name="<?php echo ($_SERVER['SERVER_NAME']); ?>"
            frp_80port_domain="<?php echo ($config['Frp80PortDomain']); ?>"
            logout_ori_url="<?php echo str_replace($config['OriDomain'],$config['OriDomain'].':'.$config['OriPort'],U('Index/logout_api@'.$config['OriDomain']));?>"
            logout_frp_80port_url="<?php echo U('Index/logout_api@'.$config['Frp80PortDomain']);?>">
    </wap0-header>
    <div class="tab-filter-box">
        <div class="tab">
            <div :class="{'active':State === '1'}" @click="changeTab('1')">
                <i class="iconfont icon-daishenhe"></i>&nbsp;未审核
            </div>
            <div :class="{'active':State === '2'}" @click="changeTab('2')">
                <i class="iconfont icon-shenhetongguo1"></i>&nbsp;已审核
            </div>
            <div :class="{'active':State === '3'}" @click="changeTab('3')">
                <i class="iconfont icon-laji"></i>&nbsp;已删除
            </div>
        </div>
        <div class="separator"></div>
        <div class="filter" @click="OpenSlider()">
            <i class="iconfont icon-shaixuan"></i>&nbsp;筛选
        </div>
    </div>
    <div id="mescroll" class="mescroll">
        <div id="lists">
            <div :class="'common-item ' + (k%2?'bgc1':'bgc2')" v-for="(v,k) in lists">
                <div class="product-box" v-if="v.IsGroup === '1'">
                    <div class="img-box">
                        <img :src="'/erp/res/' + v.FirstPic">
                    </div>
                    <div class="title" @click="window.location.href = '<?php echo U('Group/Board/detail');?>?Id=' + v.WebProductId" v-if="v.BoardId && !v.MatNo">
                        <span style="color: #e01835;">{{v.BoardId}}</span><span v-if="v.Title">,{{v.Title}}</span>
                    </div>
                    <div class="title" @click="window.location.href = '<?php echo U('Group/Box/detail');?>?Id=' + v.WebProductId" v-else-if="!v.BoardId && v.MatNo">
                        <span style="color: #e01835;">{{v.MatNo}}</span><span v-if="v.Title">,{{v.Title}}</span>
                    </div>
                    <div class="status" style="color: #ffa500;" @click="window.location.href = '<?php echo U('Pay/Order/detail');?>?CusPoNo=' + v.CusPoNo" v-if="v.UsePay === '1' && v.Paid === '0'"><span v-if="v.PayDeadlineTime < time">超时</span>未付款</div>
                    <div class="status" style="color: #1a991d;" @click="window.location.href = '<?php echo U('Pay/Order/detail');?>?CusPoNo=' + v.CusPoNo" v-if="v.UsePay === '1' && v.Paid === '1' && v.Apply === '0' && v.Refund === '0'">已付款</div>
                    <div class="status" style="color: red;" @click="window.location.href = '<?php echo U('Pay/Order/detail');?>?CusPoNo=' + v.CusPoNo" v-if="v.UsePay === '1' && v.Paid === '1' && v.Checked === '0' && v.Apply === '1' && v.Refund === '0'">申请退款中</div>
                    <div class="status" style="color: #666;" @click="window.location.href = '<?php echo U('Pay/Order/detail');?>?CusPoNo=' + v.CusPoNo" v-if="v.UsePay === '1' && v.Paid === '1' && ((v.Checked === '0' && v.Apply === '1') || (v.Checked === '1' && v.Apply === '0')) && v.Refund === '1'">已退款</div>
                </div>
                <div class="pane-box">
                    <div class="pane" style="color: #3598dc;border-color: #3598dc;">{{v.CTypeName}}</div>
                    <div class="pane" style="color: #ff4500;border-color: #ff4500;" v-if="v.IsCard === '1'">常用订单标识：{{v.CardFlag}}</div>
                    <div class="pane" style="color: red;border-color: red;" v-if="v.IsDel === '1'">删除原因：{{v.DelRemark}}</div>
                </div>
                <div class="info-box">
                    <div class="flex-box">
                        <div>客订单号：<span>{{v.CusPoNo}}</span></div>
                        <div v-if="v.CType === 's' || v.CType === 'c'">材质：<span>{{v.BoardId}}</span></div>
                        <div v-else-if="v.CType === 'x'">套件：<span>{{v.ProductId}}</span></div>
                        <div v-else-if="v.CType === 't'">货品编号：<span>{{v.MatNo}}</span></div>
                    </div>
                    <div class="flex-box" v-if="v.CType === 'c'">
                        <div>箱型：<span>{{v.BoxName}}</span></div>
                    </div>
                    <div class="flex-box" v-if="v.CType === 'c'">
                        <div>纸箱规格(mm)：<span>{{v.BoxL}}&nbsp;x&nbsp;{{v.BoxW}}&nbsp;x&nbsp;{{v.BoxH}}</span></div>
                    </div>
                    <div class="flex-box" v-if="v.CType === 's' || v.CType === 'c'">
                        <div>纸板规格(mm)：<span>{{v.Length}}&nbsp;x&nbsp;{{v.Width}}</span></div>
                    </div>
                    <div class="flex-box" v-if="v.CType === 'x'">
                        <div>套件：<span>{{v.ProductName}}</span></div>
                    </div>
                    <div class="flex-box">
                        <div>订单数：<span>{{v.OrdQty}}</span></div>
                        <div v-if="v.CType === 's' || v.CType === 'c'">下单面积(㎡)：<span>{{v.Area}}</span></div>
                    </div>
                    <div class="flex-box">
                        <div>送货地址：<span>{{v.SubDNAddress}}</span></div>
                    </div>
                    <div class="flex-box">
                        <div>下单日期：<span>{{v.BuildDate}}</span></div>
                        <div>交货日期：<span>{{v.DeliveryDate}}</span></div>
                    </div>
                </div>
                <div class="btn-box">
                    <div class="btn s3" @click="used(v.CusPoNo)" v-if="v.Checked === '1' && v.IsCard === '0' && v.CType !== 't'">设为常用</div>
                    <div class="btn s2" @click="FastBuild(v.CusPoNo,v.CType)" v-if="v.Checked === '1' && v.IsCard === '1'">快速下单</div>
                    <div class="btn s4" @click="unused(v.CusPoNo)" v-if="v.Checked === '1' && v.IsCard === '1' && v.CType !== 't'">取消常用</div>
                    <div class="btn s7" @click="delete1(v.CusPoNo)" v-if="v.IsDel === '0' && v.Checked === '0' && ( v.IsGroup === '0' || v.UsePay === '0' || v.Paid === '0' || v.Refund === '1' )">删除</div>
                    <div class="btn s1" @click="detail(v.CusPoNo)">详情</div>
                </div>
            </div>
        </div>
    </div>
    <transition name="fullpage">
        <div class="wap0-weborder-detail-fullpage" v-if="showDetail">
            <div v-if="ProductInfo">
                <div class="product-card" @click="window.location.href = '<?php echo U('Group/Board/detail');?>?Id=' + ProductInfo.WebProductId" v-if="ProductInfo.BoardId && !ProductInfo.MatNo">
                    <div class="item1">
                        <img :src="'/erp/res/' + ProductInfo.FirstPic">
                    </div>
                    <div class="item2">
                        <div class="title">
                            <span style="color: #e01835;">{{ProductInfo.BoardId}}</span><span v-if="ProductInfo.Title">,{{ProductInfo.Title}}</span>
                        </div>
                        <div class="descr">
                            下单价格&nbsp;<span style="color: #e01835;">¥{{ProductInfo.Price}}/㎡</span>&nbsp;<span style="color: #999;text-decoration: line-through;">¥{{ProductInfo.MarketPrice}}/㎡</span>
                        </div>
                        <div class="descr">
                            下单金额&nbsp;<span style="color: #e01835;">¥{{ProductInfo.Cost}}</span>
                        </div>
                        <div class="descr">
                            节省金额&nbsp;<span style="color: #e01835;">¥{{ProductInfo.SaveCost}}</span>
                        </div>
                    </div>
                </div>
                <div class="product-card" @click="window.location.href = '<?php echo U('Group/Box/detail');?>?Id=' + ProductInfo.WebProductId" v-else-if="!ProductInfo.BoardId && ProductInfo.MatNo">
                    <div class="item1">
                        <img :src="'/erp/res/' + ProductInfo.FirstPic">
                    </div>
                    <div class="item2">
                        <div class="title">
                            <span style="color: #e01835;">{{ProductInfo.MatNo}}</span><span v-if="ProductInfo.Title">,{{ProductInfo.Title}}</span>
                        </div>
                        <div class="descr">
                            下单价格&nbsp;<span style="color: #e01835;">¥{{ProductInfo.Price}}/个</span>&nbsp;<span style="color: #999;text-decoration: line-through;">¥{{ProductInfo.MarketPrice}}/个</span>
                        </div>
                        <div class="descr">
                            下单金额&nbsp;<span style="color: #e01835;">¥{{ProductInfo.Cost}}</span>
                        </div>
                        <div class="descr">
                            节省金额&nbsp;<span style="color: #e01835;">¥{{ProductInfo.SaveCost}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="base-info" v-if="BaseInfo">
                <div class="item">
                    <div class="label">订单类型</div>
                    <div class="value">{{BaseInfo.CTypeName}}</div>
                </div>
                <div class="item">
                    <div class="label">客订单号</div>
                    <div class="value">{{BaseInfo.CusPoNo}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 's' || BaseInfo.CType === 'c'">
                    <div class="label">材质</div>
                    <div class="value">{{BaseInfo.BoardId}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 'c'">
                    <div class="label">箱型</div>
                    <div class="value">{{BaseInfo.BoxName}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 'c'">
                    <div class="label">箱长(mm)</div>
                    <div class="value">{{BaseInfo.BoxL}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 'c'">
                    <div class="label">箱宽(mm)</div>
                    <div class="value">{{BaseInfo.BoxW}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 'c'">
                    <div class="label">箱高(mm)</div>
                    <div class="value">{{BaseInfo.BoxH}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 'c'">
                    <div class="label">箱舌(mm)</div>
                    <div class="value">{{BaseInfo.TonLen}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 'c'">
                    <div class="label">封箱调整(mm)</div>
                    <div class="value">{{BaseInfo.ULen}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 's' || BaseInfo.CType === 'c'">
                    <div class="label">板长(mm)</div>
                    <div class="value">{{BaseInfo.Length}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 's' || BaseInfo.CType === 'c'">
                    <div class="label">板宽(mm)</div>
                    <div class="value">{{BaseInfo.Width}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 's'">
                    <div class="label">压线名称</div>
                    <div class="value">{{BaseInfo.ScoreName}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 's'">
                    <div class="label">压线信息</div>
                    <div class="value">{{BaseInfo.ScoreInfo}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 'x' || BaseInfo.CType === 't'">
                    <div class="label">PO号</div>
                    <div class="value">{{BaseInfo.PON}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 'x'">
                    <div class="label">套件</div>
                    <div class="value">{{BaseInfo.ProductId}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 't'">
                    <div class="label">货品编号</div>
                    <div class="value">{{BaseInfo.MatNo}}</div>
                </div>
                <div class="item">
                    <div class="label">订单数</div>
                    <div class="value">{{BaseInfo.OrdQty}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 'c'">
                    <div class="label">张数</div>
                    <div class="value">{{BaseInfo.BdMultiple}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 'c'">
                    <div class="label">纸板数</div>
                    <div class="value">{{BaseInfo.BdQty}}</div>
                </div>
                <div class="item" v-if="BaseInfo.CType === 's' || BaseInfo.CType === 'c'">
                    <div class="label">下单面积(㎡)</div>
                    <div class="value">{{BaseInfo.Area}}</div>
                </div>
                <div class="item">
                    <div class="label">送货地址</div>
                    <div class="value">{{BaseInfo.SubDNAddress}}</div>
                </div>
                <div class="item">
                    <div class="label">交货日期</div>
                    <div class="value">{{BaseInfo.DeliveryDate}}</div>
                </div>
                <div class="item">
                    <div class="label">送货备注</div>
                    <div class="value">{{BaseInfo.DNRemark}}</div>
                </div>
                <div class="item">
                    <div class="label">生产备注</div>
                    <div class="value">{{BaseInfo.ProRemark}}</div>
                </div>
                <div class="item">
                    <div class="label">下单时间</div>
                    <div class="value">{{BaseInfo.BuildTime}}</div>
                </div>
            </div>
            <div class="close-btn" @click="showDetail = false">关闭</div>
        </div>
    </transition>
    <div class="right-aside" :class="{'push':BarVisible}">
        <div class="shadow" @click="CloseSlider(false)"></div>
        <div class="form">
            <div class="rows">
                <div class="row2">
                    <div class="title">订单类型</div>
                    <label :class="{'checked':form.CType === ''}">
                        全部<input type="radio" value="" v-model="form.CType" hidden>
                    </label>
                    <label :class="{'checked':form.CType === 's'}">
                        简单纸板<input type="radio" value="s" v-model="form.CType" hidden>
                    </label>
                    <label :class="{'checked':form.CType === 'c'}">
                        纸箱纸板<input type="radio" value="c" v-model="form.CType" hidden>
                    </label>
                    <label :class="{'checked':form.CType === 'x'}" v-if="form.IsGroup !== '1'">
                        纸箱<input type="radio" value="x" v-model="form.CType" hidden>
                    </label>
                    <label :class="{'checked':form.CType === 't'}" v-if="form.IsGroup !== '0'">
                        淘宝箱<input type="radio" value="t" v-model="form.CType" hidden>
                    </label>
                </div>
                <div class="row2">
                    <div class="title">是否团购</div>
                    <label :class="{'checked':form.IsGroup === ''}">
                        全部<input type="radio" value="" v-model="form.IsGroup" hidden>
                    </label>
                    <label :class="{'checked':form.IsGroup === '1'}">
                        是<input type="radio" value="1" v-model="form.IsGroup" hidden>
                    </label>
                    <label :class="{'checked':form.IsGroup === '0'}">
                        否<input type="radio" value="0" v-model="form.IsGroup" hidden>
                    </label>
                </div>
                <div class="row2">
                    <div class="title">日期类型</div>
                    <label :class="{'checked':form.DateType === 'BuildDate'}">
                        下单日期<input type="radio" value="BuildDate" v-model="form.DateType" hidden>
                    </label>
                    <label :class="{'checked':form.DateType === 'DeliveryDate'}">
                        交货日期<input type="radio" value="DeliveryDate" v-model="form.DateType" hidden>
                    </label>
                </div>
                <div class="row1">
                    <div class="title">开始日期</div>
                    <div class="input" @click="$refs.BeginDate.open()">{{datetimeFormat(form.BeginDate,'yyyy-MM-dd')}}</div>
                </div>
                <div class="row1">
                    <div class="title">结束日期</div>
                    <div class="input" @click="$refs.EndDate.open()">{{datetimeFormat(form.EndDate,'yyyy-MM-dd')}}</div>
                </div>
                <div class="row3">
                    <label for="rememberForm" :class="{'checked':form.rememberForm === 'yes'}"></label>
                    <label for="rememberForm">记住筛选条件(本次登录有效)</label>
                    <input type="checkbox" id="rememberForm" v-model="form.rememberForm" true-value="yes" false-value="no" hidden>
                </div>
                <!--<div style="height:1500px;background-color:#1aad19;border:5px solid #ff5000;"></div>-->
            </div>
            <div class="footer">
                <div class="reset" @click="ResetForm()">重置</div>
                <div class="confirm" @click="CloseSlider(true)">确定</div>
            </div>
        </div>
    </div>
    <mt-datetime-picker type="date" :start-date="MinDate" :end-date="MaxDate" v-model="form.BeginDate" ref="BeginDate"></mt-datetime-picker>
    <mt-datetime-picker type="date" :start-date="MinDate" :end-date="MaxDate" v-model="form.EndDate" ref="EndDate"></mt-datetime-picker>
    <div class="pay-more" @click="CheckedPaid0Order = [];showPaid0Order = true" v-if="!$.isEmptyObject(Paid0OrderSelect)">
        <i class="iconfont icon-fukuan"></i>
        <span class="str">批量付款</span>
    </div>
    <transition name="fullpage2">
        <div class="paid0-order-fullpage" v-if="showPaid0Order">
            <div>
                <div class="item" :class="{'dis':v.PayDeadlineTime < time,'selected':CheckedPaid0Order.indexOf(v) !== -1}" @click="ps(v)" v-for="v in Paid0OrderSelect">
                    <label></label>
                    <div class="product-box">
                        <div class="img-box">
                            <img :src="'/erp/res/' + v.FirstPic">
                        </div>
                        <div class="title">
                            <span style="color: #e01835;">{{v.BoardId || v.MatNo}}</span><span v-if="v.Title">,{{v.Title}}</span>
                        </div>
                        <div class="cost">¥{{v.Cost}}</div>
                    </div>
                </div>
            </div>
            <div class="bottom-box">
                <div class="act" :class="{'dis':Paid0OrderSelect_.length === 0,'checked':Paid0OrderAllSelect}" @click="AllSelectAction()">
                    <label></label>
                    <label>全选</label>
                </div>
                <div class="total-cost">
                    合计：<span style="color: #1aad19;">¥{{totalCost}}</span>
                </div>
                <div class="save-btn" @click="totalCusPoNo === ''?$.toast('请选择要付款的订单','text'):window.location.href = '<?php echo U('Pay/Order/way');?>?CusPoNo=' + totalCusPoNo">批量付款({{CheckedPaid0Order.length}})</div>
                <div class="close-btn" @click="showPaid0Order = false">关闭</div>
            </div>
        </div>
    </transition>
</div>

<script>
    new Vue({
        el: '#VueBox',
        data: {
            State: '<?php echo ($rememberTab); ?>'?'<?php echo ($rememberTab); ?>':'1',
            form: {
                CType: '',
                IsGroup: '',
                DateType: 'BuildDate',
                BeginDate: '<?php echo date('Y-m-d',strtotime($config['WeborderBeginDate']));?>',
                EndDate: '<?php echo date('Y-m-d',strtotime($config['WeborderEndDate']));?>',
                rememberForm: 'no'
            },
            MinDate: new Date('<?php echo date('Y-m-d',strtotime($config['WeborderMinDate']));?>'),
            MaxDate: new Date('<?php echo date('Y-m-d',strtotime($config['WeborderMaxDate']));?>'),
            CardFlag: '',
            DelRemark: '',
            WeborderDefaultDelRemark: eval('(' + '<?php echo ($WeborderDefaultDelRemark); ?>' + ')'),
            //打开侧边栏前的form对象
            BeforeOpenSliderForm: {},
            BarVisible: false,
            tempTop: 0,
            mescroll: null,
            lists: [],
            //详情
            showDetail: false,
            BaseInfo: null,
            ProductInfo: null,
            //批量付款
            showPaid0Order: false,
            Paid0OrderSelect: null,
            Paid0OrderSelect_: null,
            CheckedPaid0Order: null,
            Paid0OrderAllSelect: Boolean,
            totalCusPoNo: null,
            totalCost: null,
            //lists_api接口请求时间
            time: null
        },
        methods: {
            upCallback: function(page) {
                var _this = this;
                _this.search(page.num, page.size, function (respon) {
                    //如果是第一页需手动制空列表 (代替clearId和clearEmptyId的配置)
                    if(page.num === 1){
                        _this.lists = [];
                    }
                    _this.lists = _this.lists.concat(respon.data);
                    //_this.mescroll.endSuccess(respon.data.length);
                    _this.mescroll.endBySize(respon.data.length, respon.count);
                    _this.Paid0OrderSelect = respon.Paid0Order;
                    _this.time = respon.time;
                    _this.Paid0OrderSelect_ = [];
                    $.each(_this.Paid0OrderSelect,function (k,v) {
                        if(v.PayDeadlineTime >= _this.time){
                            _this.Paid0OrderSelect_[k] = v;
                        }
                    });
                }, function () {
                    _this.mescroll.endErr();
                });
            },
            search: function (CurPage,PageSize,successCallback,errorCallback) {
                var _this = this;
                _this.form.BeginDate = datetimeFormat(_this.form.BeginDate,'yyyy-MM-dd');
                _this.form.EndDate = datetimeFormat(_this.form.EndDate,'yyyy-MM-dd');
                $.ajax({
                    url: '<?php echo U('lists_api');?>',
                    type: 'get',
                    data: {
                        CurPage: CurPage,
                        PageSize: PageSize,
                        State: _this.State,
                        form: _this.form
                    },
                    success: function (respon) {
                        var respon = eval('(' + respon + ')');
                        if(respon.ret === '<?php echo C('succ_ret');?>'){
                            successCallback && successCallback(respon);
                        }else{
                            _this.lists = [];
                            $.toast(respon.msg,'forbidden');
                            errorCallback();
                        }
                    },
                    error: errorCallback
                });
            },
            ResetLists: function () {
                this.lists = [];
                this.mescroll.resetUpScroll();
                this.mescroll.hideTopBtn();
            },
            changeTab: function (value) {
                if(this.State !== value){
                    this.State = value;
                    this.ResetLists();
                }
            },
            used: function (CusPoNo) {
                var _this = this;
                $.prompt({
                    title: '请为这个常用订单添加标识',
                    onOK: function(value) {
                        _this.CardFlag = value;
                        $.ajax({
                            url: '<?php echo U('used_api');?>',
                            type: 'get',
                            data: {
                                CusPoNo: CusPoNo,
                                CardFlag: _this.CardFlag
                            },
                            beforeSend: function () {
                                _this.$indicator.open();
                            },
                            success: function (respon) {
                                _this.$indicator.close();
                                var respon = eval('(' + respon + ')');
                                if(respon.ret === '<?php echo C('succ_ret');?>'){
                                    $.alert(respon.msg,'',function () {
                                        _this.ResetLists();
                                    });
                                }else{
                                    $.toast(respon.msg,'forbidden');
                                }
                            }
                        });
                    }
                });
            },
            unused: function (CusPoNo){
                var _this = this;
                $.confirm('确认取消？','',function () {
                    $.ajax({
                        url: '<?php echo U('Usedorder/cancel_api');?>',
                        type: 'get',
                        data: {CusPoNo: CusPoNo},
                        beforeSend: function () {
                            _this.$indicator.open();
                        },
                        success: function (respon) {
                            _this.$indicator.close();
                            var respon = eval('(' + respon + ')');
                            if(respon.ret === '<?php echo C('succ_ret');?>'){
                                $.alert(respon.msg,'',function () {
                                    _this.ResetLists();
                                });
                            }else{
                                $.toast(respon.msg,'forbidden');
                            }
                        }
                    });
                });
            },
            FastBuild: function (CusPoNo,CType) {
                if(CType === 's'){
                    window.location.href = '<?php echo U('Build/s');?>?CusPoNo=' + CusPoNo;
                }else if(CType === 'c'){
                    window.location.href = '<?php echo U('Build/c');?>?CusPoNo=' + CusPoNo;
                }else if(CType === 'x'){
                    window.location.href = '<?php echo U('Build/x');?>?CusPoNo=' + CusPoNo;
                }
            },
            delete1: function (CusPoNo) {
                var _this = this;
                $.each(_this.WeborderDefaultDelRemark,function (k,v) {
                    v.text = v.DelRemark;
                    v.className = 'color-warning';
                    v.onClick = function () {
                        _this.DelRemark = v.DelRemark;
                        _this.delete2(CusPoNo);
                    }
                });
                $.actions({
                    title: '请选择删除原因',
                    actions: _this.WeborderDefaultDelRemark.concat([
                        {
                            text: '其他原因',
                            className: 'color-primary',
                            onClick: function () {
                                $.prompt({
                                    title: '请填写原因',
                                    onOK: function (value) {
                                        _this.DelRemark = value;
                                        _this.delete2(CusPoNo);
                                    }
                                });
                            }
                        }
                    ])
                });
            },
            delete2: function (CusPoNo) {
                var _this = this;
                $.ajax({
                    url: '<?php echo U('delete_api');?>',
                    type: 'get',
                    data: {
                        CusPoNo: CusPoNo,
                        DelRemark:_this.DelRemark
                    },
                    beforeSend: function () {
                        _this.$indicator.open();
                    },
                    success: function (respon) {
                        _this.$indicator.close();
                        var respon = eval('(' + respon + ')');
                        if(respon.ret === '<?php echo C('succ_ret');?>'){
                            $.alert(respon.msg,'', function () {
                                _this.ResetLists();
                            });
                        }else{
                            $.toast(respon.msg,'forbidden');
                        }
                    }
                });
            },
            detail: function (CusPoNo) {
                var _this = this;
                _this.BaseInfo = null;
                _this.ProductInfo = null;
                $.ajax({
                    url: '<?php echo U('detail_api');?>',
                    type: 'get',
                    data: {CusPoNo: CusPoNo},
                    beforeSend: function () {
                        _this.$indicator.open();
                    },
                    success: function (respon1) {
                        _this.BaseInfo = eval('(' + respon1 + ')');
                        $.ajax({
                            url: '<?php echo U('detail2_api');?>',
                            type: 'get',
                            data: {CusPoNo: CusPoNo},
                            success: function (respon2) {
                                _this.$indicator.close();
                                var respon2 = eval('(' + respon2 + ')');
                                if(respon2.ret === '<?php echo C('succ_ret');?>'){
                                    _this.ProductInfo = respon2.data;
                                }
                                _this.showDetail = true;
                            }
                        });
                    }
                });
            },
            ResetForm: function () {
                this.form = Object.assign({},this.BeforeOpenSliderForm);
            },
            OpenSlider: function () {
                this.BarVisible = true;
                this.tempTop = document.scrollingElement.scrollTop;
                document.body.classList.add('body-lock');
                document.body.style.top = -this.tempTop + 'px';
                this.BeforeOpenSliderForm = Object.assign({},this.form);
            },
            CloseSlider: function (IsClickConfirm) {
                this.BarVisible = false;
                document.body.classList.remove('body-lock');
                document.scrollingElement.scrollTop = this.tempTop;
                if(IsClickConfirm
                    //&& !checkObjectIsEqual(this.form,this.BeforeOpenSliderForm)
                ){
                    this.ResetLists();
                }else{
                    this.ResetForm();
                }
            },
            ps: function (v) {
                if(v.PayDeadlineTime < this.time){
                    return;
                }
                var index = this.CheckedPaid0Order.indexOf(v);
                if(index === -1){
                    this.CheckedPaid0Order.push(v);
                }else{
                    this.CheckedPaid0Order.splice(index,1);
                }
            },
            AllSelectAction: function () {
                if(this.Paid0OrderSelect_.length === 0){
                    return;
                }
                this.CheckedPaid0Order = this.CheckedPaid0Order.sort().toString() === this.Paid0OrderSelect_.sort().toString()?[]:[].concat(this.Paid0OrderSelect_);
            }
        },
        mounted: function () {
            var _this = this;
            _this.form = Object.assign({},_this.form,eval('(' + '<?php echo ($rememberForm); ?>' + ')'));
            _this.mescroll = new MeScroll('mescroll',{
                up: {
                    callback: _this.upCallback, //上拉回调
                    isBounce: false, //此处禁止ios回弹,如果您的项目是在iOS的微信,QQ,Safari等浏览器访问的,建议配置此项
                    noMoreSize: 0, //如果列表已无数据,可设置列表的总数量要大于0条才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看
                    page: {size: 7},
                    toTop: {src: '/erp/res/totop.png'},
                    htmlNodata: '<p class="upwarp-nodata">-- 没有更多订单了 --</p>',
                    //vue的案例请勿配置clearId和clearEmptyId,否则列表的数据模板会被清空
                    //clearEmptyId: 'lists', //1.下拉刷新时会自动先清空此列表,再加入数据; 2.无任何数据时会在此列表自动提示空
                    empty: {
                        warpId: 'lists',
                        icon: '/erp/res/empty.jpg',
                        tip: '没有找到相关订单'
                    }
                }
            });
            //初始化vue后,显示vue模板布局
            //document.getElementById('lists').style.display = 'block';
        },
        watch: {
            CheckedPaid0Order: function () {
                this.Paid0OrderAllSelect = (this.Paid0OrderSelect_.length === 0?false:(this.CheckedPaid0Order.length === this.Paid0OrderSelect_.length));
                var temp1 = '',temp2 = 0;
                $.each(this.CheckedPaid0Order,function (k,v) {
                    temp1 += v.CusPoNo + ',';
                    temp2 += parseFloat(v.Cost);
                });
                this.totalCusPoNo = temp1.substring(0,temp1.length - 1);
                this.totalCost = temp2.toFixed(2);
            }
        }
    });
</script>

</body>
</html>