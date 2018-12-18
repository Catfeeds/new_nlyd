
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('教练管理', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="nl-table-wapper">
                    <table class="nl-table">
                        <tbody>
                            <tr class='table-head'>
                                <td><?=__('序 号', 'nlyd-student')?></td>
                                <td><?=__('工作照', 'nlyd-student')?></td>
                                <td><?=__('姓名/ID', 'nlyd-student')?></td>
                                <td><?=__('年 龄', 'nlyd-student')?></td>
                                <td><?=__('性 别', 'nlyd-student')?></td>
                                <td><?=__('操 作', 'nlyd-student')?></td>
                            </tr>
                            <tr>
                                <td><div class="table_content">1</div></td>
                                <td><div class="table_content"><div class="img-box coach_img"><img src="<?=student_css_url.'image/nlyd.png'?>"></div></div></td>
                                <td><div class="table_content">18</div></td>
                                <td><div class="table_content c_black">男</div></td>
                                <td><div class="table_content c_black">刘亿亿</div></td>
                                <td><div class="table_content"><a class="c_blue" href="<?=home_url('/zone/coachDetail/');?>">详 情</a></div></td>
                            </tr>
                            <tr>
                                <td><div class="table_content">1</div></td>
                                <td><div class="table_content"><div class="img-box coach_img"><img src="<?=student_css_url.'image/nlyd.png'?>"></div></div></td>
                                <td><div class="table_content c_black">18</div></td>
                                <td><div class="table_content c_black">男</div></td>
                                <td><div class="table_content">刘亿亿</div></td>
                                <td><div class="table_content"><a class="c_blue" href="<?=home_url('/zone/coachDetail/');?>">详 情</a></div></td>
                            </tr>
                        </tbody>
                    </table>
                    <a href="<?=home_url('/zone/coachAdd/');?>" class="a-btn a-btn-table"><div><?=__('添加教练', 'nlyd-student')?></div></a>
                </div>
            </div>
        </div>           
    </div>
</div>
