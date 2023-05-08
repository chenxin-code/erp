<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link href="/erp/res/<?php echo ($config['FactoryLogo']); ?>" rel="shortcut icon">
    <title><?php echo ($LayoutTitle); ?></title>
    <script src="/erp/res/vue.js"></script>
    <!-- jQuery -->
    <script src="/erp/res/jquery.min.js"></script>
    <!-- SweetAlert2 插件 -->
    <link rel="stylesheet" href="/erp/res/SweetAlert2/sweetalert2.min.css">
    <script src="/erp/res/SweetAlert2/sweetalert2.min.js"></script>
    <!-- ElementUI -->
    <link rel="stylesheet" href="/erp/res/ElementUI/index.css">
    <script src="/erp/res/ElementUI/index.js"></script>
    <!-- 阿里图标cdn -->
    <link rel="stylesheet" href="<?php echo C('ali_iconfont_cdn');?>">
    <!-- 函数库 -->
    <script src="/erp/res/function.js?time=<?php echo time();?>"></script>
    <!-- 后台样式 -->
    <link rel="stylesheet" href="/erp/res/admin.css?time=<?php echo time();?>">
</head>
<body class="admin-common">
    <div class="sidebar">
        <div style="margin: 0 0 0 9px;">
            <h2 style="padding: 40px 0 10px 0;color: #222;font-size: 26px;font-weight: bold;text-align: center;">
                <a>后台管理</a>
            </h2>
            <div class="top-container">
                欢迎您，
                <a><?php echo session('ERP_Admin_User.UserName');?></a>
                <br>
                <a style="cursor: pointer;" @click="clean()">清除缓存</a> |
                <a href="<?php echo U('Index/logout');?>">退出</a>
            </div>
            <script>
                new Vue({
                    el: '.top-container',
                    methods: {
                        clean: function () {
                            $.ajax({
                                url: '<?php echo U('Index/clean_api');?>',
                                success: function (respon) {
                                    var respon = eval('(' + respon + ')');
                                    if(respon.ret === '<?php echo C('succ_ret');?>'){
                                        swal({type: 'success',title: respon.msg});
                                    }else{
                                        swal({type: 'error',title: respon.msg});
                                    }
                                }
                            });
                        }
                    }
                });
            </script>
            <ul class="menu-container">
                <li v-for="v1 in menu1">
                    <a :href="v1.href1" class="nav-top-item no-submenu" :class="{'current':v1.href1 === CurHref}" v-if="v1.href1">
                        {{v1.title1}}
                    </a>
                    <a class="nav-top-item" :class="{'current':JSON.stringify(v1.menu2).indexOf(CurHref) !== -1}" v-else>
                        {{v1.title1}}
                    </a>
                    <ul v-if="v1.menu2">
                        <li v-for="v2 in v1.menu2">
                            <a :href="v2.href2" :class="{'current':v2.href2 === CurHref}">
                                {{v2.title2}}
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <script>
                new Vue({
                    el: '.menu-container',
                    data: {
                        menu1: [
                            {
                                title1: '项目配置',
                                href1: '<?php echo U('Index/config');?>'
                            },
                            {
                                title1: '图片配置',
                                href1: '<?php echo U('Index/imgConfig');?>'
                            },
                            {
                                title1: '联系方式管理',
                                menu2: [
                                    {
                                        title2: '联系方式列表',
                                        href2: '<?php echo U('Contact/lists');?>'
                                    },
                                    {
                                        title2: '添加联系方式',
                                        href2: '<?php echo U('Contact/add');?>'
                                    }
                                ]
                            },
                            {
                                title1: '后台用户管理',
                                menu2: [
                                    {
                                        title2: '用户列表',
                                        href2: '<?php echo U('User/lists');?>'
                                    },
                                    {
                                        title2: '添加用户',
                                        href2: '<?php echo U('User/add');?>'
                                    }
                                ]
                            }
                        ].concat(Number('<?php echo ($config['UseBoardGroup']); ?>')?[
                            {
                                title1: '团购纸板管理',
                                menu2: [
                                    {
                                        title2: '纸板列表',
                                        href2: '<?php echo U('Board/lists');?>'
                                    },
                                    {
                                        title2: '添加纸板',
                                        href2: '<?php echo U('Board/add');?>'
                                    },
                                    {
                                        title2: '纸板默认图片',
                                        href2: '<?php echo U('Board/DefaultPic');?>'
                                    },
                                    {
                                        title2: '已删除纸板列表',
                                        href2: '<?php echo U('Board/delLists');?>'
                                    }
                                ]
                            }
                        ]:[]).concat(Number('<?php echo ($config['UseBoxGroup']); ?>')?[
                            {
                                title1: '团购淘宝箱管理',
                                menu2: [
                                    {
                                        title2: '淘宝箱列表',
                                        href2: '<?php echo U('Box/lists');?>'
                                    },
                                    {
                                        title2: '添加淘宝箱',
                                        href2: '<?php echo U('Box/add');?>'
                                    },
                                    {
                                        title2: '淘宝箱默认图片',
                                        href2: '<?php echo U('Box/DefaultPic');?>'
                                    },
                                    {
                                        title2: '已删除淘宝箱列表',
                                        href2: '<?php echo U('Box/delLists');?>'
                                    }
                                ]
                            }
                        ]:[]),
                        CurHref: '<?php echo U();?>'
                    },
                    mounted: function () {
                        $('.menu-container li ul').hide();
                        $('.menu-container li a.current').parent().find('ul').slideToggle('slow');
                        $('.menu-container li a.nav-top-item').click(
                            function () {
                                $(this).parent().siblings().find('ul').slideUp('normal');
                                $(this).next().slideToggle('normal');
                                return false;
                            }
                        );
                        $('.menu-container li a.no-submenu').click(
                            function () {
                                window.location.href = this.href;
                                return false;
                            }
                        );
                        $('.menu-container li .nav-top-item').hover(
                            function () {
                                $(this).stop().animate({paddingRight: '25px'},200);
                            },
                            function () {
                                $(this).stop().animate({paddingRight: '15px'});
                            }
                        );
                    }
                });
            </script>
        </div>
    </div>
    <div class="main-content">
        <style>
    .el-tabs__item.is-active {
        color: #1a991d;
    }
    .el-tabs__item:hover {
        color: #1a991d;
    }
    .el-tabs__active-bar {
        background-color: #1a991d;
    }
    /***********************************/
    .avatar-uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        overflow: hidden;
        position: relative;
    }
    .avatar-uploader .el-upload:hover {
        border-color: #409eff;
    }
    .avatar-uploader-icon {
        width: 178px;
        height: 178px;
        line-height: 178px;
        font-size: 28px;
        color: #8c939d;
        text-align: center;
    }
    .avatar {
        width: 178px;
        height: 178px;
        display: block;
    }
</style>

<div id="VueBox">
    <el-tabs v-model="module">
        <el-tab-pane label="厂商logo" name="厂商logo">
            <form>
                <table>
                    <tbody>
                    <tr>
                        <td>厂商logo</td>
                        <td>
                            <el-upload class="avatar-uploader" action="<?php echo U('setFactoryLogo_api');?>" name="pic" :on-success="OnSuccessFactoryLogo" :show-file-list="false">
                                <img class="avatar" :src="FactoryLogoSrc" v-if="FactoryLogoSrc">
                                <i class="el-icon-plus avatar-uploader-icon" v-else></i>
                            </el-upload>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </el-tab-pane>
        <el-tab-pane label="首页图片" name="首页图片">
            <form>
                <table>
                    <tbody>
                    <tr>
                        <td>广告</td>
                        <td>
                            <el-upload action="<?php echo U('addAdverPic_api');?>" name="pic" :on-preview="OnPreview" :on-remove="OnRemove" list-type="picture-card" :file-list="AdverPic">
                                <i class="el-icon-plus"></i>
                            </el-upload>
                            <el-dialog :visible.sync="elDialogVisible">
                                <img :src="elDialogImgSrc" width="100%">
                            </el-dialog>
                        </td>
                    </tr>
                    <tr v-if="UseBoardGroup">
                        <td>纸板团购</td>
                        <td>
                            <el-upload class="avatar-uploader" action="<?php echo U('setBoardGroupPic_api');?>" name="pic" :on-success="OnSuccessBoardGroupPic" :show-file-list="false">
                                <img class="avatar" :src="BoardGroupPicSrc" v-if="BoardGroupPicSrc">
                                <i class="el-icon-plus avatar-uploader-icon" v-else></i>
                            </el-upload>
                        </td>
                    </tr>
                    <tr v-if="UseBoardGroup">
                        <td>纸板团购（<?php echo ($config['BoardFlag']); ?>）</td>
                        <td>
                            <el-upload class="avatar-uploader" action="<?php echo U('setFlagBoardGroupPic_api');?>" name="pic" :on-success="OnSuccessFlagBoardGroupPic" :show-file-list="false">
                                <img class="avatar" :src="FlagBoardGroupPicSrc" v-if="FlagBoardGroupPicSrc">
                                <i class="el-icon-plus avatar-uploader-icon" v-else></i>
                            </el-upload>
                        </td>
                    </tr>
                    <tr v-if="UseBoxGroup">
                        <td>淘宝箱团购</td>
                        <td>
                            <el-upload class="avatar-uploader" action="<?php echo U('setBoxGroupPic_api');?>" name="pic" :on-success="OnSuccessBoxGroupPic" :show-file-list="false">
                                <img class="avatar" :src="BoxGroupPicSrc" v-if="BoxGroupPicSrc">
                                <i class="el-icon-plus avatar-uploader-icon" v-else></i>
                            </el-upload>
                        </td>
                    </tr>
                    <tr v-if="UseBoxGroup">
                        <td>淘宝箱团购（<?php echo ($config['BoxFlag']); ?>）</td>
                        <td>
                            <el-upload class="avatar-uploader" action="<?php echo U('setFlagBoxGroupPic_api');?>" name="pic" :on-success="OnSuccessFlagBoxGroupPic" :show-file-list="false">
                                <img class="avatar" :src="FlagBoxGroupPicSrc" v-if="FlagBoxGroupPicSrc">
                                <i class="el-icon-plus avatar-uploader-icon" v-else></i>
                            </el-upload>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </el-tab-pane>
    </el-tabs>
</div>

<script>
    new Vue({
        el: '#VueBox',
        data: {
            module: '厂商logo',
            FactoryLogoSrc: null,
            AdverPic: [],
            elDialogVisible: false,
            elDialogImgSrc: null,
            BoardGroupPicSrc: null,
            FlagBoardGroupPicSrc: null,
            BoxGroupPicSrc: null,
            FlagBoxGroupPicSrc: null,
            UseBoardGroup: Number('<?php echo ($config['UseBoardGroup']); ?>'),
            UseBoxGroup: Number('<?php echo ($config['UseBoxGroup']); ?>')
        },
        methods: {
            OnSuccessFactoryLogo: function (res,file) {
                this.FactoryLogoSrc = URL.createObjectURL(file.raw);
            },
            OnPreview: function (file) {
                this.elDialogImgSrc = file.url;
                this.elDialogVisible = true;
            },
            OnRemove: function (file) {
                $.ajax({
                    url: '<?php echo U('delAdverPic_api');?>',
                    type: 'get',
                    data: {Pic: file.name}
                });
            },
            OnSuccessBoardGroupPic: function (res,file) {
                this.BoardGroupPicSrc = URL.createObjectURL(file.raw);
            },
            OnSuccessFlagBoardGroupPic: function (res,file) {
                this.FlagBoardGroupPicSrc = URL.createObjectURL(file.raw);
            },
            OnSuccessBoxGroupPic: function (res,file) {
                this.BoxGroupPicSrc = URL.createObjectURL(file.raw);
            },
            OnSuccessFlagBoxGroupPic: function (res,file) {
                this.FlagBoxGroupPicSrc = URL.createObjectURL(file.raw);
            }
        },
        mounted: function () {
            var _this = this;
            $.ajax({
                url: '<?php echo U('showFactoryLogo_api');?>',
                success: function (respon) {
                    _this.FactoryLogoSrc = '/erp/res/' + eval('(' + respon + ')');
                }
            });
            $.ajax({
                url: '<?php echo U('showAdverPic_api');?>',
                success: function (respon) {
                    var respon = eval('(' + respon + ')');
                    $.each(respon,function (k,v) {
                        _this.AdverPic.push({name: v,url: '/erp/res/' + v});
                    });
                }
            });
            if(_this.UseBoardGroup){
                $.ajax({
                    url: '<?php echo U('showBoardGroupPic_api');?>',
                    success: function (respon) {
                        _this.BoardGroupPicSrc = '/erp/res/' + eval('(' + respon + ')');
                    }
                });
                $.ajax({
                    url: '<?php echo U('showFlagBoardGroupPic_api');?>',
                    success: function (respon) {
                        _this.FlagBoardGroupPicSrc = '/erp/res/' + eval('(' + respon + ')');
                    }
                });
            }
            if(_this.UseBoxGroup){
                $.ajax({
                    url: '<?php echo U('showBoxGroupPic_api');?>',
                    success: function (respon) {
                        _this.BoxGroupPicSrc = '/erp/res/' + eval('(' + respon + ')');
                    }
                });
                $.ajax({
                    url: '<?php echo U('showFlagBoxGroupPic_api');?>',
                    success: function (respon) {
                        _this.FlagBoxGroupPicSrc = '/erp/res/' + eval('(' + respon + ')');
                    }
                });
            }
        }
    });
</script>

    </div>
</body>
</html>