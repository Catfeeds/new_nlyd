
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback">
                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title"><?=__('我的训练', 'nlyd-student')?></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="no-info-page">
                    <div class="no-info-img">
                        <img src="<?=student_css_url.'image/noInfo/noTrain1045@2x.png'?>">
                    </div>
                    <p class="no-info-text"><?=__('您暂无训练记录', 'nlyd-student')?></p>
                    <a class="a-btn"><?=__('现在去训练', 'nlyd-student')?></a>
                </div>
            </div>
        </div>           
    </div>
</div>
