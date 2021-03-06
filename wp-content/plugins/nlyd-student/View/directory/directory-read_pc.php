
<div class="layui-fluid nl-content">
    <div class="layui-row">
        <div class="layui-row">
            <div class="layui-row dir_nav_content active">
                <div class="dir_table">
                    <div class="dir_table_name"><?=__('国际速读水平等级名录', 'nlyd-student')?></div>
                    <table>
                        <tr>
                            <td><?=__('序号', 'nlyd-student')?></td>
                            <td><?=__('姓名', 'nlyd-student')?></td>
                            <td><?=__('性别', 'nlyd-student')?></td>
                            <td><?=__('年龄', 'nlyd-student')?></td>
                            <td><?=__('级别', 'nlyd-student')?></td>
                            <td><?=__('国籍', 'nlyd-student')?></td>
                        </tr>
                        <?php foreach ($rows as $rk => $rv){ ?>
                        <tr>
                            <td><?=__($rk+1, 'nlyd-student')?></td>
                            <td><?=__($rv['real_name'], 'nlyd-student')?></td>
                            <td><?=__($rv['user_sex'], 'nlyd-student')?></td>
                            <td><?=__($rv['real_age'], 'nlyd-student')?></td>
                            <td><?=__($rv['read'], 'nlyd-student')?></td>
                            <td><span class="fastbannerform__span f32 NOFLAG <?=$rv['user_nationality']?>"></span></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>