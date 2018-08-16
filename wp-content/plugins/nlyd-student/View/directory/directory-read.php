<style>
@media screen and (max-width: 991px){
    #page {
        top: 130px;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav system-list system-course">
                <a class="mui-pull-left nl-goback">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <div class="item-wrapper">
                    <div class="center-detail">
                        <div class="system-font">
                            <p>速读水平认证名录</p>
                            <p>SPEED READING</p> 
                        </div>
                    </div>
                </div>  
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding width-padding-pc contentP-wrapper">
                    <table class="nl-table" id="flow-table">
                        <tr>
                            <td>头像</td>
                            <td>学员姓名</td>
                            <td>ID</td>
                            <td>性别</td>
                            <td>速读级别</td>
                            <td>主训教练</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="player-img img-box">
                                    <img src="<?=student_css_url.'image/icons/match-big.png'?>">
                                </div>
                            </td>
                            <td>学员姓名</td>
                            <td>ID</td>
                            <td>性别</td>
                            <td>速算级别</td>
                            <td>主训教练</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="player-img img-box">
                                    <img src="<?=student_css_url.'image/icons/match-big.png'?>">
                                </div>
                            </td>
                            <td>学员姓名</td>
                            <td>ID</td>
                            <td>性别</td>
                            <td>速算级别</td>
                            <td>主训教练</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="player-img img-box">
                                    <img src="<?=student_css_url.'image/icons/match-big.png'?>">
                                </div>
                            </td>
                            <td>学员姓名</td>
                            <td>ID</td>
                            <td>性别</td>
                            <td>速算级别</td>
                            <td>主训教练</td>
                        </tr>
                    </table>
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