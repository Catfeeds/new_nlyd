<style>
.show_form{
    /* width:600px; */
    padding:20px;
}
.layui-layer.nl-box-skin{
    width:800px;
}
.nl-box-skin .layui-layer-title{
    font-size:20px;
    height:50px;
    line-height:50px;
}
.img-zoos{
    padding:  15px 15px 0 15px;
    overflow: hidden;
}
.post-img{
    width: 62px;
    height: 62px;
    border: 1px dashed #333;
    position: relative;
    margin-right: 8px;
    float: left;
    margin-bottom: 5px;
}
.post-img.no-dash{
    border: 1px solid #eee;
    overflow: hidden;
}
.suggest-row .post-img:last-child{
    margin-right: 0;
}
.add-zoo{
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
}
.img-zoo{
    position: absolute;
    left: 0;
    top: 0;
}
.img-zoo img{
    z-index: 2;
}
.transverse{
    position: relative;
    width: 40px;
    height: 1px;
    background: #333;
    top: 50%;
    left: 50%;
    margin-left: -20px;
}
.vertical{
    position: relative;
    width: 1px;
    height: 40px;
    background: #333;
    top: 50%;
    left: 50%;
    margin-top: -20px;
}
.del{
    position: absolute;
    top: 0;
    right: 0;
    z-index: 3;
    width: 20px;
    height: 20px;
    color: red;
    text-align: center;
    font-weight: bold;
    background: #fff
}
.del i{
    font-size: 0.19rem;
}
.hide{
    display: none;
}
.show{
    display: block;
}
</style>
<div class="wrap">
    <h1 class="wp-heading-inline">监赛管理列表</h1>

    <button class="page-title-action">新增列表</button>
    <form method="get" onsubmit="return false;">

        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
                <select name="action" id="bulk-action-selector-top">
                    <option value="">批量操作</option>
                    <option value="-2">一键删除</option>
                </select>
                <input type="submit" id="doaction" class="button action  all-btn" value="应用">
            </div>
            <h2 class="screen-reader-text">轮数列表</h2><table class="wp-list-table widefat fixed striped users">
                <thead>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                        <input id="cb-select-all-2" type="checkbox">
                    </td>
                    <th scope="col" id="name" class="manage-column column-name column-primary">比赛名</th>
                    <th scope="col" id="project_name" class="manage-column column-project_name">比赛项目</th>
                    <th scope="col" id="more_num" class="manage-column column-more_num more_num">比赛轮数</th>
                    <th scope="col" id="real_name" class="manage-column column-real_name real_name">姓名</th>
                    <th scope="col" id="num" class="manage-column column-num num">座位号</th>
                    <th scope="col" id="dates" class="manage-column column-dates">时间</th>
                    <th scope="col" id="options" class="manage-column column-options options">操作</th>

                </tr>
                </thead>
                <tbody id="the-list" data-wp-lists="list:user">
                <?php if(!empty($rows)){ ?>
                    <?php foreach ($rows as $k => $v){ ?>
                    <tr>
                        <th scope="row" class="check-column">
                            <input type="checkbox" name="id[]" value="<?=$v['id']?>">
                        </th>
                        <td class="column-title column-primary">
                            <?=$v['match_title']?>
                            <button type="button" class="toggle-row">
                                <span class="screen-reader-text">显示详情</span>
                            </button>
                        </td>
                        <td class="column-project_name"><?=$v['project_title']?></td>
                        <td class="column-more_num">第<?=$v['match_more']?>轮</td>
                        <td class="column-real_name"><?=$v['student_name']?></td>
                        <td class="column-num"><?=$v['seat_number']?></td>
                        <td class="column-dates"><?=$v['created_time']?></td>
                        <td class="column-options">
                            <button type="button" class="update_more" data-id="<?=$v['id']?>">编辑</button>/
                            <button type="button" class="remove_more" data-id="<?=$v['id']?>">删除</button>
                        </td>
                    </tr>
                    <?php }?>
                <?php }else{ ?>
                    <tr>
                        <th colspan="8" style="text-align: center">暂无列表</th>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-2">全选</label>
                        <input id="cb-select-all-2" type="checkbox">
                    </td>
                    <th scope="col" class="manage-column column-project_name column-primary">比赛名</th>
                    <th scope="col" class="manage-column column-name">比赛项目</th>
                    <th scope="col" class="manage-column column-more_num more_num">比赛轮数</th>
                    <th scope="col" class="manage-column column-real_name real_name">姓名</th>
                    <th scope="col" class="manage-column column-num num">座位号</th>
                    <th scope="col" class="manage-column column-dates">时间</th>
                    <th scope="col" class="manage-column column-options">操作</th>

                </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="">批量操作</option>
                        <option value="-2">一键删除</option>
                    </select>
                    <input type="submit" id="doaction2" class="button action all-btn" value="应用">
                </div>
                <div class="tablenav-pages one-page">
                    <?=$pageHtml?>
                </div>
                <br class="clear">
            </div>
    </form>
    <!--轮数新增/修改form-->
    <div class="show_form" style="display: none" >
        <form class="add_more_form layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">姓名</label>
                <div class="layui-input-block">
                    <input type="text" value="" name="student_name" id="start_time" class="layui-input"/>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">座位号</label>
                <div class="layui-input-block">
                    <input type="text" value="" name="seat_number" id="end_time" class="layui-input"/>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">描述</label>
                <div class="layui-input-block">
                    <input type="text" value="" name="describe" id="end_time" class="layui-input"/>
                </div>
            </div>
            <div class="layui-bg-white img-zoos img-zoos1">
                <p class="tps">证据图片</p>

                <div class="post-img dash">
                    <div class="add-zoo" data-file="img-zoos1">
                        <div class="transverse"></div>
                        <div class="vertical"></div>
                    </div>
                </div>
            </div>
    </div>
    </form>
    <input style="display:none;" type="file" name="meta_val" id="img-zoos1" data-this="img-zoos1" value="" accept="image/*"/>
    <br class="clear">
</div>
<script>
    jQuery(document).ready(function($){
        //删除
        $('.remove_more').live('click',function(event){
            var id = $(this).attr('data-id');
            $.post(ajaxurl,{action:'remove_prison_log',id:id},function (data) {
                alert(data.data);
                history.go(0);
            },'json')
            return false;
        });
        $('body').on('click','.update_more',function(event){//编辑
            var _this=$(this);
            var id=_this.attr('data-id');
            //获取数据
            $.post(ajaxurl,{action:'get_prison_row',id:id},function (data) {
               console.log(data)
                var title=_this.parents('tr').find('td').eq(0).text()+_this.parents('tr').find('td').eq(1).text()+_this.parents('tr').find('td').eq(2).text()
                $('input[name=describe]').val(data['data']['describe'])
                $('input[name=seat_number]').val(data['data']['seat_number'])
                $('input[name=student_name]').val(data['data']['student_name'])
                imgs1=[];
                //图片
                $('.post-img.no-dash').remove();
                if(data['data']['evidence'].length>=3){
                    $('.post-img.dash').css('display','none')
                }else{
                    $('.post-img.dash').css('display','block')
                }
                $.each(data['data']['evidence'],function (index,value) {
                    var picture='<div class="post-img no-dash">'
                                    +'<div class="img-zoo img-box">'
                                        +'<img src="'+value+'"/>'
                                +'</div>'
                                    +'<input type="hidden" name="evidence[]" value="'+value+'" />'
                                   +'<div class="del">'
                                        +'X'
                                    +'</div>'
                                +'</div>'
                    $('.tps').after(picture)
                })
                layer.photos({//图片预览
                    photos: '.img-zoos',
                    anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                })
                showForm(title,id)
            },'json')

        });
        function showForm(title,id) {
            layer.open({
                type: 1
                ,maxWidth:1000
                ,title: title //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content:$('.show_form')
                ,btn: ['按错了','提交',  ]
                ,success: function(layero, index){
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    //按钮【按钮二】的回调
                    var fd = new FormData();
                    fd.append('student_name',$('input[name=student_name]').val());
                    fd.append('seat_number',$('input[name=seat_number]').val());
                    fd.append('describe',$('input[name=describe]').val());
                    fd.append('id',id);
                    fd.append('action','save_prison');
                    $.each(imgs1, function (i, v) {
                        fd.append('evidence[]',v);
                    })
                    $('.post-img.no-dash input').each(function () {
                        var name=$(this).attr('name')
                        fd.append(name,$(this).val());
                    })
                    $.ajax(
                        {
                        data: fd,
                        url:ajaxurl,
                        contentType : false,
                        processData : false,
                        cache : false,
                        aysnc: true ,
                        type: "POST" , // 默认使用POST方式
                        dataType:'json',
                        timeout:2000,
                        success: function(res, textStatus, jqXHR){
                            alert(res.data.info);
                            history.go(0);

                        }
                    })
                    layer.closeAll();

                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
           
        }



        $('body').on('click','.add-zoo',function(){//上传图片
            var id=$(this).attr('data-file')
            $('#'+id).click()
        })
        var imgs1=[]
        function changes(e,_this,array) {
            var file=e.target.files[0];
            array.unshift(file)
            var reader = new FileReader();
            var src='';
            //读取File对象的数据
            reader.onload = function(evt){
                //data:img base64 编码数据显示
                var dom='<div class="post-img no-dash">'
                    +'<div class="img-zoo img-box">'
                    +'<img src="'+evt.target.result+'"/>'
                    +'</div>'
                    +'<div class="del">'
                    +'X'
                    +'</div>'
                    +'</div>'
                var className=_this.attr('data-this')
                $('.'+className+' p').after(dom)
                layer.photos({//图片预览
                    photos: '.img-zoos',
                    anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                })
                if(className=="img-zoos1"){
                    if($('.'+className+' .post-img.no-dash').length>=3){
                        $('.'+className+' .post-img.dash').css('display','none')
                    }
                }
            }
            reader.readAsDataURL(file);
            $(e.target).val('')
        }

        $("#img-zoos1").change(function(e) {
            changes(e,$("#img-zoos1"),imgs1)
        });
        $('.img-zoos').on('click','.del',function(){//删除图片
            var _this=$(this);
            var index =_this.parents('.post-img').index();
            _this.parents('.img-zoos').find('.post-img.dash').css('display','block');
            _this.parents('.post-img').remove()
            if(_this.parents('.img-zoos').hasClass('img-zoos1')){
                imgs1.splice(index, 1);
            }
            layer.photos({//图片预览
                photos: '.img-zoos',
                anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
            })
        })

    });
</script>