window.share = function(rede, link){

	let url = 'mobile';
	if(Math.max(document.documentElement.clientWidth, window.innerWidth || 0) > 1100){
		url = 'pc';
	}

	if(rede == 'facebook'){
		if(url == 'mobile'){
			window.open("https://www.facebook.com/share.php?u="+link, '_blank');
		}else{
			window.open("https://www.facebook.com/share.php?u="+link, '_blank', 'width=800, height=600, toolbar=no, top=50, left=50');
		}
	}	

	if(rede == 'whatsapp'){
		if(url == 'mobile'){
			window.open("whatsapp://send?text="+link, '_blank');
		}else{
			window.open("https://web.whatsapp.com/send?text="+link, '_blank');
		}
	}

	if(rede == 'twitter'){
		if(url == 'mobile'){
			window.open("https://twitter.com/home?status="+link, '_blank');
		}else{
			window.open("https://twitter.com/home?status="+link, '_blank', 'width=800, height=600, toolbar=no, top=50, left=50');
		}
	}
};