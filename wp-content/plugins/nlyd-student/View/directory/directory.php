<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper have-footer">
            <header class="mui-bar mui-bar-nav layui-bg-white">
                <div class="search-zoo">
                    <i class="iconfont search-Icon">&#xe63b;</i>
                    <input type="text" class="serach-Input nl-foucs" placeholder="<?=__('搜索名录/课程/教练等', 'nlyd-student')?>">
                </div>
            </header>
            <div class="layui-row nl-border nl-content  layui-bg-white">
                <!-- 头部导航 -->
                <div class="layui-row width-padding">
                    <div class="top-nav">
                        <div class="top-nav-btn"><a class="fs_16 c_black6" href="<?=home_url('/student/index');?>"><?=__('首 页', 'nlyd-student')?></a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_black6 disabled_a"><?=__('我们', 'nlyd-student')?></a></div>
                        <div class="top-nav-btn active"><a class="fs_16 c_blue"  href="<?=home_url('/directory/');?>"><?=__('名 录', 'nlyd-student')?></a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_black6"  href="<?=home_url('/courses/');?>"><?=__('课 程', 'nlyd-student')?></a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_black6 disabled_a" href="<?=home_url('shops');?>"><?=__('商 城', 'nlyd-student')?></a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_black6 disabled_a"><?=__('公 益', 'nlyd-student')?></a></div>
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
                                        <p><?=__('认证教练名录', 'nlyd-student')?></p>
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
                                        <p><?=__('脑力健将名录', 'nlyd-student')?></p>
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
                                        <p><?=__('脑力战队名录', 'nlyd-student')?></p>
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
                                        <p><?=__('记忆水平认证名录', 'nlyd-student')?></p>
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
                                        <p><?=__('速读水平认证名录', 'nlyd-student')?></p>
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
                                        <p><?=__('心算水平认证名录', 'nlyd-student')?></p>
                                        <p>MENTAL LEVEL</p> 
                                    </div>
                                </div>
                                <div class="right-icon">
                                    <i class="iconfont">&#xe640;</i>
                                </div>
                            </div>  
                        </a>
                    </li>
                    <li>
                        <a  class="system-list system-match small" href="<?=home_url('directory/directoryZone');?>">
                            <div class="item-wrapper">
                                <div class="left-font">
                                    07
                                </div>
                                <div class="center-detail">
                                    <div class="system-font">
                                        <p><?=__('赛区授权信息', 'nlyd-student')?></p>
                                        <p>AUTHORITY</p> 
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