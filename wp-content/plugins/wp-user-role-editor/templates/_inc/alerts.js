/*调用方法
$.n('');
*/
/*$.alerts*/
function Alert(msg){
	this.msg=msg
}
Alert.prototype={
	constructor:Alert,
	alertInfo:function(){
		var html="<div class='addAlert'></div>";
		if(jQuery(".addAlert").length<1){
			jQuery("body").append(html)
		}
		jQuery(".addAlert").css({
			"position":"fixed",
			"top":"50%",
			"left":"50%",
			"padding":"10px 20px",
			"width":"200px",
			"border-radius":"5px",
			"font-size":"13px",
			"color":"#fff",
			"background-color":"rgba(0, 0, 0, 0.79)",
			"text-align":"center",
			"line-height":"1.5em",
			"margin-top":"-60px",
			"margin-left":"-100px",
			"display":"none",
			"z-index":"10005"
		}).text(this.msg)
			.stop(true)
			.fadeIn(300)
			.delay(500)
			.fadeOut(1000);
	}
};
jQuery.extend({
	alerts:function(msg){
		var alerts=new Alert(msg);
		alerts.alertInfo();
	}
});

