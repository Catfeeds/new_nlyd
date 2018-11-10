
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
                    if(leavePage && leavePage['grad_id']===jQuery.Request('grad_id') && leavePage['grad_type']===jQuery.Request('grad_type') && leavePage['type']===jQuery.Request('type')){
                        leavePage['leavePage']+=1;
                    }else{
                        var sessionData={
                            grad_id:jQuery.Request('grad_id'),
                            grad_type:jQuery.Request('grad_type'),
                            type:jQuery.Request('type'),
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
                    if(leavePage && leavePage['grad_id']===jQuery.Request('grad_id') && leavePage['grad_type']===jQuery.Request('grad_type') && leavePage['type']===jQuery.Request('type')){
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
                                grad_id:jQuery.Request('grad_id'),
                                grad_type:jQuery.Request('grad_type'),
                                type:jQuery.Request('type')
                            }
                        jQuery.SetSession('leavePageWaits',sessionData)
                    } else {
                        var leavePageWaits= jQuery.GetSession('leavePageWaits','1');
                        if(leavePageWaits && leavePageWaits['grad_id']===jQuery.Request('grad_id') && leavePageWaits['grad_type']===jQuery.Request('grad_type') && leavePageWaits['type']===jQuery.Request('type')){
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
                        jQuery(window).on("blur",function(){
                            var sessionData={
                                grad_id:jQuery.Request('grad_id')
                            }
                            jQuery.SetSession('waitting',sessionData)
                        })
                    } else {
                        var waitting= jQuery.GetSession('waitting','1');
                        if(waitting && waitting['grad_id']===jQuery.Request('grad_id')){
                            window.location.reload()
                            jQuery.DelSession('waitting')
                        }else{
                            jQuery.DelSession('waitting')
                        }
                    }
                    
                });
            }
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
                        grad_id:jQuery.Request('grad_id')
                    }
                    jQuery.SetSession('leavePageWaitting',sessionData)
                } else {
                    var leavePageWaitting= jQuery.GetSession('leavePageWaitting','1');
                    if(leavePageWaitting && leavePageWaitting['grad_id']===jQuery.Request('grad_id')){
                        jQuery.DelSession('leavePageWaitting')
                        window.location.reload()
                    }
                }
                
            });
        }
    }