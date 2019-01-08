
    function leaveMatchPage(submit) {//准备也页，比赛页
        if(window.location.host!='127.0.0.1'){
            history.pushState(null, null, document.URL);
            window.addEventListener('popstate', function () {
                history.pushState(null, null, document.URL);
            });
            var visibilityChange; 
            if (typeof document.hidden !== "undefined") {
                visibilityChange = "visibilitychange";
            } else if (typeof document.mozHidden !== "undefined") {
                visibilityChange = "mozvisibilitychange";
            } else if (typeof document.msHidden !== "undefined") {
                visibilityChange = "msvisibilitychange";
            } else if (typeof document.webkitHidden !== "undefined") {
                visibilityChange = "webkitvisibilitychange";
            }

            document.addEventListener(visibilityChange, function() {
                var isHidden = document.hidden;
                if (isHidden) {
                    var leavePage = jQuery.GetSession('leavePage','1');
                    if(leavePage && leavePage['match_id']===jQuery.Request('match_id') && leavePage['project_id']===jQuery.Request('project_id') && leavePage['match_more']===jQuery.Request('match_more')){
                        leavePage['leavePage']+=1;
                    }else{
                        var sessionData={
                            match_id:jQuery.Request('match_id'),
                            project_id:jQuery.Request('project_id'),
                            match_more:jQuery.Request('match_more'),
                            leavePage:1,
                            Time:[],
                        }
                        leavePage= sessionData
                    }
                    var key=leavePage['leavePage']-1;
                    leavePage['Time'][key]={out:new Date().Format("yyyy-MM-dd hh:mm:ss")}
                    jQuery.SetSession('leavePage',leavePage)
                } else {
                    var leavePage= jQuery.GetSession('leavePage','1');
                    if(leavePage && leavePage['match_id']===jQuery.Request('match_id') && leavePage['project_id']===jQuery.Request('project_id') && leavePage['match_more']===jQuery.Request('match_more')){
                        var leveTimes=parseInt(leavePage['leavePage'])
                        leavePage['Time'][leveTimes-1]['back']=new Date().Format("yyyy-MM-dd hh:mm:ss")
                        jQuery.SetSession('leavePage',leavePage)
                        if(leveTimes>0 && leveTimes<1){
                            jQuery.alerts('第'+leveTimes+'次离开考试页面,到达1次自动提交答题')
                        }
                        if(leveTimes>=1){
                            jQuery.alerts(_leavePage.submit)
                            setTimeout(function() {
                                submit();
                            }, 3000);
                        }
                    }else{
                        jQuery.DelSession('leavePage')
                    }
                }
                
            });
        }
        //  if(window.location.host!='127.0.0.1'){
        //     history.pushState(null, null, document.URL);
        //     window.addEventListener('popstate', function () {
        //         history.pushState(null, null, document.URL);
        //     });
        //     jQuery(window).on("blur",function(){
        //         var leavePage = jQuery.GetSession('leavePage','1');
        //         if(leavePage && leavePage['match_id']===jQuery.Request('match_id') && leavePage['project_id']===jQuery.Request('project_id') && leavePage['match_more']===jQuery.Request('match_more')){
        //             leavePage['leavePage']+=1;
        //         }else{
        //             var sessionData={
        //                 match_id:jQuery.Request('match_id'),
        //                 project_id:jQuery.Request('project_id'),
        //                 match_more:jQuery.Request('match_more'),
        //                 leavePage:1,
        //                 Time:[],
        //             }
        //             leavePage= sessionData
        //         }
        //         var key=leavePage['leavePage']-1;
        //         leavePage['Time'][key]={out:new Date().Format("yyyy-MM-dd hh:mm:ss")}
        //         jQuery.SetSession('leavePage',leavePage)
        //     })  
        //     jQuery(window).on("focus", function(e) {
        //         var leavePage= jQuery.GetSession('leavePage','1');
        //         if(leavePage && leavePage['match_id']===jQuery.Request('match_id') && leavePage['project_id']===jQuery.Request('project_id') && leavePage['match_more']===jQuery.Request('match_more')){
        //             var leveTimes=parseInt(leavePage['leavePage'])
        //             leavePage['Time'][leveTimes-1]['back']=new Date().Format("yyyy-MM-dd hh:mm:ss")
        //             jQuery.SetSession('leavePage',leavePage)
        //             if(leveTimes>0 && leveTimes<1){
        //                 jQuery.alerts('第'+leveTimes+'次离开考试页面,到达1次自动提交答题')
        //             }
        //             if(leveTimes>=1){
        //                 jQuery.alerts(_leavePage.submit)
        //                 setTimeout(function() {
        //                     submit();
        //                 }, 3000);
        //             }
        //         }else{
        //             jQuery.DelSession('leavePage')
        //         }
        //     });
        //  }
    }

    function leavePageLoad(url){//比赛纪录页
        if(window.location.host!='127.0.0.1'){
                history.pushState(null, null, document.URL);
                window.addEventListener('popstate', function () {
                    history.pushState(null, null, document.URL);
                });
                var visibilityChange; 
                if (typeof document.hidden !== "undefined") {
                    visibilityChange = "visibilitychange";
                } else if (typeof document.mozHidden !== "undefined") {
                    visibilityChange = "mozvisibilitychange";
                } else if (typeof document.msHidden !== "undefined") {
                    visibilityChange = "msvisibilitychange";
                } else if (typeof document.webkitHidden !== "undefined") {
                    visibilityChange = "webkitvisibilitychange";
                }
    
                document.addEventListener(visibilityChange, function() {
                    var isHidden = document.hidden;
                    if (isHidden) {
                        var sessionData={
                                match_id:jQuery.Request('match_id'),
                                project_id:jQuery.Request('project_id'),
                                match_more:jQuery.Request('match_more')
                            }
                        jQuery.SetSession('leavePageWaits',sessionData)
                    } else {
                        var leavePageWaits= jQuery.GetSession('leavePageWaits','1');
                        if(leavePageWaits && leavePageWaits['match_id']===jQuery.Request('match_id') && leavePageWaits['project_id']===jQuery.Request('project_id') && leavePageWaits['match_more']===jQuery.Request('match_more')){
                            jQuery.DelSession('leavePageWaits')
                            if(url.length>0){
                                window.location.href=url
                            }else{
                                window.location.reload()
                            }
                        }else{
                            jQuery.DelSession('leavePageWaits')
                        }
                    }
                    
                });
            }
        // if(window.location.host!='127.0.0.1'){
        //     history.pushState(null, null, document.URL);
        //     window.addEventListener('popstate', function () {
        //         history.pushState(null, null, document.URL);
        //     });
        //     jQuery(window).on("blur",function(){
        //         var sessionData={
        //                 match_id:jQuery.Request('match_id'),
        //                 project_id:jQuery.Request('project_id'),
        //                 match_more:jQuery.Request('match_more')
        //             }
        //         jQuery.SetSession('leavePageWaits',sessionData)
        //     })  
        //     jQuery(window).on("focus", function(e) {
        //         var leavePageWaits= jQuery.GetSession('leavePageWaits','1');
        //         if(leavePageWaits && leavePageWaits['match_id']===jQuery.Request('match_id') && leavePageWaits['project_id']===jQuery.Request('project_id') && leavePageWaits['match_more']===jQuery.Request('match_more')){
        //             jQuery.DelSession('leavePageWaits')
        //             if(url.length>0){
        //                 window.location.href=url
        //             }else{
        //                 window.location.reload()
        //             }
        //         }else{
        //             jQuery.DelSession('leavePageWaits')
        //         }
        //     });
        // }
    }

    function matchDetail(){//比赛详情页
        if(window.location.host!='127.0.0.1'){
                var visibilityChange; 
                if (typeof document.hidden !== "undefined") {
                    visibilityChange = "visibilitychange";
                } else if (typeof document.mozHidden !== "undefined") {
                    visibilityChange = "mozvisibilitychange";
                } else if (typeof document.msHidden !== "undefined") {
                    visibilityChange = "msvisibilitychange";
                } else if (typeof document.webkitHidden !== "undefined") {
                    visibilityChange = "webkitvisibilitychange";
                }
    
                document.addEventListener(visibilityChange, function() {
                    var isHidden = document.hidden;
                    if (isHidden) {
                        var sessionData={
                            match_id:jQuery.Request('match_id')
                        }
                        jQuery.SetSession('waitting',sessionData)
                    } else {
                        var waitting= jQuery.GetSession('waitting','1');
                        if(waitting && waitting['match_id']===jQuery.Request('match_id')){
                            window.location.reload()
                            jQuery.DelSession('waitting')
                        }else{
                            jQuery.DelSession('waitting')
                        }
                    }
                    
                });
            }
        // if(window.location.host!='127.0.0.1'){
        //     jQuery(window).on("blur",function(){
        //         var sessionData={
        //             match_id:jQuery.Request('match_id')
        //         }
        //         jQuery.SetSession('waitting',sessionData)
        //     })
        //     jQuery(window).on("focus", function(e) {
        //         var waitting= jQuery.GetSession('waitting','1');
        //         if(waitting && waitting['match_id']===jQuery.Request('match_id')){
        //             window.location.reload()
        //             jQuery.DelSession('waitting')
        //         }else{
        //             jQuery.DelSession('waitting')
        //         }
        //     });
        // }
    }
    function matchWaitting(){//比赛等待页
        if(window.location.host!='127.0.0.1'){
            history.pushState(null, null, document.URL);
            window.addEventListener('popstate', function () {
                history.pushState(null, null, document.URL);
            });
            var visibilityChange; 
            if (typeof document.hidden !== "undefined") {
                visibilityChange = "visibilitychange";
            } else if (typeof document.mozHidden !== "undefined") {
                visibilityChange = "mozvisibilitychange";
            } else if (typeof document.msHidden !== "undefined") {
                visibilityChange = "msvisibilitychange";
            } else if (typeof document.webkitHidden !== "undefined") {
                visibilityChange = "webkitvisibilitychange";
            }
    
            document.addEventListener(visibilityChange, function() {
                var isHidden = document.hidden;
                if (isHidden) {
                    var sessionData={
                        match_id:jQuery.Request('match_id')
                    }
                    jQuery.SetSession('leavePageWaitting',sessionData)
                } else {
                    var leavePageWaitting= jQuery.GetSession('leavePageWaitting','1');
                    if(leavePageWaitting && leavePageWaitting['match_id']===jQuery.Request('match_id')){
                        jQuery.DelSession('leavePageWaitting')
                        window.location.reload()
                    }
                }
                
            });
        }
        // if(window.location.host!='127.0.0.1'){
        // jQuery(window).on("blur",function(){
        //     var sessionData={
        //         match_id:jQuery.Request('match_id')
        //     }
        //     jQuery.SetSession('leavePageWaitting',sessionData)
        // })
        // jQuery(window).on("focus", function(e) {
        //     var leavePageWaitting= jQuery.GetSession('leavePageWaitting','1');
        //     if(leavePageWaitting && leavePageWaitting['match_id']===jQuery.Request('match_id')){
        //         jQuery.DelSession('leavePageWaitting')
        //         window.location.reload()
        //     }
        // });
        //  }
    }