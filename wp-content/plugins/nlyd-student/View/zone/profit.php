
<style>
@media screen and (max-width: 1199px){
    .layui-fluid>.layui-row>.layui-bg-white:first-child,#page{
        background-color:#f6f6f6!important;
    }
    #page{
        top:0;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <a class="mui-pull-left nl-goback">
                <div><i class="iconfont">&#xe610;</i></div>
            </a>
            <div class="layui-row nl-border nl-content">
                <div class="apply profit_title layui-row layui-bg-white">
                    <div class="ta_c c_black"><?=__('可提现金额(元)', 'nlyd-student')?></div>
                    <div class="ta_c c_green bold  fs_22 profit_money_">¥ 200.00</div>
                    <a class="bg_gradient_green tixian c_white ta_c dis_table" href="<?=home_url('zone/getCash');?>"><div class="dis_cell"><?=__('提 现', 'nlyd-student')?></div></a>
                    <div class="profit_footer flex-h">
                        <div class="flex1 ta_c">
                            <span class="c_black"><?=__('今日收益', 'nlyd-student')?>：</span>
                            <span class="c_green">¥ 200.00</span>
                        </div>
                        <div class="flex1 ta_c">
                            <span class="c_black"><?=__('累计收益', 'nlyd-student')?>：</span>
                            <span class="c_green">¥ 200.00</span>
                        </div>
                    </div>
                </div>

                <div class="apply width-padding layui-row layui-bg-white width-margin-pc">
                    <div class="profit-layui-tab layui-tab layui-tab-brief" lay-filter="profit" style="margin:0">
                        <ul style="margin:0;padding:0" class="layui-tab-title layui-row">
                            <li class="layui-this dis_table" lay-id="1"><div class="dis_cell"><?=__('全部记录', 'nlyd-student')?></div></li>
                            <li class="dis_table" lay-id="2"><div class="dis_cell"><?=__('收益记录', 'nlyd-student')?></div></li>
                            <li class="dis_table" lay-id="3"><div class="dis_cell"><?=__('提现记录', 'nlyd-student')?></div></li>
                        </ul>
                        <div class="layui-tab-content">
                            <!-- 全部记录 -->
                            <div class="layui-tab-item layui-show">
                                <div class="layui-row" id="1">
                                    <a class="profit_list c_black layui-row" href="<?=home_url('zone/profitDetail');?>">
                                        <div class="profit_inline profit_icon">
                                            <div class="zone_bg bg_add"></div>
                                        </div>
                                        <div class="profit_inline profit_time fs_14">
                                            <span><?=__('比赛收益', 'nlyd-student')?></span><br>
                                            <span class="c_black3">2018/12/12 13:18</span>
                                        </div>
                                        <div class="c_green profit_inline profit_money fs_14">+500.00</div>
                                        <div class="profit_inline profit_arrow"><i class="iconfont fs_20">&#xe727;</i></div>
                                    </a>
                                    <a class="profit_list c_black layui-row" href="<?=home_url('zone/profitDetail');?>">
                                        <div class="profit_inline profit_icon">
                                            <div class="zone_bg bg_reduce"></div>
                                        </div>
                                        <div class="profit_inline profit_time fs_14">
                                            <span><?=__('账户提现', 'nlyd-student')?></span><br>
                                            <span class="c_black3">2018/12/12 13:18</span>
                                        </div>
                                        <div class="c_black3 profit_inline profit_money fs_14">-500.00</div>
                                        <div class="profit_inline profit_arrow"><i class="iconfont fs_20">&#xe727;</i></div>
                                    </a>
                                </div>
                            </div>
                            <!-- 收益记录 -->
                            <div class="layui-tab-item">
                                <div class="layui-row" id="2">
                                    <a class="profit_list c_black layui-row" href="<?=home_url('zone/profitDetail');?>">
                                        <div class="profit_inline profit_icon">
                                            <div class="zone_bg bg_add"></div>
                                        </div>
                                        <div class="profit_inline profit_time fs_14">
                                            <span><?=__('比赛收益', 'nlyd-student')?></span><br>
                                            <span class="c_black3">2018/12/12 13:18</span>
                                        </div>
                                        <div class="c_green profit_inline profit_money fs_14">+500.00</div>
                                        <div class="profit_inline profit_arrow"><i class="iconfont fs_20">&#xe727;</i></div>
                                    </a>
                                    <a class="profit_list c_black layui-row" href="<?=home_url('zone/profitDetail');?>">
                                        <div class="profit_inline profit_icon">
                                            <div class="zone_bg bg_add"></div>
                                        </div>
                                        <div class="profit_inline profit_time fs_14">
                                            <span><?=__('比赛收益', 'nlyd-student')?></span><br>
                                            <span class="c_black3">2018/12/12 13:18</span>
                                        </div>
                                        <div class="c_green profit_inline profit_money fs_14">+500.00</div>
                                        <div class="profit_inline profit_arrow"><i class="iconfont fs_20">&#xe727;</i></div>
                                    </a>
                                    <a class="profit_list c_black layui-row" href="<?=home_url('zone/profitDetail');?>">
                                        <div class="profit_inline profit_icon">
                                            <div class="zone_bg bg_add"></div>
                                        </div>
                                        <div class="profit_inline profit_time fs_14">
                                            <span><?=__('比赛收益', 'nlyd-student')?></span><br>
                                            <span class="c_black3">2018/12/12 13:18</span>
                                        </div>
                                        <div class="c_green profit_inline profit_money fs_14">+500.00</div>
                                        <div class="profit_inline profit_arrow"><i class="iconfont fs_20">&#xe727;</i></div>
                                    </a>
                                </div>
                            </div>
                            <!-- 提现记录 -->
                            <div class="layui-tab-item">
                                <div class="layui-row" id="3">
                                    <a class="profit_list c_black layui-row" href="<?=home_url('zone/getCashDetail');?>">
                                        <div class="profit_inline profit_icon">
                                            <div class="zone_bg bg_reduce"></div>
                                        </div>
                                        <div class="profit_inline profit_time fs_14">
                                            <span><?=__('账户提现', 'nlyd-student')?></span><br>
                                            <span class="c_black3">2018/12/12 13:18</span>
                                        </div>
                                        <div class="c_black3 profit_inline profit_money fs_14">-500.00</div>
                                        <div class="profit_inline profit_arrow"><i class="iconfont fs_20">&#xe727;</i></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>            
    </div>
</div>

<script>
jQuery(function($) { 
    layui.use(['element','flow'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        var flow = layui.flow;//流加载
    })
})
</script>
