<style>
@media screen and (max-width: 1199px){
    #page {
        top: 130px;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper have-footer">
            <div class="layui-row nl-border nl-content">
                    <header class="mui-bar mui-bar-nav system-list system-teacher layui-tab layui-tab-brief" lay-filter="tabs">
                        <a class="mui-pull-left nl-goback">
                            <i class="iconfont">&#xe610;</i>
                        </a>
                        <div class="item-wrapper">
                            <div class="center-detail">
                                <div class="system-font">
                                    <p><?=__('国际脑力运动师资体系', 'nlyd-student')?></p>
                                    <p>IISC RESOURCES</p> 
                                </div>
                            </div>
                        </div>  
                        <ul class="layui-tab-title width-left  width-left-pc">
                            <li class="layui-this"><?=__('认证教练', 'nlyd-student')?></li>
                            <li><?=__('基地教练', 'nlyd-student')?></li>
                            <li><?=__('认证记忆导师', 'nlyd-student')?></li>
                            <div class="nl-transform"><?=__('认证教练', 'nlyd-student')?></div>
                        </ul>
                    </header>
                    <div class="layui-tab-content width-margin width-margin-pc">
                        <div class="layui-tab-item layui-show">
                            <ul class="flow-default layui-row" id="flow-teacher1">
                                <li class="layui-col-lg4 layui-col-md12 layui-col-sm12 layui-col-xs12">
                                    <a class="coach-row">
                                        <div class="coach-picture">
                                        <img src="<?=student_css_url.'image/noInfo/404x3.png'?>" >
                                        </div>
                                        <div class="coach-detail">
                                            <div class="coach-detail-row">
                                                <span class="coach-name">陈卫东</span>
                                                <span class="coach-info light-c">男</span>
                                                <span class="coach-info light-c">ID 10000009</span>
                                            </div>
                                            <div class="coach-detail-row">
                                                <span class="coach-info"><?=__('国际脑力运动委员会', 'nlyd-student')?>（IISC）</span>
                                            </div>
                                            <div class="coach-detail-row">
                                                <span class="coach-info"><?=__('高级教练', 'nlyd-student')?></span>
                                            </div>
                                            <div class="right-icon">
                                                <i class="iconfont">&#xe640;</i>
                                            </div>
                                        </div>
                                    </a>
                                </li>

                                <li class="layui-col-lg4 layui-col-md12 layui-col-sm12 layui-col-xs12">
                                    <a class="coach-row">
                                        <div class="coach-picture">
                                            <img src="<?=student_css_url.'image/test/test1.png'?>">
                                        </div>
                                        <div class="coach-detail">
                                            <div class="coach-detail-row">
                                                <span class="coach-name">陈卫东</span>
                                                <span class="coach-info light-c">男</span>
                                                <span class="coach-info light-c">ID 10000009</span>
                                            </div>
                                            <div class="coach-detail-row">
                                                <span class="coach-info">国际脑力运动委员会（IISC）</span>
                                            </div>
                                            <div class="coach-detail-row">
                                                <span class="coach-info">高级教练</span>
                                            </div>
                                            <div class="right-icon">
                                                <i class="iconfont">&#xe640;</i>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="layui-tab-item">
                            <ul class="flow-default layui-row" id="flow-teacher2">
                                <li class="base-coach">
                                    <div class="coach-top-img img-box">
                                        <img src="<?=student_css_url.'image/test/test1.png'?>" >
                                    </div>
                                    <div class="coach-bottom-info"><span>陈卫东</span> <span class="coach-cop">河南基地</span></div>
                                </li>
                                <li class="base-coach">
                                    <div class="coach-top-img img-box">
                                        <img src="<?=student_css_url.'image/test/test1.png'?>" >
                                    </div>
                                    <div class="coach-bottom-info"><span>陈卫东</span> <span class="coach-cop">河南基地</span></div>
                                </li>
                                <li class="base-coach">
                                    <div class="coach-top-img img-box">
                                        <img src="<?=student_css_url.'image/noInfo/404x3.png'?>" >
                                    </div>
                                    <div class="coach-bottom-info"><span>陈卫东</span> <span class="coach-cop">河南基地</span></div>
                                </li>
                                <li class="base-coach">
                                    <div class="coach-top-img img-box">
                                        <img src="<?=student_css_url.'image/test/test1.png'?>" >
                                    </div>
                                    <div class="coach-bottom-info"><span>陈卫东</span> <span class="coach-cop">河南基地</span></div>
                                </li>
                            </ul>
                        </div>
                        <div class="layui-tab-item">
                            <ul class="flow-default layui-row" id="flow-teacher3">
                                <li class="base-coach">
                                    <div class="coach-top-img img-box">
                                        <img src="<?=student_css_url.'image/test/test1.png'?>" >
                                    </div>
                                    <div class="coach-bottom-info"><span>陈卫东</span></div>
                                    <div class="coach-bottom-info"><span class="coach-cop">成都道和慧明文化传播有限·</span></div>
                                </li>
                                <li class="base-coach">
                                    <div class="coach-top-img img-box">
                                        <img src="<?=student_css_url.'image/test/test1.png'?>" >
                                    </div>
                                    <div class="coach-bottom-info"><span>陈卫东</span></div>
                                    <div class="coach-bottom-info"><span class="coach-cop">成都道和慧明文化传播有限·</span></div>
                                </li>
                                <li class="base-coach">
                                    <div class="coach-top-img img-box">
                                        <img src="<?=student_css_url.'image/noInfo/404x3.png'?>" >
                                    </div>
                                    <div class="coach-bottom-info"><span>陈卫东</span></div>
                                    <div class="coach-bottom-info"><span class="coach-cop">成都道和慧明文化传播有限·</span></div>
                                </li>
                                <li class="base-coach">
                                    <div class="coach-top-img img-box">
                                        <img src="<?=student_css_url.'image/test/test1.png'?>" >
                                    </div>
                                    <div class="coach-bottom-info"><span>陈卫东</span></div>
                                    <div class="coach-bottom-info"><span class="coach-cop">成都道和慧明文化传播有限·</span></div>
                                </li>    
                            </ul>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
<script>

jQuery(function($) { 
    $('.layui-tab-title li').click(function(){
        var _this=$(this);
        var index=_this.index();
        $('.layui-tab-title li').removeClass('layui-this');
        _this.addClass('layui-this');
        
        $('.layui-tab-content .layui-tab-item').removeClass('layui-show');
        $('.layui-tab-content .layui-tab-item').eq(index).addClass('layui-show');

        var left=_this.position().left+parseInt(_this.css('marginLeft'));
        var html=_this.html();
        // var data_id=$(this).attr('data-id')
        $('.nl-transform').css({
            'transform':'translate3d('+left+'px, 0px, 0px)'
        }).html(html)
    })
    
})
</script>