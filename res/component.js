Vue.component('cell-group', {
    template: '<div class="cell-group" :class="{\'mini\':mini}">\n' +
    '        <slot/>\n' +
    '    </div>',
    props: {mini: Boolean}
});
Vue.component('cell', {
    template: '<div class="cell"\n' +
    '         :style="{padding:padding}"' +
    '         :class="{\'cell-clickable\':clickable || isLink}"\n' +
    '         @click="onClick">\n' +
    '        <div class="cell-title" :style="{width:labelWidth?labelWidth:\'\'}"><span v-html="title"></span></div>\n' +
    '        <div class="cell-value">\n' +
    '            <slot>\n' +
    '                <span style="display: block;text-align: center;"\n' +
    '                      :style="{color:!value?\'#999\':\'inherit\',\'padding-right\':help?\'5px\':\'0\'}">\n' +
    '                    {{value?value:placeholder}}\n' +
    '                </span>\n' +
    '            </slot>\n' +
    '        </div>\n' +
    '        <slot name="right-icon"></slot>' +
    '        <i class="cell-right-icon" :class="{\'arrow-left-icon\':isLink}"' +
    '           :style="{\'padding-right\':help?\'5px\':\'0\'}">' +
    '           <svg @click.stop="$emit(\'icon-click\')" v-if="help" t="1524312955582" fill="#1aad19" class="icon"\n             viewBox="0 0 1024 1024"\n             version="1.1"\n             xmlns="http://www.w3.org/2000/svg" p-id="2159"\n             width="18" height="18">\n            <path d="M1024 512C1024 229.230208 794.769792 0 512 0 229.230208 0 0 229.230208 0 512 0 794.769792 229.230208 1024 512 1024 629.410831 1024 740.826187 984.331046 830.768465 912.686662 841.557579 904.092491 843.33693 888.379234 834.742758 877.590121 826.148587 866.801009 810.43533 865.021658 799.646219 873.615827 718.470035 938.277495 618.001779 974.048781 512 974.048781 256.817504 974.048781 49.951219 767.182496 49.951219 512 49.951219 256.817504 256.817504 49.951219 512 49.951219 767.182496 49.951219 974.048781 256.817504 974.048781 512 974.048781 599.492834 949.714859 683.336764 904.470807 755.960693 897.177109 767.668243 900.755245 783.071797 912.462793 790.365493 924.170342 797.659191 939.573897 794.081058 946.867595 782.373508 997.013826 701.880796 1024 608.898379 1024 512Z"\n                  p-id="2160"></path>\n            <path d="M533.078812 691.418556C551.918022 691.418556 567.190219 706.673952 567.190219 725.511386L567.190219 734.541728C567.190219 753.370677 552.049365 768.634558 533.078812 768.634558L533.078812 768.634558C514.239601 768.634558 498.967405 753.379162 498.967405 734.541728L498.967405 725.511386C498.967405 706.682436 514.108258 691.418556 533.078812 691.418556L533.078812 691.418556ZM374.634146 418.654985C374.634146 418.654985 377.308518 442.210609 403.631972 442.210609 429.955424 442.210609 431.511799 418.654985 431.511799 418.654985 429.767552 342.380653 465.107535 306.162338 537.45591 309.760186 585.612324 315.19693 610.562654 342.380653 612.231066 391.391309 608.894242 413.21824 590.617557 441.441342 558.083539 475.90071 515.008196 519.47462 493.470524 558.49126 493.470524 592.950626L493.470524 628.289468C493.470524 628.289468 496.775846 649.365867 520.582206 649.365867 544.388565 649.365867 547.693888 628.289468 547.693888 628.289468L547.693888 603.744164C547.693888 574.961397 568.321517 540.342125 609.652612 500.28611 652.879629 460.469948 674.341463 424.091729 674.341463 391.391309 670.777131 300.725594 623.530758 253.473886 532.223166 249.796087 427.189099 248.037141 374.634146 304.323439 374.634146 418.654985Z"\n                  p-id="2161"></path>\n        </svg>\n    </i>\n' +
    '    </div>',
    props: {
        title: String,
        placeholder: String,
        value: [String, Number],
        isLink: Boolean,
        help: Boolean,
        clickable: Boolean,
        padding: String,
        labelWidth: String
    },
    methods: {
        onClick: function () {
            this.$emit('click');
        }
    }
});
Vue.component('field', {
    template: '<cell class="field"\n' +
    '          :help="help"\n  :label-width="labelWidth" :padding="padding"\n' +
    '          @icon-click="$emit(\'icon-click\')"\n' +
    '          :title="label" :style="{\'align-items\':type === \'textarea\'?\'baseline\':\'center\'}">\n' +
    '        <textarea v-if="type === \'textarea\'"\n' +
    '                  v-bind="$attrs"\n' +
    '                  v-on="listeners"\n' +
    '                  ref="textarea"\n' +
    '                  class="field-control" style="outline: none;text-align: left;"\n' +
    '                  :id="id"\n'+
    '                  :value="data"' +
    '                  @focus="$emit(\'focus\')"\n' +
    '                  @blur="$emit(\'blur\')"\n' +
    '        />\n' +
    '        <input v-else\n' +
    '               v-bind="$attrs"\n' +
    '               v-on="listeners"\n' +
    '               class="field-control" style="outline: none;text-align: center;"\n' +
    '               ref="field"\n' +
    '               :id="id"\n'+
    '               :type="type"\n' +
    '               :value="data"\n' +
    '               @focus="$emit(\'focus\')"\n' +
    '               @blur="$emit(\'blur\')"\n' +
    '        />\n' +
    '      <div slot="right-icon"><slot name="right-icon"></slot></div>' +
    '    </cell>',
    props: {
        type: {
            type: String,
            default: 'text'
        },
        value: {},
        label: String,
        autosize: [Boolean, Object],
        help: Boolean,
        padding: String,
        id:String,
        labelWidth: String
    },
    data: function () {
        return {
            data: this.value
        };
    },
    computed: {
        listeners: function () {
            return {
                input: this.onInput
            };
        }
    },
    watch: {
        data: function (val) {
            this.$nextTick(this.adjustSize);
            this.$emit('input', val);
        },
        value: function (val) {
            this.data = val;
        }
    },
    methods: {
        isObj: function (x) {
            var type = typeof x;
            return x !== null && (type === 'object' || type === 'function');
        },
        onInput: function (event) {
            this.data = event.target.value;
            this.$emit('input', event.target.value);
        },
        adjustSize: function () {
            if (!(this.type === 'textarea' && this.autosize)) {return;}
            var el = this.$refs.textarea;
            if (!el) {return;}
            el.style.height = 'auto';
            var height = el.scrollHeight;
            if (this.isObj(this.autosize)) {
                var maxHeight, minHeight = this.autosize;
                if (maxHeight) {height = Math.min(height, maxHeight);}
                if (minHeight) {height = Math.max(height, minHeight);}
            }
            if (height) {el.style.height = height + 'px';}
        },
        focus: function () {
            var target = this.type === 'textarea'?this.type:'field';
            this.$refs[target].focus();
        },
        blur: function () {
            var target = this.type === 'textarea'?this.type:'field';
            this.$refs[target].blur();
        }
    },
    mounted: function () {
        this.$nextTick(this.adjustSize);
    }
});
Vue.component('menu-box',{
    template: '<div class="menu-box">\n' +
        '    <div class="empty" v-if="Number(right) && ur_name === \',,\'">\n' +
        '        <img :src="empty_img">\n' +
        '        <p>此账号没有任何权限</p>\n' +
        '    </div>\n' +
        '    <div v-else>\n' +
        '        <div class="item" v-if="showURName(v.URName)" v-for="v in lists">\n' +
        '            <div @click="window.location.href = v.href">\n' +
        '                <a>\n' +
        '                    <i :class="\'iconfont \' + v.iconClass"></i>\n' +
        '                    <div>{{v.title}}</div>\n' +
        '                </a>\n' +
        '            </div>\n' +
        '        </div>\n' +
        '    </div>\n' +
        '</div>',
    props: ['lists','right','ur_name','empty_img'],
    methods: {
        showURName: function (URName) {
            var _this = this;
            if(!Number(_this.right)){
                return true;
            }
            if(URName.indexOf('@') === -1){
                return _this.ur_name.indexOf(',' + URName + ',') !== -1;
            }else{
                var URName_ = URName.split('@'),result = false;
                $.each(URName_,function (k,v) {
                    if(_this.ur_name.indexOf(',' + v + ',') !== -1){
                        result = true;
                        return false;
                    }
                });
                return result;
            }
        }
    }
});
Vue.component('master-picker',{
    template: '<div class="master-picker">\n' +
    '    <div class="mask" @click="close" v-if="show"></div>\n' +
    '    <transition name="fade">\n' +
    '        <div class="area_ctrl" v-if="show">\n' +
    '            <div class="area_btn_box">\n' +
    '                <div class="area_btn larea_cancel" @click="clear">清空</div>\n' +
    '                <div class="slot-center">\n' +
    '                    <slot></slot>\n' +
    '                </div>\n' +
    '                <div class="area_btn larea_finish" @click="confirm">确定</div>\n' +
    '            </div>\n' +
    '            <div class="area_roll_mask">\n' +
    '                <div class="area_roll">\n' +
    '                    <div class="loading" v-if="loading">\n' +
    '                        <div>\n' +
    '                            <svg class="svg" width="38" height="38" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" stroke="#1aad19">\n' +
    '                                <g fill="none" fill-rule="evenodd">\n' +
    '                                    <g transform="translate(1 1)" stroke-width="3">\n' +
    '                                        <circle stroke-opacity="0.3" cx="18" cy="18" r="18"/>\n' +
    '                                        <path d="M36 18c0-9.94-8.06-18-18-18">\n' +
    '                                            <animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="0.6s" repeatCount="indefinite"/>\n' +
    '                                        </path>\n' +
    '                                    </g>\n' +
    '                                </g>\n' +
    '                            </svg>\n' +
    //'                            <span>加载中...</span>\n' +
    '                        </div>\n' +
    '                    </div>\n' +
    '                    <div>\n' +
    '                        <div top="0" ref="province" class="gear area_province" data-areatype="area_province" data-type="provs" :data-len="pData1.length" val="5" @touchstart="gearTouchStart" @touchmove="gearTouchMove" @touchend="gearTouchEnd">\n' +
    '                            <div v-if="connection" class="tooth" v-for="(v,k) in pData1" :key="k">\n' +
    '                                <div class="row">\n' +
    '                                    <span>{{v.value}}</span>\n' +
    '                                    <span>{{v.text}}</span>\n' +
    '                                </div>\n' +
    '                            </div>\n' +
    '                            <div v-if="!connection" class="tooth" v-for="(v,k) in pData1" :key="k">\n' +
    '                                <div>{{v.text}}</div>\n' +
    '                            </div>\n' +
    '                        </div>\n' +
    '                        <div class="area_grid" v-if="!noData">\n' +
    '                        </div>\n' +
    '                        <div class="no-data" v-else>暂无数据</div>\n' +
    '                    </div>\n' +
    '                    <div v-if="selectData.columns > 1">\n' +
    '                        <div class="gear area_city" top="0" ref="city" data-areatype="area_city" data-type="city" :data-len="pData2.length" @touchstart="gearTouchStart" @touchmove="gearTouchMove" @touchend="gearTouchEnd" val="5">\n' +
    '                            <div class="tooth" v-for="(v,k) in pData2" :key="k">{{v.text}}</div>\n' +
    '                        </div>\n' +
    '                        <div class="area_grid"></div>\n' +
    '                    </div>\n' +
    '                    <div v-if="selectData.columns > 2">\n' +
    '                        <div class="gear area_county" top="0" ref="county" data-areatype="area_county" :data-len="pData3.length" @touchstart="gearTouchStart" @touchmove="gearTouchMove" @touchend="gearTouchEnd" val="5">\n' +
    '                            <div class="tooth" v-for="(v,k) in pData3" :key="k">{{v.text}}</div>\n' +
    '                        </div>\n' +
    '                        <div class="area_grid"></div>\n' +
    '                    </div>\n' +
    '                </div>\n' +
    '            </div>\n' +
    '        </div>\n' +
    '    </transition>\n' +
    '</div>',
    props: {
        connection: Boolean,
        show: {
            type: Boolean,
            default: false,
        },
        selectData: {
            type: Object,
            default: {},
        },
        loading: Boolean,
        noData: Boolean,
    },
    data: function() {
        return {
            pData1: [],
            pData2: [],
            pData3: [],
            selects: {
                select1: '',
                select2: '',
                select3: '',
            },
            toothHeight: 0,
        };
    },
    methods: {
        clear: function(e) {
            this.$emit('clear');
            e.preventDefault();
        },
        confirm: function(e) {
            this.$emit('confirm', this.selects);
            e.preventDefault();
        },
        close: function(){
            this.$emit('close', this.selects);
            this.$emit('update:show', false);
        },
        gearTouchStart: function(e) {
            e.preventDefault();
            var target = e.target;
            while (true) {
                if (!target.classList.contains('gear')) {
                    target = target.parentElement;
                } else {
                    break;
                }
            }
            clearInterval(target['int_' + target.id]);
            target['old_' + target.id] = e.targetTouches[0].screenY;
            target['o_t_' + target.id] = (new Date()).getTime();
            var top = target.getAttribute('top');
            if (top) {
                target['o_d_' + target.id] = parseFloat(top.replace(/em/g, ''));
            } else {
                target['o_d_' + target.id] = 0;
            }
            target.style.webkitTransitionDuration = target.style.transitionDuration = '0ms';
        },
        //手指移动
        gearTouchMove: function(e) {
            e.preventDefault();
            var target = e.target;
            while (true) {
                if (!target.classList.contains('gear')) {
                    target = target.parentElement;
                } else {
                    break;
                }
            }
            target['new_' + target.id] = e.targetTouches[0].screenY;
            target['n_t_' + target.id] = (new Date()).getTime();
            var f = (target['new_' + target.id] - target['old_' + target.id]) * 30 / window.innerHeight;
            target['pos_' + target.id] = target['o_d_' + target.id] + f;
            target.style['-webkit-transform'] = 'translate3d(0,' + target['pos_' + target.id] + 'em,0)';
            target.setAttribute('top', target['pos_' + target.id] + 'em');
            if (e.targetTouches[0].screenY < 1) {
                this.gearTouchEnd(e);
            }
        },
        gearTouchEnd: function(e) {
            e.preventDefault();
            var target = e.target;
            while (true) {
                if (!target.classList.contains('gear')) {
                    target = target.parentElement;
                } else {
                    break;
                }
            }
            var flag = (target['new_' + target.id] - target['old_' + target.id]) / (target['n_t_' + target.id] - target['o_t_' + target.id]);
            if (Math.abs(flag) <= 0.2) {
                target['spd_' + target.id] = (
                    flag < 0 ? -0.08 : 0.08);
            } else {
                if (Math.abs(flag) <= 0.5) {
                    target['spd_' + target.id] = (
                        flag < 0 ? -0.16 : 0.16);
                } else {
                    target['spd_' + target.id] = flag / 2;
                }
            }
            if (!target['pos_' + target.id]) {
                target['pos_' + target.id] = 0;
            }
            this.rollGear(target);
        },
        rollGear: function(target) {
            var _this = this;
            var d = 0;
            var stopGear = false;
            function setDuration() {
                target.style.webkitTransitionDuration = target.style.transitionDuration = '200ms';
                stopGear = true;
            }
            clearInterval(target['int_' + target.id]);
            target['int_' + target.id] = setInterval(function() {
                var pos = target['pos_' + target.id];
                var speed = target['spd_' + target.id] * Math.exp(-0.03 * d);
                pos += speed;
                if (Math.abs(speed) > 0.1) {

                } else {
                    var b = Math.round(pos / 2) * 2;
                    pos = b;
                    setDuration();
                }
                if (pos > 0) {
                    pos = 0;
                    setDuration();
                }
                var minTop = -(target.dataset.len - 1) * 2;
                if (pos < minTop) {
                    pos = minTop;
                    setDuration();
                }
                if (stopGear) {
                    var gearVal = Math.abs(pos) / 2;
                    _this.setGear(target, gearVal);
                    clearInterval(target['int_' + target.id]);
                }
                target['pos_' + target.id] = pos;
                target.style['-webkit-transform'] = 'translate3d(0,' + pos + 'em,0)';
                target.setAttribute('top', pos + 'em');
                d++;
            }, 30);
        },
        setGear: function(target, val) {
            var _self = this;
            var endVal = Math.round(val);
            var type = target.getAttribute('data-type');
            // 不是联级
            if (!this.selectData.link) {
                if (type === 'provs') {
                    _self.selects.select1 = _self.pData1[endVal];
                } else if (type === 'city') {
                    _self.selects.select2 = _self.pData2[endVal];
                } else {
                    _self.selects.select3 = _self.pData3[endVal];
                }
            } else {
                if (type === 'provs') {
                    _self.selects.select1 = _self.pData1[endVal];
                    _self.resetData2(endVal);
                    if (this.selectData.columns === 3) {
                        _self.resetData3(0);
                    }
                } else if (type === 'city' && this.selectData.columns === 2) {
                    this.selects.select2 = this.pData2[endVal];
                } else if (type === 'city' && this.selectData.columns === 3) {
                    _self.resetData3(endVal);
                    this.selects.select2 = this.pData2[endVal];
                } else if (this.selectData.columns === 3) {
                    this.selects.select3 = this.pData3[endVal];
                }
            }
        },
        setTop: function(defaultData) {
            this.$nextTick(function() {
                var province = this.$refs.province;
                var city = this.$refs.city;
                var county = this.$refs.county;
                var pos1 = 0;
                var pos2 = 0;
                var pos3 = 0;
                if (defaultData[0] && defaultData[0].value) {
                    this.selects.select1 = defaultData[0];
                    for (var i = 0, len = this.pData1.length; i < len; i++) {
                        if (this.pData1[i].value == defaultData[0].value) {
                            pos1 = -(i * 2);
                            break;
                        }
                    }
                    province.style.transform = province.style['-webkit-transform'] = 'translate3d(0,' + pos1 + 'em,0)';
                    province.setAttribute('top', pos1 + 'em');
                }
                if (defaultData[1] && defaultData[1].value) {
                    for (var i = 0, len = this.pData2.length; i < len; i++) {
                        if (this.pData2[i].value == defaultData[1].value) {
                            pos2 = -(i * 2);
                            break;
                        }
                    }
                    this.selects.select2 = defaultData[1];
                    city.setAttribute('top', pos2 + 'em');
                    city.style['-webkit-transform'] = 'translate3d(0,' + pos2 + 'em,0)';
                }
                if (defaultData[2] && defaultData[2].value) {
                    for (var i = 0, len = this.pData3.length; i < len; i++) {
                        if (this.pData3[i].value == defaultData[2].value) {
                            pos3 = -(i * 2);
                            break;
                        }
                    }
                    this.selects.select3 = defaultData[2];
                    county.setAttribute('top', pos3 + 'em');
                    county.style['-webkit-transform'] = 'translate3d(0,' + pos3 + 'em,0)';
                }
            });
        },
        resetData2: function(endVal) {
            this.$nextTick(function() {
                var city = this.$refs.city;
                if (this.pData1[endVal] && this.selectData.pData2[this.pData1[endVal].value]) {
                    this.pData2 = this.selectData.pData2[this.pData1[endVal].value];
                } else {
                    this.pData2 = [];
                }
                this.selects.select2 = this.pData2[0];
                city.setAttribute('top', 0);
                city.style['-webkit-transform'] = 'translate3d(0, 0, 0)';
            });
        },
        resetData3: function(endVal) {
            this.$nextTick(function() {
                var county = this.$refs.county;
                if (this.pData2.length > 0 && this.pData2[endVal]) {
                    this.pData3 = this.selectData.pData3[this.pData2[endVal].value];
                } else {
                    this.pData3 = [];
                }
                this.selects.select3 = this.pData3[0];
                county.setAttribute('top', 0);
                county.style['-webkit-transform'] = 'translate3d(0, 0, 0)';
            });
        },
        init: function() {
            if (!this.selectData.link) {
                this.pData1 = this.selectData.pData1;
                this.pData2 = this.selectData.pData2;
                this.pData3 = this.selectData.pData3;
            } else {
                this.pData1 = this.selectData.pData1;
                this.pData2 = this.selectData.pData2[this.pData1[0].value];
                if (this.selectData.columns === 3) {
                    this.pData3 = this.selectData.pData3[this.pData2[0].value];
                }
            }
            if (this.selectData.columns === 1) {
                this.selects.select1 = this.pData1[0];
            } else if (this.selectData.columns === 2) {
                this.selects.select1 = this.pData1[0];
                this.selects.select2 = this.pData2[0];
            } else if (this.selectData.columns === 3) {
                this.selects.select1 = this.pData1[0];
                this.selects.select2 = this.pData2[0];
                this.selects.select3 = this.pData3[0];
            }
        },
    },
    created: function() {
        this.init();
    },
    watch: {
        selectData: {
            handler: function () {
                var _this = this;
                setTimeout(function () {
                    if(_this.$refs.province){
                        _this.$refs.province.style['-webkit-transform'] = 'translate3d(0, 0, 0)';
                    }
                },0);
                _this.init();
            },
            deep: true,
        },
        show: function(val) {
            val && this.setTop(this.selectData.default || []);
        },
    },
});
Vue.component('open-statis-card',{
    template: '<div class="open-statis-card page" v-if="filterbarMode">\n' +
    '        <div class="statis-group">\n' +
    '            <transition-group name="slide-son">\n' +
    '                <div class="statis-item" v-for="(v1,k1) in statisFilter" v-if="checkShow(v1)" :key="k1">\n' +
    '                    <label class="title">{{v1.title}}</label>\n' +
    '                    <div class="statis-labels">\n' +
    '                        <label class="labels-item"\n' +
    '                               :class="{\n' +
    '                           \'selected\':statisModel.type.value === v2.value\n' +
    '                             || statisModel.way.value === v2.value\n' +
    '                             || statisModel.charts.type.value === v2.value\n' +
    '                             || statisModel.charts.prop.value === v2.value\n' +
    '                            }"\n' +
    '                               v-for="(v2,k2) in v1.data" :key="k2">\n' +
    '                            {{v2.name}}\n' +
    '                            <input v-if="v1.title === \'统计类型\'" :value="v2" v-model="statisModel.type" type="radio"\n' +
    '                                   name="statisType" hidden>\n' +
    '                            <input v-else-if="v1.title === \'统计方式\'" :value="v2" v-model="statisModel.way"\n' +
    '                                   type="radio"\n' +
    '                                   name="statisWay" hidden>\n' +
    '                            <input v-else-if="v1.title === \'图表类型\'" :value="v2" v-model="statisModel.charts.type"\n' +
    '                                   type="radio"\n' +
    '                                   name="chartsType" hidden>\n' +
    '                            <input v-else-if="v1.title === \'图表属性\'" :value="v2" v-model="statisModel.charts.prop"\n' +
    '                                   type="radio"\n' +
    '                                   name="chartsProp" hidden>\n' +
    '                        </label>\n' +
    '                    </div>\n' +
    '                </div>\n' +
    '            </transition-group>\n' +
    '        </div>\n' +
    '        <button @click="finish" class="statis-confirm">确定</button>\n' +
    '    </div>',
    props: {
        statisFilter: {type: Array, default: null},
        filterbarMode: {type: Boolean, default: false},
        statisModelProp: Object
    },
    data: function () {
        return {
            //最终选择的统计参数
            statisModel: JSON.parse(JSON.stringify(this.statisModelProp))
        };
    },
    methods: {
        checkShow: function (statis) {
            if (statis.title === '统计类型' || statis.title === '统计方式') {
                return true;
            }
            if (this.statisModel.way.value === 'charts') {
                return true;
            } else if (this.statisModel.way.value === 'lists') {
                return false;
            }
            return false;
        },
        finish: function () {
            this.$emit('update:filterbarMode',false);
            this.$emit('finish',this.statisModel);
        }
    },
    watch: {
        'statisModel.way.value': function (newVal) {
            var charts = this.statisModel.charts;
            if (newVal === 'charts') {
                this.statisFilter.forEach(function (item) {
                    if (item.title === '图表类型') {
                        charts.type = item.data[0];
                    }
                    if (item.title === '图表属性') {
                        charts.prop = item.data[0];
                    }
                });
            } else if (newVal === 'lists') {
                charts.type = {};
                charts.prop = {};
            }
        }
    }
});
Vue.component('wap01-order-detail-fullpage',{
    template: '<div class="wap01-order-detail-fullpage" v-if="showDetail">\n' +
    '        <div class="top-title" v-if="GetOrderDNRe">退货明细</div>\n' +
    '        <div class="list-info" v-if="GetOrderDNRe">\n' +
    '            <div class="item">\n' +
    '                <div class="label">单号</div>\n' +
    '                <div class="value">{{GetOrderDNRe.DNStr}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">生效日期</div>\n' +
    '                <div class="value">{{GetOrderDNRe.IssueDate}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">订单编号</div>\n' +
    '                <div class="value">{{GetOrderDNRe.OrderId}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">送货数</div>\n' +
    '                <div class="value">{{GetOrderDNRe.DeliQty}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">销售面积(㎡)</div>\n' +
    '                <div class="value">{{GetOrderDNRe.TSalesArea}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">送货金额(元)</div>\n' +
    '                <div class="value">{{GetOrderDNRe.DeliAmt}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">价格(元)</div>\n' +
    '                <div class="value">{{GetOrderDNRe.Price}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">平方价(元/㎡)</div>\n' +
    '                <div class="value">{{GetOrderDNRe.SquarePrice}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">退货原因</div>\n' +
    '                <div class="value">{{GetOrderDNRe.ReturnCause}}</div>\n' +
    '            </div>\n' +
    '        </div>\n' +
    '        <div class="top-title" v-if="GetOrderDN">送货明细</div>\n' +
    '        <div class="list-info" v-if="GetOrderDN">\n' +
    '            <div class="item">\n' +
    '                <div class="label">单号</div>\n' +
    '                <div class="value">{{GetOrderDN.DNStr}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">回签</div>\n' +
    '                <div class="value">{{GetOrderDN.Signed}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">装货日期</div>\n' +
    '                <div class="value">{{GetOrderDN.PackDate}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">送货日期</div>\n' +
    '                <div class="value">{{GetOrderDN.DNDate}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">生效日期</div>\n' +
    '                <div class="value">{{GetOrderDN.IssueDate}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">订单编号</div>\n' +
    '                <div class="value">{{GetOrderDN.OrderId}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">送货数</div>\n' +
    '                <div class="value">{{GetOrderDN.DeliQty}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">销售面积(㎡)</div>\n' +
    '                <div class="value">{{GetOrderDN.TSalesArea}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">送货金额(元)</div>\n' +
    '                <div class="value">{{GetOrderDN.DeliAmt}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">价格(元)</div>\n' +
    '                <div class="value">{{GetOrderDN.Price}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">平方价(元/㎡)</div>\n' +
    '                <div class="value">{{GetOrderDN.SquarePrice}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">送货备注</div>\n' +
    '                <div class="value">{{GetOrderDN.DNRemark}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">送货公司</div>\n' +
    '                <div class="value">{{GetOrderDN.CusSubNo}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">子公司简称</div>\n' +
    '                <div class="value">{{GetOrderDN.CusSubName}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">送货地址</div>\n' +
    '                <div class="value">{{GetOrderDN.SubDNAddress}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">联系人</div>\n' +
    '                <div class="value">{{GetOrderDN.SubContactPerson}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">联系电话</div>\n' +
    '                <div class="value"><a :href="\'tel:\' + GetOrderDN.SubTelNo">{{GetOrderDN.SubTelNo}}</a></div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">车牌号</div>\n' +
    '                <div class="value">{{GetOrderDN.CarNo}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">司机名称</div>\n' +
    '                <div class="value">{{GetOrderDN.CarPName}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">电话</div>\n' +
    '                <div class="value"><a :href="\'tel:\' + GetOrderDN.Phone">{{GetOrderDN.Phone}}</a></div>\n' +
    '            </div>\n' +
    '        </div>\n' +
    '        <div class="top-title" v-if="GetOrderSch">传单明细</div>\n' +
    '        <div class="list-info" v-if="GetOrderSch">\n' +
    '            <div class="item">\n' +
    '                <div class="label">工单状态</div>\n' +
    '                <div class="value">{{GetOrderSch.SState}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">门幅(mm)</div>\n' +
    '                <div class="value">{{GetOrderSch.SPaperWidth}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">数量</div>\n' +
    '                <div class="value">{{GetOrderSch.SQty}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">添加时间</div>\n' +
    '                <div class="value">{{GetOrderSch.AddTime}}</div>\n' +
    '            </div>\n' +
    '        </div>\n' +
    '        <div class="top-title" v-if="GetOrderDetail">订单详情</div>\n' +
    '        <div class="list-info" v-if="GetOrderDetail">\n' +
    '            <div class="item">\n' +
    '                <div class="label">客户编号</div>\n' +
    '                <div class="value">{{GetOrderDetail.CusId}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">客户简称</div>\n' +
    '                <div class="value">{{GetOrderDetail.CusShortName}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">送货备注</div>\n' +
    '                <div class="value">{{GetOrderDetail.DNRemark}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">订单编号</div>\n' +
    '                <div class="value">{{GetOrderDetail.strOrderId}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">客订单号</div>\n' +
    '                <div class="value">{{GetOrderDetail.CusPoNo}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">订单材质</div>\n' +
    '                <div class="value">{{GetOrderDetail.BoardId}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">材质名称</div>\n' +
    '                <div class="value">{{GetOrderDetail.BoardName}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">压线信息</div>\n' +
    '                <div class="value">{{GetOrderDetail.ScoreInfo}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">压型</div>\n' +
    '                <div class="value">{{GetOrderDetail.ScoreType}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">货品名称</div>\n' +
    '                <div class="value">{{GetOrderDetail.MatName}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">箱型</div>\n' +
    '                <div class="value">{{GetOrderDetail.BoxName}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">箱长(mm)</div>\n' +
    '                <div class="value">{{GetOrderDetail.BoxL}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">箱宽(mm)</div>\n' +
    '                <div class="value">{{GetOrderDetail.BoxW}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">箱高(mm)</div>\n' +
    '                <div class="value">{{GetOrderDetail.BoxH}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">板长(mm)</div>\n' +
    '                <div class="value">{{GetOrderDetail.Length}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">板宽(mm)</div>\n' +
    '                <div class="value">{{GetOrderDetail.Width}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">工艺流程</div>\n' +
    '                <div class="value">{{GetOrderDetail.ProFlow}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">订单数</div>\n' +
    '                <div class="value">{{GetOrderDetail.OrdQty}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">传单数</div>\n' +
    '                <div class="value">{{GetOrderDetail.SchQty}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">完工数</div>\n' +
    '                <div class="value">{{GetOrderDetail.FinishedQty}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">送货数</div>\n' +
    '                <div class="value">{{GetOrderDetail.DeliQty}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">退货数</div>\n' +
    '                <div class="value">{{GetOrderDetail.ReturnQty}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">销售面积(㎡)</div>\n' +
    '                <div class="value">{{GetOrderDetail.TSalesArea}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">价格(元)</div>\n' +
    '                <div class="value">{{GetOrderDetail.Price}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">平方价(元/㎡)</div>\n' +
    '                <div class="value">{{GetOrderDetail.SquarePrice}}</div>\n' +
    '            </div>\n' +
    '            <div class="item">\n' +
    '                <div class="label">金额(元)</div>\n' +
    '                <div class="value">{{GetOrderDetail.Amt}}</div>\n' +
    '            </div>\n' +
    '        </div>\n' +
    '        <div class="close-btn" @click="$emit(\'update:showDetail\',false)">关闭</div>\n' +
    '    </div>',
    props: ['ajax_url','ajax_data_order_type','ajax_data_order_id','showDetail'],
    data: function () {
        return {
            GetOrderDNRe: [],
            GetOrderDN: [],
            GetOrderSch: [],
            GetOrderDetail: []
        };
    },
    mounted: function () {
        var _this = this;
        $.ajax({
            url: _this.ajax_url,
            type: 'get',
            data: {
                OrderType: _this.ajax_data_order_type,
                OrderId: _this.ajax_data_order_id
            },
            beforeSend: function () {
                _this.$indicator.open();
            },
            success: function (respon) {
                _this.$indicator.close();
                var respon = eval('(' + respon + ')');
                _this.GetOrderDNRe = respon.GetOrderDNRe;
                _this.GetOrderDN = respon.GetOrderDN;
                _this.GetOrderSch = respon.GetOrderSch;
                _this.GetOrderDetail = respon.GetOrderDetail;
            }
        });
    }
});
Vue.component('wap0-header',{
    template: '<div class="common-header">\n' +
    '    <div class="header">\n' +
    '        <div class="flag" v-if="flag"><i class="iconfont icon-denglu"></i>&nbsp;&nbsp;{{flag}}</div>\n' +
    '        <div class="back" @click="window.history.back()">\n' +
    '            <i class="iconfont icon-fanhui2"></i>\n' +
    '        </div>\n' +
    '        <div class="three-dots" @click="isCall = true">\n' +
    '            <i class="iconfont icon-sandian2"></i>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '    <div :style="{\'visibility\':isCall?\'visible\':\'hidden\'}">\n' +
    '        <div class="close-mini" @click="isCall = false"></div>\n' +
    '        <div class="three-dots-open-mini">\n' +
    '            <div class="angle"></div>\n' +
    '            <div class="item" @click="window.location.href = index_url" v-if="typeof(index_url) !== \'undefined\'">\n' +
    '                <i class="iconfont icon-index"></i>\n' +
    '                &nbsp;首页\n' +
    '            </div>\n' +
    '            <div class="item" @click="window.location.href = menu_url" v-if="typeof(menu_url) !== \'undefined\'">\n' +
    '                <i class="iconfont icon-caidan"></i>\n' +
    '                &nbsp;菜单\n' +
    '            </div>\n' +
    // '            <div class="item" @click="window.location.href = pwd_url" v-if="typeof(pwd_url) !== \'undefined\'">\n' +
    // '                <i class="iconfont icon-iconfontmima"></i>\n' +
    // '                &nbsp;改密\n' +
    // '            </div>\n' +
    '            <div class="item" @click="logout()" v-if="typeof(logout_url) !== \'undefined\'">\n' +
    '                <i class="iconfont icon-tuichu"></i>\n' +
    '                &nbsp;退出\n' +
    '            </div>\n' +
    '            <div class="item" @click="window.location.reload()">\n' +
    '                <i class="iconfont icon-shuaxin"></i>\n' +
    '                &nbsp;刷新\n' +
    '            </div>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '</div>',
    props: [
        'flag','index_url','menu_url',
        'logout_url',//'pwd_url',
        'use_board_group','use_box_group','open_80port','use_wxpay',
        'server_name',
        'frp_80port_domain',
        'logout_ori_url',
        'logout_frp_80port_url'
    ],
    data: function () {
        return {
            isCall: false
        };
    },
    methods: {
        logout: function () {
            var _this = this;
            $.confirm('确定要退出吗？','',
                function () {
                    $.ajax({
                        url: _this.logout_url,
                        success: function (respon) {
                            var respon = eval('(' + respon + ')');
                            if((Number(_this.use_board_group) || Number(_this.use_box_group)) && !Number(_this.open_80port) && Number(_this.use_wxpay)){
                                if(_this.server_name === _this.frp_80port_domain){
                                    $.ajax({
                                        url: _this.logout_ori_url,
                                        dataType: 'jsonp',
                                        success: function () {
                                            window.location.href = respon.redirect_url;
                                        }
                                    });
                                }else{
                                    $.ajax({
                                        url: _this.logout_frp_80port_url,
                                        dataType: 'jsonp',
                                        success: function () {
                                            window.location.href = respon.redirect_url;
                                        }
                                    });
                                }
                            }else{
                                window.location.href = respon.redirect_url;
                            }
                        }
                    });
                }
            );
        }
    }
});
Vue.component('wap1-header',{
    template: '<div class="common-header">\n' +
    '    <div class="header">\n' +
    '        <div class="flag" v-if="flag"><i class="iconfont icon-denglu"></i>&nbsp;&nbsp;{{flag}}</div>\n' +
    '        <div class="back" @click="window.history.back()">\n' +
    '            <i class="iconfont icon-fanhui2"></i>\n' +
    '        </div>\n' +
    '        <div class="three-dots" @click="isCall = true">\n' +
    '            <i class="iconfont icon-sandian2"></i>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '    <div :style="{\'visibility\':isCall?\'visible\':\'hidden\'}">\n' +
    '        <div class="close-mini" @click="isCall = false"></div>\n' +
    '        <div class="three-dots-open-mini">\n' +
    '            <div class="angle"></div>\n' +
    '            <div class="item" @click="window.location.href = index_url" v-if="typeof(index_url) !== \'undefined\'">\n' +
    '                <i class="iconfont icon-index"></i>\n' +
    '                &nbsp;首页\n' +
    '            </div>\n' +
    '            <div class="item" @click="window.location.href = menu_url" v-if="typeof(menu_url) !== \'undefined\'">\n' +
    '                <i class="iconfont icon-caidan"></i>\n' +
    '                &nbsp;菜单\n' +
    '            </div>\n' +
    // '            <div class="item" @click="window.location.href = pwd_url" v-if="typeof(pwd_url) !== \'undefined\'">\n' +
    // '                <i class="iconfont icon-iconfontmima"></i>\n' +
    // '                &nbsp;改密\n' +
    // '            </div>\n' +
    '            <div class="item" @click="logout()" v-if="typeof(logout_url) !== \'undefined\'">\n' +
    '                <i class="iconfont icon-tuichu"></i>\n' +
    '                &nbsp;退出\n' +
    '            </div>\n' +
    '            <div class="item" @click="window.location.reload()">\n' +
    '                <i class="iconfont icon-shuaxin"></i>\n' +
    '                &nbsp;刷新\n' +
    '            </div>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '</div>',
    props: [
        'flag','index_url','menu_url','logout_url',//'pwd_url',
        'open_80port',
        'use_scan',
        'server_name',
        'frp_80port_domain',
        'logout_ori_url',
        'logout_frp_80port_url'
    ],
    data: function () {
        return {
            isCall: false
        };
    },
    methods: {
        logout: function () {
            var _this = this;
            $.confirm('确定要退出吗？','',
                function () {
                    $.ajax({
                        url: _this.logout_url,
                        success: function (respon) {
                            var respon = eval('(' + respon + ')');
                            if(!Number(_this.open_80port) && Number(_this.use_scan)){
                                if(_this.server_name === _this.frp_80port_domain){
                                    $.ajax({
                                        url: _this.logout_ori_url,
                                        dataType: 'jsonp',
                                        success: function () {
                                            window.location.href = respon.redirect_url;
                                        }
                                    });
                                }else{
                                    $.ajax({
                                        url: _this.logout_frp_80port_url,
                                        dataType: 'jsonp',
                                        success: function () {
                                            window.location.href = respon.redirect_url;
                                        }
                                    });
                                }
                            }else{
                                window.location.href = respon.redirect_url;
                            }
                        }
                    });
                }
            );
        }
    }
});
Vue.component('group-header',{
    template: '<div class="common-header">\n' +
    '    <div class="header">\n' +
    '        <div class="flag" v-if="flag"><i class="iconfont icon-denglu"></i>&nbsp;&nbsp;{{flag}}</div>\n' +
    '        <div class="back" @click="window.history.back()">\n' +
    '            <i class="iconfont icon-fanhui2"></i>\n' +
    '        </div>\n' +
    '        <div class="three-dots" @click="isCall = true">\n' +
    '            <i class="iconfont icon-sandian2"></i>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '    <div :style="{\'visibility\':isCall?\'visible\':\'hidden\'}">\n' +
    '        <div class="close-mini" @click="isCall = false"></div>\n' +
    '        <div class="three-dots-open-mini">\n' +
    '            <div class="angle"></div>\n' +
    '            <div class="item" @click="window.location.href = index_url" v-if="typeof(index_url) !== \'undefined\'">\n' +
    '                <i class="iconfont icon-index"></i>\n' +
    '                &nbsp;首页\n' +
    '            </div>\n' +
    '            <div class="item" @click="window.location.href = menu_url" v-if="typeof(menu_url) !== \'undefined\'">\n' +
    '                <i class="iconfont icon-caidan"></i>\n' +
    '                &nbsp;菜单\n' +
    '            </div>\n' +
    '            <div class="item" @click="window.location.reload()">\n' +
    '                <i class="iconfont icon-shuaxin"></i>\n' +
    '                &nbsp;刷新\n' +
    '            </div>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '</div>',
    props: ['flag','index_url','menu_url'],
    data: function () {
        return {
            isCall: false
        };
    }
});
Vue.component('pay-header',{
    template: '<div class="common-header">\n' +
    '    <div class="header">\n' +
    '        <div class="flag" v-if="flag"><i class="iconfont icon-denglu"></i>&nbsp;&nbsp;{{flag}}</div>\n' +
    '        <div class="back" @click="window.history.back()">\n' +
    '            <i class="iconfont icon-fanhui2"></i>\n' +
    '        </div>\n' +
    '        <div class="three-dots" @click="isCall = true">\n' +
    '            <i class="iconfont icon-sandian2"></i>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '    <div :style="{\'visibility\':isCall?\'visible\':\'hidden\'}">\n' +
    '        <div class="close-mini" @click="isCall = false"></div>\n' +
    '        <div class="three-dots-open-mini">\n' +
    '            <div class="angle"></div>\n' +
    '            <div class="item" @click="window.location.href = index_url" v-if="typeof(index_url) !== \'undefined\'">\n' +
    '                <i class="iconfont icon-index"></i>\n' +
    '                &nbsp;首页\n' +
    '            </div>\n' +
    '            <div class="item" @click="window.location.href = menu_url" v-if="typeof(menu_url) !== \'undefined\'">\n' +
    '                <i class="iconfont icon-caidan"></i>\n' +
    '                &nbsp;菜单\n' +
    '            </div>\n' +
    '            <div class="item" @click="window.location.reload()">\n' +
    '                <i class="iconfont icon-shuaxin"></i>\n' +
    '                &nbsp;刷新\n' +
    '            </div>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '</div>',
    props: ['flag','index_url','menu_url'],
    data: function () {
        return {
            isCall: false
        };
    }
});
Vue.component('vue2-countdown',{
    template: '<div class="vue2-countdown">\n' +
        '    <span v-if="msTime.show">\n' +
        '        <span v-if="tipShow">{{tipText}}</span>\n' +
        '        <span v-else>{{tipTextEnd}}</span>\n' +
        '        <span v-if="msTime.day > 0"><span class="pane" :style="{\'background-color\':tipShow?\'#696969\':\'#de1935\'}">{{msTime.day}}</span>天</span><span class="pane" :style="{\'background-color\':tipShow?\'#696969\':\'#de1935\'}">{{msTime.hour}}</span>:<span class="pane" :style="{\'background-color\':tipShow?\'#696969\':\'#de1935\'}">{{msTime.minutes}}</span>:<span class="pane" :style="{\'background-color\':tipShow?\'#696969\':\'#de1935\'}">{{msTime.seconds}}</span>\n' +
        '    </span>\n' +
        '    <span v-else>{{endText}}</span>\n' +
        '</div>',
    props: {
        //距离开始提示文字
        tipText: {
            type: String,
            default: '距离开始'
        },
        //距离结束提示文字
        tipTextEnd: {
            type: String,
            default: '距离结束'
        },
        //时间控件ID
        id: {
            type: String,
            default: '1'
        },
        //当前时间
        currentTime: {
            type: Number
        },
        // 活动开始时间
        startTime: {
            type: Number
        },
        // 活动结束时间
        endTime: {
            type: Number
        },
        // 倒计时结束显示文本
        endText: {
            type: String,
            default: '已结束'
        },
        //是否开启秒表倒计，未完成
        secondsFixed: {
            type: Boolean,
            defaule: false
        }
    },
    data: function () {
        return {
            tipShow: true,
            msTime: {
                show: false,//倒计时状态
                day: '',
                hour: '',
                minutes: '',
                seconds: ''
            },
            star: '',
            end: '',
            current: ''
        };
    },
    methods: {
        runTime (startTime,endTime,callFun,type) {
            let msTime = this.msTime;
            let timeDistance = startTime - endTime;
            if(timeDistance > 0){
                this.msTime.show = true;
                msTime.day = Math.floor( timeDistance / 86400000 );
                timeDistance-= msTime.day * 86400000;
                msTime.hour = Math.floor( timeDistance / 3600000 );
                timeDistance-= msTime.hour * 3600000;
                msTime.minutes = Math.floor( timeDistance / 60000 );
                timeDistance-= msTime.minutes * 60000;
                //是否开启秒表倒计,未完成
                //this.secondsFixed ? msTime.seconds = new Number(timeDistance / 1000).toFixed(2) : msTime.seconds = Math.floor( timeDistance / 1000 ).toFixed(0);
                msTime.seconds = Math.floor( timeDistance / 1000 ).toFixed(0);
                timeDistance-= msTime.seconds * 1000;
                if( msTime.hour < 10){
                    msTime.hour = "0" + msTime.hour;
                }
                if(msTime.minutes < 10){
                    msTime.minutes= "0" + msTime.minutes;
                }
                if(msTime.seconds < 10) {
                    msTime.seconds = "0" + msTime.seconds;
                }
                let _s = Date.now();
                let _e = Date.now();
                let diffPerFunc = _e - _s;
                setTimeout(()=>{
                    if(type){
                        this.runTime(this.end,endTime+=1000,callFun,true);
                    }else{
                        this.runTime(this.star,endTime+=1000,callFun);
                    }
                },1000-diffPerFunc);
            }else{
                callFun();
            }
        },
        start_message () {
            this.$set(this,'tipShow',false);
            this.$emit('start_callback', this.msTime.show);
            setTimeout(()=>{
                this.runTime(this.end,this.star,this.end_message,true)
            },1);
        },
        end_message(){
            this.msTime.show = false;
            this.$emit('end_callback',this.msTime.show);
        }
    },
    mounted: function () {
        //判断是秒还是毫秒
        this.startTime.toString().length==10 ? this.star = this.startTime*1000 : this.star = this.startTime;
        this.endTime.toString().length==10 ? this.end = this.endTime*1000 : this.end = this.endTime;
        if(this.currentTime){
            this.currentTime.toString().length==10 ? this.current = this.currentTime*1000 : this.current = this.currentTime;
        }else{
            this.current=( new Date() ).getTime();
        }
        if(this.end<this.current){
            //结束时间小于当前时间 活动已结束
            this.msTime.show = false;
            this.end_message();
        }else if(this.current<this.star){
            //当前时间小于开始时间 活动尚未开始
            this.$set(this,'tipShow',true);
            setTimeout(()=>{
                this.runTime(this.star,this.current,this.start_message);
            },1);
        }else if(this.end>this.current&&this.star<this.current||this.star==this.current){
            //结束时间大于当前并且开始时间小于当前时间，执行活动开始倒计时
            this.$set(this,'tipShow',false);
            this.msTime.show = true;
            this.$emit('start_callback', this.msTime.show);
            setTimeout(()=>{
                //这里有个bug被作者给坑了（参考链接：https://segmentfault.com/a/1190000016794694?utm_source=tag-newest）
                this.runTime(this.end,this.current,this.end_message,true)
            },1);
        }
    }
});
Vue.component('confirm-build-info',{
    template: '<div class="confirm-build-info" :class="{\'show\':showConfirmBuildInfo}">\n' +
    '    <div class="bg" @click="document.body.classList.remove(\'body-lock\');$emit(\'update:showConfirmBuildInfo\',false);"></div>\n' +
    '    <div class="main">\n' +
    '        <div class="build-info">\n' +
    '            <h2>下单信息</h2>\n' +
    '            <table>\n' +
    '                <tr>\n' +
    '                    <th>客订单号</th>\n' +
    '                    <td>{{form.CusPoNo}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="typeof(group) === \'undefined\' && (build_type === \'s\' || build_type === \'c\')">\n' +
    '                    <th>材质</th>\n' +
    '                    <td>{{form.BoardId}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="build_type === \'c\'">\n' +
    '                    <th>箱型</th>\n' +
    '                    <td>{{form.BoxId}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="build_type === \'s\'">\n' +
    '                    <th>纸板规格(mm)</th>\n' +
    '                    <td>{{form.Length}}&nbsp;x&nbsp;{{form.Width}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="build_type === \'s\'">\n' +
    '                    <th>压线名称</th>\n' +
    '                    <td>{{form.ScoreName}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="build_type === \'s\'">\n' +
    '                    <th>压线信息</th>\n' +
    '                    <td>{{form.ScoreInfo}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="build_type === \'c\'">\n' +
    '                    <th>纸箱规格(mm)</th>\n' +
    '                    <td>{{form.BoxL}}&nbsp;x&nbsp;{{form.BoxW}}&nbsp;x&nbsp;{{form.BoxH}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="build_type === \'c\'">\n' +
    '                    <th>箱舌(mm)</th>\n' +
    '                    <td>{{form.TonLen}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="build_type === \'c\'">\n' +
    '                    <th>封箱调整(mm)</th>\n' +
    '                    <td>{{form.ULen}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="build_type === \'c\'">\n' +
    '                    <th>纸板规格(mm)</th>\n' +
    '                    <td>{{form.Length}}&nbsp;x&nbsp;{{form.Width}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="build_type === \'c\'">\n' +
    '                    <th>张数</th>\n' +
    '                    <td>{{form.BdMultiple}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="build_type === \'x\' || build_type === \'t\'">\n' +
    '                    <th>PO号</th>\n' +
    '                    <td>{{form.PON}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="build_type === \'x\'">\n' +
    '                    <th>套件</th>\n' +
    '                    <td>{{form.ProductId}}</td>\n' +
    '                </tr>\n' +
    '                <tr>\n' +
    '                    <th>订单数</th>\n' +
    '                    <td>{{form.OrdQty}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="build_type === \'c\'">\n' +
    '                    <th>纸板数</th>\n' +
    '                    <td>{{form.BdQty}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="typeof(Area) !== \'undefined\'">\n' +
    '                    <th>下单面积(㎡)</th>\n' +
    '                    <td>{{Area}}</td>\n' +
    '                </tr>\n' +
    '                <tr>\n' +
    '                    <th>送货公司</th>\n' +
    '                    <td>{{form.CusSubNo}}</td>\n' +
    '                </tr>\n' +
    '                <tr>\n' +
    '                    <th>交货日期</th>\n' +
    '                    <td>{{form.DeliveryDate}}</td>\n' +
    '                </tr>\n' +
    '                <tr>\n' +
    '                    <th>送货备注</th>\n' +
    '                    <td>{{form.DNRemark}}</td>\n' +
    '                </tr>\n' +
    '                <tr>\n' +
    '                    <th>生产备注</th>\n' +
    '                    <td>{{form.ProRemark}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="typeof(Cost) !== \'undefined\'">\n' +
    '                    <th>下单金额</th>\n' +
    '                    <td style="color: #e01835;">¥{{Cost}}</td>\n' +
    '                </tr>\n' +
    '                <tr v-if="typeof(SaveCost) !== \'undefined\'">\n' +
    '                    <th>节省金额</th>\n' +
    '                    <td style="color: #e01835;">¥{{SaveCost}}</td>\n' +
    '                </tr>\n' +
    '            </table>\n' +
    '        </div>\n' +
    '        <div class="confirm-btn" @click="document.body.classList.remove(\'body-lock\');$emit(\'update:showConfirmBuildInfo\',false);$emit(\'build\');">确认下单</div>\n' +
    '    </div>\n' +
    '</div>',
    props: ['group','build_type','form','Area','Cost','SaveCost','showConfirmBuildInfo']
});
Vue.component('build-result-fullpage',{
    template: '<div class="build-result-fullpage" v-if="showBuildResult">\n' +
    '    <div class="icon-box" v-if="result">\n' +
    '        <i class="weui-icon-success"></i>\n' +
    '    </div>\n' +
    '    <div class="icon-box" v-else>\n' +
    '        <i class="weui-icon-cancel"></i>\n' +
    '    </div>\n' +
    '    <div class="msg">{{msg}}</div>\n' +
    '    <div class="btn-box">\n' +
    '        <button class="s1" @click="window.location.href = pay_url" v-if="typeof(pay_url) !== \'undefined\' && result">去付款</button>\n' +
    '        <button class="s2" @click="window.location.href = weborder_url" v-if="result">查看微信订单</button>\n' +
    '        <button class="s3" @click="$emit(\'update:showBuildResult\',false)">继续下单</button>\n' +
    '    </div>\n' +
    '</div>',
    props: ['result','msg','pay_url','weborder_url','showBuildResult']
});
