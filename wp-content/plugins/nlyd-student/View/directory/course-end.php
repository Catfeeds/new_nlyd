
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
                <h1 class="mui-title"><div><?=__('结课成绩', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="nl-table-wapper">
                    <div class="c_black width-padding width-padding-pc bold fs_16 courseEnd_title"><?=__('高效记忆术·G预报班·成都郫县学员结课成绩', 'nlyd-student')?></div>
                    <table class="nl-table">
                        <tbody>
                            <tr class='table-head'>
                                <td><?=__('姓名/ID', 'nlyd-student')?></td>
                                <td><?=__('技 能', 'nlyd-student')?></td>
                                <td><?=__('分 享', 'nlyd-student')?></td>
                                <td><?=__('学费补贴', 'nlyd-student')?></td>
                                <td><?=__('奖 励', 'nlyd-student')?></td>
                            </tr>
                            <tr>
                                <td><div class="table_content"><span class="c_black">王好学</span><br><span class="ff_num fs_12">10000888</span></div></td>
                                <td><div class="table_content c_green"><?=__('达标', 'nlyd-student')?></td>
                                <td><div class="table_content c_green"><?=__('达标', 'nlyd-student')?></div></td>
                                <td><div class="table_content c_orange">3000.00</div></td>
                                <td><div class="table_content c_black">500.00</div></td>
                            </tr>
                            <tr>
                                <td><div class="table_content"><span class="c_black">王好学</span><br><span class="ff_num fs_12">10000888</span></div></td>
                                <td><div class="table_content"><?=__('未达标', 'nlyd-student')?></td>
                                <td><div class="table_content"><?=__('未达标', 'nlyd-student')?></div></td>
                                <td><div class="table_content">--</div></td>
                                <td><div class="table_content">--</div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>           
    </div>
</div>
