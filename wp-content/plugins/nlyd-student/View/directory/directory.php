<!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
<!--[if lt IE 9]>
  <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
  <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper have-footer">
            <header class="mui-bar mui-bar-nav layui-bg-white">
                <div class="search-zoo">
                    <i class="iconfont search-Icon">&#xe63b;</i>
                    <input type="text" class="serach-Input nl-foucs" placeholder="搜索名录/课程/教练等">
                </div>
            </header>
            <div class="layui-row nl-border nl-content  layui-bg-white">
                <!-- 头部导航 -->
                <div class="layui-row width-padding">
                    <div class="top-nav">
                        <div class="top-nav-btn"><a class="fs_16 c_black6" href="<?=home_url('/student/index');?>">首 页</a></div>
                        <div class="top-nav-btn active"><a class="fs_16 c_blue"  href="<?=home_url('directory');?>">名 录</a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_black6">课 程</a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_black6" href="<?=home_url('shops');?>">商 城</a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_black6">公 益</a></div>
                    </div>
                </div>
               
                <div class="layui-row width-padding  layui-bg-white">
                    <ul style="margin:0">
                    <li>
                        <a class="system-list system-course small" href="<?=home_url('teams/coachList/directory/1');?>">
                            <div class="item-wrapper">
                                <div class="left-font">
                                    01
                                </div>
                                <div class="center-detail">
                                    <div class="system-font">
                                        <p>认证教练名录</p>
                                        <p>BRAIN COACH</p> 
                                    </div>
                                </div>
                                <div class="right-icon">
                                    <i class="iconfont">&#xe640;</i>
                                </div>
                            </div>  
                        </a>
                    </li>
                    <li>
                        <a  class="system-list system-teacher small" href="<?=home_url('directory/directoryPlayer');?>">
                            <div class="item-wrapper">
                                <div class="left-font">
                                    02
                                </div>
                                <div class="center-detail">
                                    <div class="system-font">
                                        <p>脑力健将名录</p>
                                        <p>BRAIN POWER</p> 
                                    </div>
                                </div>
                                <div class="right-icon">
                                    <i class="iconfont">&#xe640;</i>
                                </div>
                            </div>  
                        </a>
                    </li>
                    <li>
                        <a  class="system-list system-match small" href="<?=home_url('teams');?>">
                            <div class="item-wrapper">
                                <div class="left-font">
                                    03
                                </div>
                                <div class="center-detail">
                                    <div class="system-font">
                                        <p>脑力战队名录</p>
                                        <p>BRAIN TEAM</p> 
                                    </div>
                                </div>
                                <div class="right-icon">
                                    <i class="iconfont">&#xe640;</i>
                                </div>
                            </div>  
                        </a>
                    </li>
                    <li>
                        <a  class="system-list system-test small" href="<?=home_url('directory/directoryRemember');?>">
                            <div class="item-wrapper">
                                <div class="left-font">
                                    04
                                </div>
                                <div class="center-detail">
                                    <div class="system-font">
                                        <p>记忆水平认证名录</p>
                                        <p>BRAIN COALEVELCH</p> 
                                    </div>
                                </div>
                                <div class="right-icon">
                                    <i class="iconfont">&#xe640;</i>
                                </div>
                            </div>  
                        </a>
                    </li>
                    <li>
                        <a  class="system-list system-course small" href="<?=home_url('directory/directoryRead');?>">
                            <div class="item-wrapper">
                                <div class="left-font">
                                    05
                                </div>
                                <div class="center-detail">
                                    <div class="system-font">
                                        <p>速读水平认证名录</p>
                                        <p>SOEED READING</p> 
                                    </div>
                                </div>
                                <div class="right-icon">
                                    <i class="iconfont">&#xe640;</i>
                                </div>
                            </div>  
                        </a>
                    </li>
                    <li>
                        <a  class="system-list system-teacher small" href="<?=home_url('directory/directorycalculation');?>">
                            <div class="item-wrapper">
                                <div class="left-font">
                                    06
                                </div>
                                <div class="center-detail">
                                    <div class="system-font">
                                        <p>心算水平认证名录</p>
                                        <p>MENTAL LEVEL</p> 
                                    </div>
                                </div>
                                <div class="right-icon">
                                    <i class="iconfont">&#xe640;</i>
                                </div>
                            </div>  
                        </a>
                    </li>
                </ul>
                </div>



            </div>
        </div>
    </div>
</div>