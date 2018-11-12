<!-- 提现 -->
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12  detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
        <a class="mui-pull-left nl-goback">
        <div><i class="iconfont">&#xe610;</i></div>
        </a>
        <h1 class="mui-title"><div><?=__('收款账户管理', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <form class="nl-page-form layui-form" lay-filter='addAdress'>
                    <div class="form-inputs">
                        <div class="form-input-row">
                            <div class="form-input-label"><?=__('开户姓名', 'nlyd-student')?></div>
                            <input type="text" name="country" autocomplete="off" class="nl-input nl-foucs" placeholder="<?=__('开户姓名', 'nlyd-student')?>">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><?=__('开户行', 'nlyd-student')?></div>
                            <input type="text" name="country" autocomplete="off" class="nl-input nl-foucs" placeholder="<?=__('开户行', 'nlyd-student')?>">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><?=__('开户账号', 'nlyd-student')?></div>
                            <input type="text" name="country" autocomplete="off" class="nl-input nl-foucs" placeholder="<?=__('开户账号', 'nlyd-student')?>">
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><?=__('银行预留手机号', 'nlyd-student')?></div>
                            <input type="text" name="country" autocomplete="off" class="nl-input nl-foucs" placeholder="<?=__('预留手机号', 'nlyd-student')?>">
                        </div>
                    </div>
                    <a class="a-btn a-btn-table" lay-filter="addAccount" lay-submit=""><div><?=__('保存', 'nlyd-student')?></div></a>
                </form>
            </div>
        </div>           
    </div>
</div>