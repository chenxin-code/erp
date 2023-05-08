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
<div id="VueBox">
    <group-header flag="<?php echo ($HeaderFlag); ?>" index_url="<?php echo U('Wap0/Index1/index');?>" menu_url="<?php echo U('Wap0/Index/menu');?>"></group-header>
    <div class="product-card" style="margin-top: 40px;" @click="window.location.href = '<?php echo U('Board/detail');?>?Id=' + ProductInfo.Id">
        <div class="item1">
            <img :src="'/erp/res/' + ProductInfo.Pic[0]" v-if="ProductInfo.Pic[0]">
        </div>
        <div class="item2">
            <div class="title">
                <span style="color: #e01835;">{{ProductInfo.BoardId}}</span><span v-if="ProductInfo.Title">,{{ProductInfo.Title}}</span>
            </div>
            <vue2-countdown
                    :start-time="ProductInfo.BeginTime"
                    :end-time="ProductInfo.EndTime"
                    :current-time="'<?php echo time();?>'"
                    :tip-text="'距团购开始'"
                    :tip-text-end="'距团购结束'"
                    :end-text="'团购已结束'"
                    v-on:end_callback="window.location.href = '<?php echo U('Board/detail');?>?Id=' + ProductInfo.Id"
                    style="font-size: 13px;">
            </vue2-countdown>
        </div>
        <div class="item3">
            <div style="color: #e01835;">¥{{ProductInfo.Price}}/㎡</div>
            <div style="color: #999;text-decoration: line-through;">¥{{ProductInfo.MarketPrice}}/㎡</div>
        </div>
    </div>
    <cell-group>
        <field label="客订单号" placeholder="未填写则系统自动生成" v-model="form.CusPoNo"></field>
        <cell title="纸板规格(mm)" @icon-click="$.alert('板长范围：' + MinLength + 'mm&nbsp;~&nbsp;' + MaxLength + 'mm<br>板宽范围：' + MinWidth + 'mm&nbsp;~&nbsp;' + MaxWidth + 'mm','')" help>
            <div class="slot">
                <input type="number" placeholder="板长" v-model="form.Length" @focus="disBtn = true" @blur="calcAreaCost()">
                x
                <input type="number" placeholder="板宽" v-model="form.Width" @focus="disBtn = true" @blur="calcAreaCost()">
            </div>
        </cell>
        <cell title="压线名称" placeholder="选择压线名称" @click="showScoreNameSelect = true" is-link>
            <span v-if="form.ScoreName">{{form.ScoreName}}</span>
        </cell>
        <field label="压线信息" placeholder="压线和=板宽（格式：x+x+x）" v-model="form.ScoreInfo"></field>
        <field label="订单数" type="number" placeholder="输入订单数" v-model="form.OrdQty" ref="OrdQty" @focus="disBtn = true" @blur="calcAreaCost()"></field>
        <cell title="下单面积(㎡)" placeholder="待计算" @icon-click="$.alert('下单面积范围：' + Number(ProductInfo.BuildMin) + '㎡&nbsp;~&nbsp;' + Number(ProductInfo.BuildMax) + '㎡','')" help>
            <span v-if="Area">{{Area}}</span>
        </cell>
        <cell title="送货公司" placeholder="选择送货公司" @click="showCustomerDNSelect = true" is-link>
            <span v-if="form.CusSubNo">{{form.CusSubNo}}</span>
        </cell>
        <cell title="交货日期" placeholder="选择交货日期" @click="$refs.DeliveryDate.open()" is-link>
            {{datetimeFormat(form.DeliveryDate,'yyyy-MM-dd')}}
        </cell>
        <field label="送货备注" placeholder="填写送货备注" rows="1" type="textarea" v-model="form.DNRemark" autosize></field>
        <field label="生产备注" placeholder="填写生产备注" rows="1" type="textarea" v-model="form.ProRemark" autosize></field>
    </cell-group>
    <mt-datetime-picker type="date" :start-date="MinDate" :end-date="MaxDate" v-model="form.DeliveryDate" ref="DeliveryDate"></mt-datetime-picker>
    <div class="group-build-footer2" v-if="IsRangePrice">
        当前价格&nbsp;<span style="color: #e01835;">¥{{Price}}/㎡</span>
        <span class="pane" v-if="$.isEmptyObject(help)">最低价</span>
        <div class="help" v-for="v in help">
            订单数满&nbsp;<span style="color: #4b0;text-decoration: underline;cursor: pointer;" @click="form.OrdQty = getOrdQtyByBdQty(v.BdQty);$refs.OrdQty.focus()">{{getOrdQtyByBdQty(v.BdQty)}}</span>
            价格减至&nbsp;<span style="color: #e01835;">¥{{v.Price}}/㎡</span>
        </div>
    </div>
    <div class="group-build-footer1">
        <div class="cost">
            <span v-if="validArea">下单金额：<span style="color: #e01835;">¥{{Cost}}</span></span>
            <span v-if="validArea">节省金额：<span style="color: #e01835;">¥{{SaveCost}}</span></span>
        </div>
        <div class="btn" @click="bcheck()">下单</div>
    </div>
    <transition name="fullpage">
        <div class="diy-select-fullpage" v-if="showScoreNameSelect">
            <div class="empty" v-if="$.isEmptyObject(ScoreNameSelect)">
                <img src="/erp/res/empty.jpg">
                <p>没有可选择项</p>
            </div>
            <div v-else>
                <div class="item" :class="{'selected':v === form.ScoreName_}" @click="form.ScoreName_ = v" v-for="v in ScoreNameSelect">
                    <div class="content">
                        <span>{{v}}</span>
                    </div>
                    <label></label>
                </div>
            </div>
            <div class="confirm-btn" @click="form.ScoreName = form.ScoreName_;showScoreNameSelect = false">确定</div>
        </div>
        <div class="diy-select-fullpage" v-if="showCustomerDNSelect">
            <div class="empty" v-if="$.isEmptyObject(CustomerDNSelect)">
                <img src="/erp/res/empty.jpg">
                <p>没有可选择项</p>
            </div>
            <div v-else>
                <div class="item" :class="{'selected':v.CusSubNo === form.CusSubNo_}" @click="form.CusSubNo_ = v.CusSubNo" v-for="v in CustomerDNSelect">
                    <div class="content">
                        <span>{{v.CusSubNo}}</span>
                        <span v-if="v.SubDNAddress">送货地址：{{v.SubDNAddress}}</span>
                    </div>
                    <label></label>
                </div>
            </div>
            <div class="confirm-btn" @click="form.CusSubNo = form.CusSubNo_;showCustomerDNSelect = false">确定</div>
        </div>
    </transition>
    <confirm-build-info
            group
            build_type="s"
            :form="form"
            :Area="Area"
            :cost.sync="Cost"
            :save-cost.sync="SaveCost"
            :show-confirm-build-info.sync="showConfirmBuildInfo"
            @build="build()">
    </confirm-build-info>
    <transition name="fullpage">
        <build-result-fullpage
                :result="result"
                :msg="msg"
                :pay_url="pay_url"
                weborder_url="<?php echo U('Wap0/Weborder/lists');?>"
                :show-build-result.sync="showBuildResult"
                v-if="showBuildResult">
        </build-result-fullpage>
    </transition>
</div>

<script>
    new Vue({
        el: '#VueBox',
        data: {
            //存放初始化数据
            form_ini: {
                CusPoNo: null,
                Length: null,
                Width: null,
                ScoreName: eval('(' + '<?php echo ($ScoreNameSelect); ?>' + ')')[0],
                ScoreName_: eval('(' + '<?php echo ($ScoreNameSelect); ?>' + ')')[0],
                ScoreInfo: null,
                OrdQty: null,
                CusSubNo: '<?php echo ($ERPId); ?>',
                CusSubNo_: '<?php echo ($ERPId); ?>',
                DeliveryDate: '<?php echo date('Y-m-d',strtotime($config['BuildDeliveryDate']));?>',
                DNRemark: null,
                ProRemark: null
            },
            //实际数据
            form: {},
            disBtn: false,//禁用下单按钮
            MinDate: new Date('<?php echo date('Y-m-d',strtotime($config['BuildMinDate']));?>'),
            MaxDate: new Date('<?php echo date('Y-m-d',strtotime($config['BuildMaxDate']));?>'),
            MinLength: Number('<?php echo ($config['BuildMinLength']); ?>'),
            MaxLength: Number('<?php echo ($config['BuildMaxLength']); ?>'),
            MinWidth: Number('<?php echo ($config['BuildMinWidth']); ?>'),
            MaxWidth: Number('<?php echo ($config['BuildMaxWidth']); ?>'),
            ProductInfo: eval('(' + '<?php echo ($ProductInfo); ?>' + ')'),
            ScoreNameSelect: eval('(' + '<?php echo ($ScoreNameSelect); ?>' + ')'),
            CustomerDNSelect: eval('(' + '<?php echo ($CustomerDNSelect); ?>' + ')'),
            showScoreNameSelect: false,
            showCustomerDNSelect: false,
            Area: null,
            validArea: false,
            Cost: null,
            SaveCost: null,
            IsRangePrice: false,
            Price: null,
            help: null,
            //确认下单信息
            showConfirmBuildInfo: false,
            //下单结果
            result: Boolean,
            msg: String,
            pay_url: undefined,
            showBuildResult: false
        },
        methods: {
            bcheck: function () {
                var _this = this;
                if(_this.disBtn){return;}
                if(!_this.form.Length){$.toast('请填写板长','text');return;}
                if(_this.form.Length < _this.MinLength || _this.form.Length > _this.MaxLength){
                    $.toast('板长范围：' + _this.MinLength + 'mm&nbsp;~&nbsp;' + _this.MaxLength + 'mm','text');return;
                }
                if(!_this.form.Width){$.toast('请填写板宽','text');return;}
                if(_this.form.Width < _this.MinWidth || _this.form.Width > _this.MaxWidth){
                    $.toast('板宽范围：' + _this.MinWidth + 'mm&nbsp;~&nbsp;' + _this.MaxWidth + 'mm','text');return;
                }
                if(!_this.form.ScoreName){$.toast('请选择压线名称','text');return;}
                if(_this.form.ScoreInfo){
                    if(!new RegExp('<?php echo C('ScoreInfoPatternJS');?>').test(_this.form.ScoreInfo)){
                        $.toast('压线格式不正确','text');return;
                    }
                    if(Number(this.form.Width) !== eval(this.form.ScoreInfo)){
                        $.toast('压线和不等于板宽','text');return;
                    }
                }
                if(!_this.form.OrdQty){$.toast('请填写订单数','text');return;}
                if(_this.Area && !_this.validArea){
                    $.toast('下单面积范围：' + Number(_this.ProductInfo.BuildMin) + '㎡&nbsp;~&nbsp;' + Number(_this.ProductInfo.BuildMax) + '㎡','text');return;
                }
                if(!_this.form.CusSubNo){$.toast('请选择送货公司','text');return;}
                if(!_this.form.DeliveryDate){$.toast('请填写交货日期','text');return;}
                _this.form = Object.assign({},_this.form,{Id: _this.ProductInfo.Id});
                _this.form.DeliveryDate = datetimeFormat(_this.form.DeliveryDate,'yyyy-MM-dd');
                $.ajax({
                    url: '<?php echo U('bcheck_s_api');?>',
                    type: 'post',
                    data: _this.form,
                    beforeSend: function () {
                        _this.$indicator.open();
                    },
                    success: function (respon) {
                        _this.$indicator.close();
                        var respon = eval('(' + respon + ')');
                        if(respon.ret === '<?php echo C('succ_ret');?>'){
                            document.body.classList.add('body-lock');
                            _this.showConfirmBuildInfo = true;
                        }else{
                            $.toast(respon.msg,'forbidden');
                        }
                    }
                });
            },
            build: function () {
                var _this = this;
                $.ajax({
                    url: '<?php echo U('s_api');?>',
                    type: 'post',
                    data: _this.form,
                    beforeSend: function () {
                        _this.$indicator.open();
                    },
                    success: function (respon) {
                        _this.$indicator.close();
                        var respon = eval('(' + respon + ')');
                        _this.result = (respon.ret === '<?php echo C('succ_ret');?>');
                        if(_this.result){
                            _this.form = Object.assign({},_this.form_ini,{ScoreName: _this.form.ScoreName,ScoreName_: _this.form.ScoreName,CusSubNo: _this.form.CusSubNo,CusSubNo_: _this.form.CusSubNo});
                            _this.Area = null;
                            _this.validArea = false;
                            _this.Cost = null;
                            _this.SaveCost = null;
                            _this.IsRangePrice = false;
                            _this.Price = null;
                            _this.help = null;
                        }
                        _this.msg = respon.msg;
                        if(Number('<?php echo ($config['UseWxPay']); ?>') || Number('<?php echo ($config['UseAliPay']); ?>')){
                            _this.pay_url = '<?php echo U('Pay/Order/way');?>?CusPoNo=' + respon.CusPoNo;
                        }
                        _this.showBuildResult = true;
                    }
                });
            },
            //通过纸板数反推订单数
            getOrdQtyByBdQty: function (BdQty) {
                return BdQty;
            },
            calcAreaCost: function () {
                var _this = this;
                _this.Area = null;
                _this.validArea = false;
                _this.Cost = null;
                _this.SaveCost = null;
                _this.IsRangePrice = false;
                _this.Price = null;
                _this.help = null;
                if(_this.form.Length && (_this.form.Length < _this.MinLength || _this.form.Length > _this.MaxLength)){
                    _this.form.Length = null;$.toast('板长范围：' + _this.MinLength + 'mm&nbsp;~&nbsp;' + _this.MaxLength + 'mm','text',function () {
                        _this.disBtn = false;
                    });return;
                }
                if(_this.form.Width && (_this.form.Width < _this.MinWidth || _this.form.Width > _this.MaxWidth)){
                    _this.form.Width = null;$.toast('板宽范围：' + _this.MinWidth + 'mm&nbsp;~&nbsp;' + _this.MaxWidth + 'mm','text',function () {
                        _this.disBtn = false;
                    });return;
                }
                if(_this.form.OrdQty && !(/(^[1-9]\d*$)/.test(_this.form.OrdQty))){
                    $.toast('订单数错误','text',function () {
                        _this.validArea = false;
                        _this.form.OrdQty = null;
                        _this.disBtn = false;
                    });return;
                }
                if(_this.form.Length && _this.form.Width && _this.form.OrdQty){
                    $.ajax({
                        url: '<?php echo U('calcAreaCost_api');?>',
                        type: 'get',
                        data: {
                            Id: _this.ProductInfo.Id,
                            Length: _this.form.Length,
                            Width: _this.form.Width,
                            OrdQty: _this.form.OrdQty
                        },
                        beforeSend: function () {
                            _this.$indicator.open();
                        },
                        success: function (respon) {
                            _this.$indicator.close();
                            var respon = eval('(' + respon + ')');
                            _this.Area = respon.Area;
                            if(Number(respon.validArea)){
                                _this.validArea = true;
                                _this.Cost = respon.Cost;
                                _this.SaveCost = respon.SaveCost;
                                if(Number(respon.IsRangePrice)){
                                    _this.IsRangePrice = true;
                                    _this.Price = respon.Price;
                                    _this.help = respon.help;
                                }else{
                                    _this.IsRangePrice = false;
                                    _this.Price = null;
                                    _this.help = null;
                                }
                            }else{
                                $.toast('下单面积范围：' + Number(_this.ProductInfo.BuildMin) + '㎡&nbsp;~&nbsp;' + Number(_this.ProductInfo.BuildMax) + '㎡','text',function () {
                                    _this.validArea = false;
                                    _this.form.OrdQty = null;
                                    _this.Area = null;
                                });
                            }
                            _this.disBtn = false;
                        }
                    });
                }else{
                    _this.disBtn = false;
                }
            }
        },
        mounted: function () {
            this.form = Object.assign({},this.form_ini);
        }
    });
</script>

</body>
</html>