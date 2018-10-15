<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper layui-bg-wgite">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback">
                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title"> <?=__('消息详情', 'nlyd-student')?></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="message-row">
                    <div class="message-title">
                        <span class="accept-name"><?=$row->title?></span>
                        
                        <span class="message-time"><?=$row->message_time?></span>
                    </div>
                    <p class="message-detail"><?=$row->content?></p>
                </div>
            </div>
        </div>           
    </div>
</div>