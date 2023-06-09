//vue无缝滚动组件
!
    function(t, i) {
        "object" == typeof exports && "object" == typeof module ? module.exports = i() : "function" == typeof define && define.amd ? define([], i) : "object" == typeof exports ? exports.vueSeamlessScroll = i() : t.vueSeamlessScroll = i()
    }("undefined" != typeof self ? self : this, function() {
        return function(t) {
            function i(o) {
                if (e[o]) return e[o].exports;
                var s = e[o] = {
                    i: o,
                    l: !1,
                    exports: {}
                };
                return t[o].call(s.exports, s, s.exports, i), s.l = !0, s.exports
            }
            var e = {};
            return i.m = t, i.c = e, i.d = function(t, e, o) {
                i.o(t, e) || Object.defineProperty(t, e, {
                    configurable: !1,
                    enumerable: !0,
                    get: o
                })
            }, i.n = function(t) {
                var e = t && t.__esModule ?
                    function() {
                        return t.
                            default
                    } : function() {
                        return t
                    };
                return i.d(e, "a", e), e
            }, i.o = function(t, i) {
                return Object.prototype.hasOwnProperty.call(t, i)
            }, i.p = "", i(i.s = 0)
        }([function(t, i, e) {
            "use strict";
            Object.defineProperty(i, "__esModule", {
                value: !0
            });
            var o = e(1),
                s = function(t) {
                    return t && t.__esModule ? t : {
                        default:
                        t
                    }
                }(o);
            s.
                default.install = function(t) {
                var i = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {};
                t.component(i.componentName || s.
                    default.name, s.
                    default)
            }, "undefined" != typeof window && window.Vue && Vue.component(s.
                default.name, s.
                default), i.
                default = s.
                default
        }, function(t, i, e) {
            var o = e(2)(e(3), e(7), null, null);
            t.exports = o.exports
        }, function(t, i) {
            t.exports = function(t, i, e, o) {
                var s, n = t = t || {},
                    r = typeof t.
                        default;
                "object" !== r && "function" !== r || (s = t, n = t.
                    default);
                var a = "function" == typeof n ? n.options:
                    n;
                if (i && (a.render = i.render, a.staticRenderFns = i.staticRenderFns), e && (a._scopeId = e), o) {
                    var h = Object.create(a.computed || null);
                    Object.keys(o).forEach(function(t) {
                        var i = o[t];
                        h[t] = function() {
                            return i
                        }
                    }), a.computed = h
                }
                return {
                    esModule: s,
                    exports: n,
                    options: a
                }
            }
        }, function(t, i, e) {
            "use strict";
            Object.defineProperty(i, "__esModule", {
                value: !0
            }), e(4)();
            var o = e(5),
                s = e(6);
            i.
                default = {
                name: "vue-seamless-scroll",
                data: function() {
                    return {
                        xPos: 0,
                        yPos: 0,
                        delay: 0,
                        copyHtml: "",
                        height: 0,
                        width: 0,
                        realBoxWidth: 0,
                        reqFrame: null,
                        singleWaitTime: null,
                        isHover: !1
                    }
                },
                props: {
                    data: {
                        type: Array,
                        default:


                            function() {
                                return []
                            }
                    },
                    classOption: {
                        type: Object,
                        default:


                            function() {
                                return {}
                            }
                    }
                },
                computed: {
                    leftSwitchState: function() {
                        return this.xPos < 0
                    },
                    rightSwitchState: function() {
                        return Math.abs(this.xPos) < this.realBoxWidth - this.width
                    },
                    leftSwitchClass: function() {
                        return this.leftSwitchState ? "" : this.options.switchDisabledClass
                    },
                    rightSwitchClass: function() {
                        return this.rightSwitchState ? "" : this.options.switchDisabledClass
                    },
                    leftSwitch: function() {
                        return {
                            position: "absolute",
                            margin: this.height / 2 + "px 0 0 -" + this.options.switchOffset + "px",
                            transform: "translate(-100%,-50%)"
                        }
                    },
                    rightSwitch: function() {
                        return {
                            position: "absolute",
                            margin: this.height / 2 + "px 0 0 " + (this.width + this.options.switchOffset) + "px",
                            transform: "translateY(-50%)"
                        }
                    },
                    float: function() {
                        return this.isHorizontal ? {
                            float: "left",
                            overflow: "hidden"
                        } : {
                            overflow: "hidden"
                        }
                    },
                    pos: function() {
                        return {
                            transform: "translate(" + this.xPos + "px," + this.yPos + "px)",
                            transition: "all " + (this.ease || "ease-in") + " " + this.delay + "ms",
                            overflow: "hidden"
                        }
                    },
                    defaultOption: function() {
                        return {
                            step: 0.4,//步长 越大滚动速度越快
                            limitMoveNum: 7,//无缝滚动最小数据量
                            hoverStop: 1,//是否启用鼠标hover控制
                            direction: 1,//1 往上 0 往下
                            openTouch: 0,//能否滑动列表
                            singleHeight: 0,
                            singleWidth: 0,
                            waitTime: 1000,//单步停止等待时间
                            switchOffset: 30,
                            autoPlay: 1,
                            switchSingleStep: 134,
                            switchDelay: 400,
                            switchDisabledClass: "disabled",
                            isSingleRemUnit: 0
                        }
                    },
                    options: function() {
                        return s({}, this.defaultOption, this.classOption)
                    },
                    moveSwitch: function() {
                        return this.data.length < this.options.limitMoveNum
                    },
                    hoverStop: function() {
                        return !this.options.autoPlay || !this.options.hoverStop || this.moveSwitch
                    },
                    canNotTouch: function() {
                        return !this.options.openTouch || !this.options.autoPlay
                    },
                    isHorizontal: function() {
                        return this.options.direction > 1 || !this.options.autoPlay
                    },
                    baseFontSize: function() {
                        return this.options.isSingleRemUnit ? parseInt(window.getComputedStyle(document.documentElement, null).fontSize) : 1
                    },
                    realSingleStopWidth: function() {
                        return this.options.singleWidth * this.baseFontSize
                    },
                    realSingleStopHeight: function() {
                        return this.options.singleHeight * this.baseFontSize
                    }
                },
                methods: {
                    leftSwitchClick: function() {
                        if (this.leftSwitchState) return Math.abs(this.xPos) < this.options.switchSingleStep ? void(this.xPos = 0) : void(this.xPos += this.options.switchSingleStep)
                    },
                    rightSwitchClick: function() {
                        if (this.rightSwitchState) return this.realBoxWidth - this.width + this.xPos < this.options.switchSingleStep ? void(this.xPos = this.width - this.realBoxWidth) : void(this.xPos -= this.options.switchSingleStep)
                    },
                    _cancle: function() {
                        cancelAnimationFrame(this.reqFrame || "")
                    },
                    touchStart: function(t) {
                        var i = this;
                        if (!this.canNotTouch) {
                            var e = void 0,
                                o = t.targetTouches[0];
                            this.startPos = {
                                x: o.pageX,
                                y: o.pageY
                            }, this.startPosY = this.yPos, this.startPosX = this.xPos, this.options.singleHeight && this.options.singleWidth ? (e && clearTimeout(e), e = setTimeout(function() {
                                i._cancle()
                            }, this.options.waitTime + 20)) : this._cancle()
                        }
                    },
                    touchMove: function(t) {
                        if (!(this.canNotTouch || t.targetTouches.length > 1 || t.scale && 1 !== t.scale)) {
                            var i = t.targetTouches[0];
                            this.endPos = {
                                x: i.pageX - this.startPos.x,
                                y: i.pageY - this.startPos.y
                            }, event.preventDefault();
                            var e = Math.abs(this.endPos.x) < Math.abs(this.endPos.y) ? 1 : 0;
                            1 === e && this.options.direction < 2 ? this.yPos = this.startPosY + this.endPos.y : 0 === e && this.options.direction > 1 && (this.xPos = this.startPosX + this.endPos.x)
                        }
                    },
                    touchEnd: function() {
                        var t = this;
                        if (!this.canNotTouch) {
                            var i = void 0,
                                e = this.options.direction;
                            if (this.delay = 50, 1 === e) this.yPos > 0 && (this.yPos = 0);
                            else if (0 === e) {
                                var o = this.$refs.realBox.offsetHeight / 2 * -1;
                                this.yPos < o && (this.yPos = o)
                            } else if (2 === e) this.xPos > 0 && (this.xPos = 0);
                            else if (3 === e) {
                                var s = -1 * this.$refs.slotList.offsetWidth;
                                this.xPos < s && (this.xPos = s)
                            }
                            i && clearTimeout(i), i = setTimeout(function() {
                                t.delay = 0, t._move()
                            }, this.delay)
                        }
                    },
                    enter: function() {
                        this.hoverStop || (this.isHover = !0, this.singleWaitTime && clearTimeout(this.singleWaitTime), this._cancle())
                    },
                    leave: function() {
                        this.hoverStop || (this.isHover = !1, this._move())
                    },
                    _move: function() {
                        this.isHover || (this._cancle(), this.reqFrame = requestAnimationFrame(function() {
                            var t = this;
                            if (this.$refs.realBox) {
                                var i = this.$refs.realBox.offsetHeight / 2,
                                    e = this.$refs.slotList.offsetWidth,
                                    o = this.options.direction;
                                1 === o ? (Math.abs(this.yPos) >= i && (this.yPos = 0), this.yPos -= this.options.step) : 0 === o ? (this.yPos >= 0 && (this.yPos = -1 * i), this.yPos += this.options.step) : 2 === o ? (Math.abs(this.xPos) >= e && (this.xPos = 0), this.xPos -= this.options.step) : 3 === o && (this.xPos >= 0 && (this.xPos = -1 * e), this.xPos += this.options.step), this.singleWaitTime && clearTimeout(this.singleWaitTime), this.realSingleStopHeight ? Math.abs(this.yPos) % this.realSingleStopHeight < 1 ? this.singleWaitTime = setTimeout(function() {
                                    t._move()
                                }, this.options.waitTime) : this._move() : this.realSingleStopWidth && Math.abs(this.xPos) % this.realSingleStopWidth < 1 ? this.singleWaitTime = setTimeout(function() {
                                    t._move()
                                }, this.options.waitTime) : this._move()
                            }
                        }.bind(this)))
                    },
                    _initMove: function() {
                        var t = this;
                        this.$nextTick(function() {
                            if (t.height = t.$refs.wrap.offsetHeight, t.width = t.$refs.wrap.offsetWidth, t.isHorizontal) {
                                var i = void 0;
                                i = t.options.autoPlay ? 2 : 1;
                                var e = t.$refs.slotList.offsetWidth * i;
                                t.$refs.realBox.style.width = e + "px", t.realBoxWidth = e
                            }
                            if (!t.options.autoPlay) return t.ease = "linear", void(t.delay = t.options.switchDelay);
                            if (t._dataWarm(t.data), t.copyHtml = "", t.moveSwitch) t._cancle(), t.yPos = t.xPos = 0;
                            else {
                                t.copyHtml = t.$refs.slotList.innerHTML, t._move()
                            }
                        })
                    },
                    _dataWarm: function(t) {
                        t.length
                    }
                },
                mounted: function() {
                    this._initMove()
                },
                watch: {
                    data: function(t, i) {
                        this._dataWarm(t), o(t, i) || (this._cancle(), this._initMove())
                    }
                },
                beforeDestroy: function() {
                    this._cancle()
                }
            }
        }, function(t, i) {
            var e = function() {
                window.cancelAnimationFrame = function() {
                    return window.cancelAnimationFrame || window.webkitCancelAnimationFrame || window.mozCancelAnimationFrame || window.oCancelAnimationFrame || window.msCancelAnimationFrame ||
                        function(t) {
                            return window.clearTimeout(t)
                        }
                }(), window.requestAnimationFrame = function() {
                    return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame ||
                        function(t) {
                            return window.setTimeout(t, 1000 / 60)
                        }
                }()
            };
            t.exports = e
        }, function(t, i) {
            var e = function(t, i) {
                if (t === i) return !0;
                if (t.length !== i.length) return !1;
                for (var e = 0; e < t.length; ++e) if (t[e] !== i[e]) return !1;
                return !0
            };
            t.exports = e
        }, function(t, i) {
            function e() {
                Array.isArray || (Array.isArray = function(t) {
                    return "[object Array]" === Object.prototype.toString.call(t)
                });
                var t = void 0,
                    i = void 0,
                    s = void 0,
                    n = void 0,
                    r = void 0,
                    a = void 0,
                    h = 1,
                    l = arguments[0] || {},
                    c = !1,
                    u = arguments.length;
                if ("boolean" == typeof l && (c = l, l = arguments[1] || {}, h++), "object" !== (void 0 === l ? "undefined" : o(l)) && "function" != typeof l && (l = {}), h === u) return l;
                for (; h < u; h++) if (null != (i = arguments[h])) for (t in i) s = l[t], n = i[t], r = Array.isArray(n), c && n && ("object" === (void 0 === n ? "undefined" : o(n)) || r) ? (r ? (r = !1, a = s && Array.isArray(s) ? s : []) : a = s && "object" === (void 0 === s ? "undefined" : o(s)) ? s : {}, l[t] = e(c, a, n)) : void 0 !== n && (l[t] = n);
                return l
            }
            var o = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ?
                function(t) {
                    return typeof t
                } : function(t) {
                    return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t
                };
            t.exports = e
        }, function(t, i) {
            t.exports = {
                render: function() {
                    var t = this,
                        i = t.$createElement,
                        e = t._self._c || i;
                    return e("div", {
                        ref: "wrap"
                    }, [t.isHorizontal ? e("div", {
                        class: t.leftSwitchClass,
                        style: t.leftSwitch,
                        on: {
                            click: t.leftSwitchClick
                        }
                    }, [t._t("left-switch")], 2) : t._e(), t._v(" "), t.isHorizontal ? e("div", {
                        class: t.rightSwitchClass,
                        style: t.rightSwitch,
                        on: {
                            click: t.rightSwitchClick
                        }
                    }, [t._t("right-switch")], 2) : t._e(), t._v(" "), e("div", {
                        ref: "realBox",
                        style: t.pos,
                        on: {
                            mouseenter: t.enter,
                            mouseleave: t.leave,
                            touchstart: t.touchStart,
                            touchmove: t.touchMove,
                            touchend: t.touchEnd
                        }
                    }, [e("div", {
                        ref: "slotList",
                        style: t.float
                    }, [t._t("default")], 2), t._v(" "), e("div", {
                        style: t.float,
                        domProps: {
                            innerHTML: t._s(t.copyHtml)
                        }
                    })])])
                },
                staticRenderFns: []
            }
        }]).
            default
    });