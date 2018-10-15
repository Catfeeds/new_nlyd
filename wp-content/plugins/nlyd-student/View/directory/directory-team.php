<style>
@media screen and (max-width: 1199px){
    #page {
        top: 130px;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav system-list system-match">
                <a class="mui-pull-left nl-goback">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <div class="item-wrapper">
                    <div class="center-detail">
                        <div class="system-font">
                            <p><?=__('脑力战队名录', 'nlyd-student')?></p>
                            <p>BRAIN TEAM</p> 
                        </div>
                    </div>
                </div>  
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding width-padding-pc contentP-wrapper">
                    <div class="nl-table-wapper">
                        <table class="nl-table">
                            <thead>
                                <tr>
                                    <td><?=__('战队名称', 'nlyd-student')?></td>
                                    <td><?=__('战队负责人', 'nlyd-student')?></td>
                                    <td><?=__('战队口号', 'nlyd-student')?></td>
                                    <td><?=__('战队成员', 'nlyd-student')?></td>
                                </tr>
                            </thead>
                            <tbody id="flow-table">
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
