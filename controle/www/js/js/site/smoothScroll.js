window.ancoraativa = 0;
window.smoothScroll = {
	velocidade_padrao: 2000,
	easeInOutQuart: (time, from, distance, dutation) => {

		if ((time /= dutation / 2) < 1){

			return distance / 2 * time * time * time * time + from;
		}

		return -distance / 2 * ((time -= 2) * time * time * time - 2) + from;
	},
	getScrollTopByHref: element => document.querySelector(element.getAttribute('href')).offsetTop,
	scrollToPosition: (to, dutation) => {
		smoothScroll.smoothScrollTo(0, to, dutation);
	},
	scrollToIdOnClick: (event, dutation) => {

		if(ancoraativa !== 2){

			ancoraativa = 1;

			if(ancoraativa === 1){
				event.preventDefault();
				var to = smoothScroll.getScrollTopByHref(event.currentTarget);
				smoothScroll.scrollToPosition(to, dutation);
			}
		}
	},
	smoothScrollTo: (endX, endY, dutation = smoothScroll.velocidade_padrao) => {

		/* Caso houver um header para descontar a rolagem */
		var header = 0;
		/* PC */
		dev.id('headerFixo') ? header = Boss.getById('headerFixo').clientHeight : false;

		/* Mobile*/
		dev.id('headerMobile') ? header = Boss.getById('headerMobile').clientHeight : false;

		var startY = window.scrollY || window.pageYOffset;
		var distanceY = endY - (startY + header);
		var startTime = new Date().getTime();

		var timer = setInterval(function () {

			var time = new Date().getTime() - startTime;
			var newY = smoothScroll.easeInOutQuart(time, startY, distanceY, dutation);

			if (time >= dutation || ancoraativa == 3){

				clearInterval(timer);
				ancoraativa = 0;
			}

			window.scroll(0, newY);
		}, 1000 / 60);
	},
	init: () => {
		
		dutation = arguments.length > 0 ? arguments[0] : 2000;
		if(arguments.length > 1) throw new Error("Too many arguments! Expected 1.");

		var menuItems = document.querySelectorAll('a[href^="#"]');

		menuItems.forEach(function (item) {

			dev.add('click', item, function(evt){
				smoothScroll.scrollToIdOnClick(evt, dutation);
			});
		});
	},
	go: (element, destino, duration = smoothScroll.velocidade_padrao) => {

		if(ancoraativa !== 2){

			ancoraativa = 1;

			if(ancoraativa === 1){
				ancoraativa = 2;

				if(Boss.getById(destino)){

					var yorigem = element.offsetTop;
					var ydestino = Boss.getById(destino).offsetTop;

					smoothScroll.smoothScrollTo(yorigem, ydestino, duration);
				}
			}
		}
	},
	goTop: () => {
		if(ancoraativa !== 2){

			ancoraativa = 1;

			if(ancoraativa === 1){
				var yorigem = window.scrollY;
				ancoraativa = 2;
				var ydestino = Boss.getById('push-loader').offsetTop;
				smoothScroll.smoothScrollTo(yorigem, ydestino, this.velocidade_padrao);
			}
		}
	}
};

/* PAUSA O SCROLL - SUAVE QUANDO PRESSIONA AS TECLAS, PAGEDOWN, PAGEUP, SETA CIMA E BAIXO */
dev.add('keydown', window, function(event){
	var btn = event.keyCode;
	if(btn === 38 || btn === 40 || btn === 35 || btn === 36 || btn === 34 || btn === 33 || btn === 32){
		ancoraativa = 3;
	}
});

/* PAUSA O SCROLL - SUAVE QUANDO O SCROLL DO MOUSE É ACIONADO (usuario tentando usar o scroll) */
dev.add('wheel', window, () => ancoraativa = 3);

/* PAUSA O SCROLL - SUAVE QUANDO O SCROLL DO MOUSE É ACIONADO (usuario tentando usar o scroll) */
dev.add('touchmove', window, () => ancoraativa = 3);