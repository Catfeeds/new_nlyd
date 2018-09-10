
    function leaveMatchPage(submit) {//准备也页，比赛页
        
        // if(window.location.host=='ydbeta.gjnlyd.com'){
            history.pushState(null, null, document.URL);
            window.addEventListener('popstate', function () {
                history.pushState(null, null, document.URL);
            });
            jQuery(window).on("blur",function(){
                var leavePage = jQuery.GetSession('leavePage','1');
                if(leavePage && leavePage['match_id']===jQuery.Request('match_id') && leavePage['project_id']===jQuery.Request('project_id') && leavePage['match_more']===jQuery.Request('match_more')){
                    leavePage['leavePage']+=1;
                }else{
                    var sessionData={
                        match_id:jQuery.Request('match_id'),
                        project_id:jQuery.Request('project_id'),
                        match_more:jQuery.Request('match_more'),
                        leavePage:1
                    }
                    leavePage= sessionData
                }
                jQuery.SetSession('leavePage',leavePage)
            })  
            jQuery(window).on("focus", function(e) {
                var leavePage= jQuery.GetSession('leavePage','1');
                if(leavePage && leavePage['match_id']===jQuery.Request('match_id') && leavePage['project_id']===jQuery.Request('project_id') && leavePage['match_more']===jQuery.Request('match_more')){
                    var leveTimes=parseInt(leavePage['leavePage'])
                    if(leveTimes>0 && leveTimes<3){
                        jQuery.alerts('第'+leveTimes+'次离开考试页面,超过2次自动提交答题')
                    }
                    if(leveTimes>=3){
                        jQuery.alerts('第'+leveTimes+'次离开考试页面,自动提交本轮答题')
                        setTimeout(function() {
                            submit();
                        }, 1000);
                        submit();
                    }
                }
            });
        // }
    }

    function leavePageLoad(url){//比赛纪录页
        // if(window.location.host=='ydbeta.gjnlyd.com'){
            history.pushState(null, null, document.URL);
            window.addEventListener('popstate', function () {
                history.pushState(null, null, document.URL);
            });
            jQuery(window).on("blur",function(){
                var sessionData={
                        match_id:jQuery.Request('match_id'),
                        project_id:jQuery.Request('project_id'),
                        match_more:jQuery.Request('match_more')
                    }
                jQuery.SetSession('leavePageWaits',sessionData)
            })  
            jQuery(window).on("focus", function(e) {
                var leavePageWaits= jQuery.GetSession('leavePageWaits','1');
                if(leavePageWaits && leavePageWaits['match_id']===jQuery.Request('match_id') && leavePageWaits['project_id']===jQuery.Request('project_id') && leavePageWaits['match_more']===jQuery.Request('match_more')){
                    jQuery.DelSession('leavePageWaits')
                    if(url){
                        window.location.href=url
                    }else{
                        window.location.reload()
                    }
                    
                }
            });
        // }
    }

    function matchDetail(){//比赛详情页
        // if(window.location.host=='ydbeta.gjnlyd.com'){
            jQuery(window).on("blur",function(){
                jQuery.SetSession('waitting','1')
            })  
            jQuery(window).on("focus", function(e) {
                var waitting= jQuery.GetSession('waitting');
                if(waitting){
                    jQuery.DelSession('waitting')
                    window.location.reload()
                }
            });
        // }
    }
