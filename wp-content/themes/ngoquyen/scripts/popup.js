/*
var popup_mp=0;
function SVIT_ADS_GetCookie(Name) {
	var re=new RegExp(Name+"=[^;]+", "i");
	if (document.cookie.match(re)) 
		return decodeURIComponent(document.cookie.match(re)[0].split("=")[1]); 
	return ""
}

function SVIT_ADS_SetCookie(name, value, days) {
	if (typeof days!="undefined"){ 
		var expireDate = new Date()
		var expstring=expireDate.setDate(expireDate.getDate()+days)
		document.cookie = name+"="+decodeURIComponent(value)+"; expires="+expireDate.toGMTString()
	}
	else document.cookie = name+"="+decodeURIComponent(value);
}

function vtlai_popup() {
	var cookie_popup_ads = SVIT_ADS_GetCookie('popup_mp_popup_ads');
	if (cookie_popup_ads=='') {
		if(popup_mp==0) {
			popup_mp=1;
			var Time_expires = 24 * 3600 * 1000;
			SVIT_ADS_SetCookie('popup_mp_popup_ads','true',Time_expires);			
			var vtlai_popup_0 = window.open('http://www.muabanlaptopcuhcm.com','_bank','width=500, height=110,scrollbars=yes,top=0,left=0',true);
		}
	}
}
*/