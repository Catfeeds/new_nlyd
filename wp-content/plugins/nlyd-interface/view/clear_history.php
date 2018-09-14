<?php
/**
 * 项目默认配置
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/21
 * Time: 9:48
 */
$setting = get_option('default_setting');

?>

<div class="wrap">
    <h1>清除历史</h1>

    <form method="post" id="default_form" novalidate="novalidate">
        <input type="hidden" name="action" value="Clear_history">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><label for="blogname">一键删除指定用户</label></th>
                <td>
                    <div class="layui-input-block">
                        <select class="js-data-select-ajax" name="id[]" style="width: 100%" data-action="get_user_list" data-type="user" data-placeholder="输入用户名/手机/邮箱/昵称" multiple></select>
                    </div>
                </td>
                <td>
                    <button class="clear_history" type="button" data-type="user">确定</button>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="blogname">一键清除比赛</label></th>
                <td>
                    <div class="layui-input-block">
                        <select class="js-data-select-ajax" name="id[]" style="width: 100%" data-action="get_match_list" data-placeholder="输入比赛名字" multiple></select>
                    </div>
                </td>
                <td>
                    <button class="clear_history" type="button" data-type="match">确定</button>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="blogname">一键清除题目</label></th>
                <td>
                    <div class="layui-input-block">
                        <select class="js-data-select-ajax" name="id[]" style="width: 100%" data-action="get_question_list" data-placeholder="输入题目名字" multiple></select>
                    </div>
                </td>
                <td>
                    <button class="clear_history" type="button" data-type="question">确定</button>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="blogname">一键清除教练</label></th>
                <td>
                    <div class="layui-input-block">
                        <select class="js-data-select-ajax" name="id[]" style="width: 100%" data-action="get_user_list" data-type="teacher" data-placeholder="输入教练名字" multiple></select>
                    </div>
                </td>
                <td>
                    <button class="clear_history" type="button" data-type="teacher">确定</button>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="blogname">一键清除战队</label></th>
                <td>
                    <div class="layui-input-block">
                        <select class="js-data-select-ajax" name="id[]" style="width: 100%" data-action="get_team_list" data-type="team" data-placeholder="输入战队名字" multiple></select>
                    </div>
                </td>
                <td>
                    <button class="clear_history" type="button" data-type="team">确定</button>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="blogname">一键切换题目类型</label></th>
                <td>
                    <div class="layui-input-block">
                        <select class="js-data-select-ajax" name="id" style="width: 100%" data-action="get_category_list" data-type="team" data-placeholder="输入题目分类名字" ></select>
                    </div>
                </td>
                <td>
                    <button class="clear_history" type="button" data-type="category">确定</button>
                </td>
            </tr>
            </tbody>
    </form>

</div>
<script>
    /*jQuery(document).ready(function($){
        $('#test_select2').select2({
            placeholder : '输入话题关键字',
            tags : true,
            multiple : true, //多选
            height: '40px',
            maximumSelectionLength : 3,
            allowClear : true,
            language: "zh-CN",
            data : [{id: "1", text: "446546@qq.com"}, {id: "3", text: "15982345102"}, {id: "4", text: "277564072@qq.com"}]
        });
    });*/
</script>
