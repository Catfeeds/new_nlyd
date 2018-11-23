<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback"><div><i class="iconfont">&#xe610;</i></div></a>
            <h1 class="mui-title"><div><?=__('考级成绩查看', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <p class="c_black bold fs_16 pl_10 mt_20 mb_20"><?=$rows[0]['post_title']?><span class="ml_10 fs_14  <?=$row['grading_result'] == 1 ? 'c_green' : 'c_black6';?> "><?=__($row['result_cn'], 'nlyd-student')?></span></p>
                <div class="nl-table-wapper">
                        <table class="nl-table" >
                            <thead>
                                <tr>
                                    <td><div class="table_content bold "><?=__('序 号', 'nlyd-student')?></div></td>
                                    <td><div class="table_content bold"><?=__('姓 名', 'nlyd-student')?></div></td>
                                    <td><div class="table_content bold"><?=__('ID', 'nlyd-student')?></div></td>
                                    <td><div class="table_content bold"><?=__('考级成绩', 'nlyd-student')?></div></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $k =>$val ){ ?>
                                <tr>
                                    <td><div class="table_content"><?=$k+1;?></div></td>
                                    <td><div class="table_content"><?=$val['real_name'];?></div></td>
                                    <td><div class="table_content"><?=$val['user_ID'];?></div></td>
                                    <td><div class="table_content"><span class="<?=$val['grading_result'] == 1 ? 'c_green' : 'c_black6';?> "><?=$val['result_cn'];?></span></div></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php if(!empty($row['grading_log_id'])):?>
                <a class="a-btn" id="complete" href="<?=home_url('gradings/myAnswerLog/grad_id/'.$_GET['grad_id'])?>"><div><?=__('我的答题记录', 'nlyd-student')?></div></a>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>