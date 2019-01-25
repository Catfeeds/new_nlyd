
<div class="layui-fluid nl-content">
    <div class="layui-row">
        <div class="layui-row">
            <div class="layui-row dir_nav_content active">
                <div class="dir_head_name "><?=__('国际记忆水平等级名录', 'nlyd-student')?></div>
                <?php foreach ($rows as $row){
                    if(!empty($row)){
                        switch ($row[0]['memory']){
                            case '1':
                                    echo '<div class="dir_table_name">'.__('初等一级', 'nlyd-student').'</div>';
                                break;
                            case '2':
                                    echo '<div class="dir_table_name">'.__('初等二级', 'nlyd-student').'</div>';
                                break;
                            case '3':
                                    echo '<div class="dir_table_name">'.__('初等三级', 'nlyd-student').'</div>';
                                break;
                            case '4':
                                    echo '<div class="dir_table_name">'.__('中等四级', 'nlyd-student').'</div>';
                                break;
                            case '5':
                                    echo '<div class="dir_table_name">'.__('中等五级', 'nlyd-student').'</div>';
                                break;
                            case '6':
                                    echo '<div class="dir_table_name">'.__('中等六级', 'nlyd-student').'</div>';
                                break;
                            case '7':
                                    echo '<div class="dir_table_name">'.__('中等七级', 'nlyd-student').'</div>';
                                break;
                            case '8':
                                    echo '<div class="dir_table_name">'.__('高等八级', 'nlyd-student').'</div>';
                                break;
                            case '9':
                                    echo '<div class="dir_table_name">'.__('高等九级', 'nlyd-student').'</div>';
                                break;
                            case '10':
                                    echo '<div class="dir_table_name">'.__('高等十级', 'nlyd-student').'</div>';
                                break;
                        }
                        ?>
                        <div class="dir_table">
                            <table>
                                <tr>
                                    <td><?=__('序号', 'nlyd-student')?></td>
                                    <td><?=__('姓名', 'nlyd-student')?></td>
                                    <td><?=__('性别', 'nlyd-student')?></td>
                                    <td><?=__('年龄', 'nlyd-student')?></td>
                                    <td><?=__('国籍', 'nlyd-student')?></td>
                                </tr>
                        <?php
                        foreach ($row as $rk => $rv){
                            ?>
                                <tr>
                                    <td><?=__($rk+1, 'nlyd-student')?></td>
                                    <td><?=__($rv['real_name'], 'nlyd-student')?></td>
                                    <td><?=__($rv['user_sex'], 'nlyd-student')?></td>
                                    <td><?=__($rv['real_age'], 'nlyd-student')?></td>
                                    <td><span class="fastbannerform__span f32 NOFLAG <?=$rv['user_nationality']?>"></span></td>
                                </tr>

                            <?php
                        }

                        ?>
                            </table>
                        </div>
                        <?php
                    }
                    ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>