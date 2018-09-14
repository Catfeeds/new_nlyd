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
            <header class="mui-bar mui-bar-nav system-list system-teacher">
                <a class="mui-pull-left nl-goback">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <div class="item-wrapper">
                    <div class="center-detail">
                        <div class="system-font">
                            <p>脑力健将名录</p>
                            <p>BRAIN POWER</p> 
                        </div>
                    </div>
                </div>  
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding width-padding-pc contentP-wrapper">
                    <div class="nl-table-wapper">
                        <table class="nl-table" >
                            <thead>
                                <tr>
                                    <td><div class="table_content">头像 </div></td>
                                    <td><div class="table_content">学员姓名 </div></td>
                                    <td><div class="table_content">ID </div></td>
                                    <td><div class="table_content">性别 </div></td>
                                    <td><div class="table_content">类别 </div></td>
                                    <td><div class="table_content">级别 </div></td>
                                    <td><div class="table_content">主训教练 </div></td>
                                    <td><div class="table_content">国籍 </div></td>
                                </tr>
                            </thead>
                            <tbody id="flow-table">
                                <tr>
                                    <td>
                                        <div class="player-img img-box">
                                            <img src="<?=student_css_url.'image/icons/match-big.png'?>">
                                        </div>
                                    </td>
                                    <td><div class="table_content">学员姓名 </div></td>
                                    <td><div class="table_content">ID </div></td>
                                    <td><div class="table_content">性别 </div></td>
                                    <td><div class="table_content">类别 </div></td>
                                    <td><div class="table_content">级别 </div></td>
                                    <td><div class="table_content">主训教练 </div></td>
                                    <td><div class="table_content">国籍 </div></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 战队分页 -->
<input type="hidden" name="_wpnonce" id="getTeam" value="<?=wp_create_nonce('student_get_team_code_nonce');?>">
<script>
jQuery(function($) {   
    layui.use(['layer','flow'], function(){
        var flow = layui.flow;//流加载
//--------------------分页--------------------------

 //--------------------分页--------------------------  
    })
})
</script>