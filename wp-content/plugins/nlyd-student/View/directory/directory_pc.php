
<div class="layui-fluid nl-content">
    <div class="layui-row">
        <div class="layui-row dir_nav">
            <?php for($i = 1; $i <= $max_level; $i++){ ?>

                <a href="<?=home_url('directory/directoryPlayer/level/'.$i)?>" class="c_black <?=$i==$current_level?'active':''?>"><?php printf(__('%s级脑力健将', 'nlyd-student'), $i)?></a>
            <?php } ?>
<!--            <a class=" c_black active">1--><?//=__('级脑力健将', 'nlyd-student')?><!--</a>-->
<!--            <a class=" c_black">1--><?//=__('级脑力健将', 'nlyd-student')?><!--</a>-->
<!--            <a class=" c_black">1--><?//=__('级脑力健将', 'nlyd-student')?><!--</a>-->
<!--            <a class=" c_black">1--><?//=__('级脑力健将', 'nlyd-student')?><!--</a>-->
<!--            <a class=" c_black">1--><?//=__('级脑力健将', 'nlyd-student')?><!--</a>-->
<!--            <a class=" c_black">1--><?//=__('级脑力健将', 'nlyd-student')?><!--</a>-->
<!--            <a class=" c_black">1--><?//=__('级脑力健将', 'nlyd-student')?><!--</a>-->
<!--            <a class=" c_black">1--><?//=__('级脑力健将', 'nlyd-student')?><!--</a>-->
        </div>
        <div class="layui-row flex-h">
            <div class="flex1 dir_nav_btn active"><?=__('中国脑力健将', 'nlyd-student')?></div>
            <div class="flex1 dir_nav_btn"><?=__('国际脑力健将', 'nlyd-student')?></div>
        </div>

        <div class="layui-row mt_10">
            <div class="layui-row dir_nav_content active">
                <?php if(isset($rows[1])) foreach ($rows[1] as $rowk => $row){ ?>
                    <div class="dir_table">
                        <div class="dir_table_name"><?php printf(__('中国%s级脑力健将（%s）', 'nlyd-student'), $current_level, $cateArr[$rowk]['post_title'])?></div>
                        <table>
                            <tr>
                                <td><?=__('序号', 'nlyd-student')?></td>
                                <td><?=__('姓名', 'nlyd-student')?></td>
                                <td><?=__('性别', 'nlyd-student')?></td>
                                <td><?=__('年龄', 'nlyd-student')?></td>
                                <td><?=__('组别', 'nlyd-student')?></td>
                                <td><?=__('国籍', 'nlyd-student')?></td>
                            </tr>
                            <?php foreach ($row as $rk => $rv){ ?>
                                <tr>
                                    <td><?=$rk+1?></td>
                                    <td><?=$rv['real_name']?></td>
                                    <td><?=__($rv['sex'], 'nlyd-student')?></td>
                                    <td><?=$rv['age']?></td>
                                    <td><?=__(getAgeGroupNameByAge($rv['age']), 'nlyd-student')?></td>
                                    <td><span class="fastbannerform__span f32 NOFLAG <?=$rv['user_nationality']?>"></span></td>
                                </tr>
                            <?php } ?>

                        </table>
                    </div>
                <?php } ?>

<!---->
<!--                <div class="dir_table">-->
<!--                    <div class="dir_table_name">--><?//=__('中国1级脑力健将（速读类）', 'nlyd-student')?><!--</div>-->
<!--                    <table>-->
<!--                        <tr>-->
<!--                            <td>--><?//=__('序号', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('姓名', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('性别', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('年龄', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('组别', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('国籍', 'nlyd-student')?><!--</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>--><?//=__('01', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('李奥', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('男', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('11', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('儿童组', 'nlyd-student')?><!--</td>-->
<!--                            <td><span class="fastbannerform__span f32 NOFLAG cn"></span></td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>--><?//=__('02', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('姓名', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('性别', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('年龄', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('组别', 'nlyd-student')?><!--</td>-->
<!--                            <td><span class="fastbannerform__span f32 NOFLAG gb"></span></td>-->
<!--                        </tr>-->
<!--                    </table>-->
<!--                </div>-->

<!--                <div class="dir_table">-->
<!--                    <div class="dir_table_name">--><?//=__('中国1级脑力健将（心算类）', 'nlyd-student')?><!--</div>-->
<!--                    <table>-->
<!--                        <tr>-->
<!--                            <td>--><?//=__('序号', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('姓名', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('性别', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('年龄', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('组别', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('国籍', 'nlyd-student')?><!--</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>--><?//=__('01', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('李奥', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('男', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('11', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('儿童组', 'nlyd-student')?><!--</td>-->
<!--                            <td><span class="fastbannerform__span f32 NOFLAG cn"></span></td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>--><?//=__('02', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('姓名', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('性别', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('年龄', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('组别', 'nlyd-student')?><!--</td>-->
<!--                            <td><span class="fastbannerform__span f32 NOFLAG gb"></span></td>-->
<!--                        </tr>-->
<!--                    </table>-->
<!--                </div>-->
            </div>

            <div class="layui-row dir_nav_content">
                <?php if(isset($rows[2])) foreach ($rows[2] as $rowk2 => $row2){ ?>
                    <div class="dir_table">
                        <div class="dir_table_name"><?php printf(__('国际%s级脑力健将（%s）', 'nlyd-student'), $current_level, $cateArr[$rowk2]['post_title'])?></div>
                        <table>
                            <tr>
                                <td><?=__('序号', 'nlyd-student')?></td>
                                <td><?=__('姓名', 'nlyd-student')?></td>
                                <td><?=__('性别', 'nlyd-student')?></td>
                                <td><?=__('年龄', 'nlyd-student')?></td>
                                <td><?=__('组别', 'nlyd-student')?></td>
                                <td><?=__('国籍', 'nlyd-student')?></td>
                            </tr>
                            <?php foreach ($row2 as $rk2 => $rv2){ ?>
                                <tr>
                                    <td><?=$rk2+1?></td>
                                    <td><?=$rv2['real_name']?></td>
                                    <td><?=__($rv2['sex'], 'nlyd-student')?></td>
                                    <td><?=$rv2['age']?></td>
                                    <td><?=__(getAgeGroupNameByAge($rv2['age']), 'nlyd-student')?></td>
                                    <td><span class="fastbannerform__span f32 NOFLAG <?=$rv2['user_nationality']?>"></span></td>
                                </tr>
                            <?php } ?>

                        </table>
                    </div>
                <?php } ?>
<!--                <div class="dir_table">-->
<!--                    <div class="dir_table_name">--><?//=__('国际1级脑力健将（记忆类）', 'nlyd-student')?><!--</div>-->
<!--                    <table>-->
<!--                        <tr>-->
<!--                            <td>--><?//=__('序号', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('姓名', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('性别', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('年龄', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('组别', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('国籍', 'nlyd-student')?><!--</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>--><?//=__('01', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('李奥', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('男', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('11', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('儿童组', 'nlyd-student')?><!--</td>-->
<!--                            <td><span class="fastbannerform__span f32 NOFLAG cn"></span></td>-->
<!--                        </tr>-->
<!--                    </table>-->
<!--                </div>-->
<!---->
<!--                <div class="dir_table">-->
<!--                    <div class="dir_table_name">--><?//=__('国际1级脑力健将（速读类）', 'nlyd-student')?><!--</div>-->
<!--                    <table>-->
<!--                        <tr>-->
<!--                            <td>--><?//=__('序号', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('姓名', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('性别', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('年龄', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('组别', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('国籍', 'nlyd-student')?><!--</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>--><?//=__('01', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('李奥', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('男', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('11', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('儿童组', 'nlyd-student')?><!--</td>-->
<!--                            <td><span class="fastbannerform__span f32 NOFLAG cn"></span></td>-->
<!--                        </tr>-->
<!--                    </table>-->
<!--                </div>-->
<!---->
<!--                <div class="dir_table">-->
<!--                    <div class="dir_table_name">--><?//=__('国际1级脑力健将（心算类）', 'nlyd-student')?><!--</div>-->
<!--                    <table>-->
<!--                        <tr>-->
<!--                            <td>--><?//=__('序号', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('姓名', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('性别', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('年龄', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('组别', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('国籍', 'nlyd-student')?><!--</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>--><?//=__('01', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('李奥', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('男', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('11', 'nlyd-student')?><!--</td>-->
<!--                            <td>--><?//=__('儿童组', 'nlyd-student')?><!--</td>-->
<!--                            <td><span class="fastbannerform__span f32 NOFLAG cn"></span></td>-->
<!--                        </tr>-->
<!--                    </table>-->
<!--                </div>-->
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function($) { 
        $('body').on('hover','.dir_nav_btn',function(){
            var _this=$(this),
            index=_this.index();
            $('.dir_nav_btn').removeClass('active');
            _this.addClass('active');
            $('.dir_nav_content').removeClass('active');
            $('.dir_nav_content').eq(index).addClass('active')
        })
    })
</script>