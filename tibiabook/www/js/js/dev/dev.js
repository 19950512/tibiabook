class Dev {

   async add(evento, elemento, funcao) {
        if(elemento !== null){
            if(window.addEventListener){
                elemento.addEventListener(evento, function(evento){
                    funcao(evento);
                }, true);
            }else{
                elemento.attachEvent("on"+evento, function(){
                    funcao(evento);
                });
            }
        }
    }

    debounce(func, wait, immediate){

        let timeout;
        return function(...args){

            const context = this;

            const later = () => {

                timeout = null;
                if(!immediate){
                    func.apply(context, args);
                }
            };

            const callNow = immediate && !timeout;

            clearTimeout(timeout);

            timeout = setTimeout(later, wait);

            if (callNow){
                func.apply(context, args);
            }
        };
    }

    targt(evts) {
        return evts.target;
    }

    trigger (evts, elemento){
        if(document.createEvent){
            var Evento = document.createEvent('HTMLEvents');
            Evento.initEvent(evts, true, true);
            elemento.dispatchEvent(Evento);
        }else{
            var Evento = document.createEventObject();
            elemento.fireEvent('on'+evts, Evento);
        }
    }

    id(element){
        return document.getElementById(element);
    }

    delay(funcao, time){
        setTimeout( funcao, time );
    }

    testJSON(text){
        if (typeof text !== "string"){
            return false;
        }
        try{
            return true;
        }
        catch (error){
            return false;
        }
    }

   async ajax(url, data){

		var result = [];
		for(var i in data){
			result.push([i]+'='+data [i]);
		}

		try {

			return fetch(url, {
				method: 'POST',
				mode: 'cors',
				headers: {
             	   'Content-Type':'application/x-www-form-urlencoded'
				},
				body: result.join('&')
			}).then(resposta => {

				let res = resposta.clone().json();
			
				return res;

			}).catch (error => {
					console.error(error)
				}
			);

		} catch (erro) {
			console.error('ERROW: ' + erro);
		}
    }

    mensagem(mensagem, tipo = 'blue', time = 3000){
        
        let classeBG = (tipo == 'blue') ? 'feed_blue' : 'feed_red';

        feed_back.classList.add(classeBG);
        feed_back.style.opacity = '1';
        feed_back.style.top = '-1px';
        feed_back.innerHTML = '<p>'+mensagem+'</p>';
        
        this.delay( f => {
        
            feed_back.style.opacity = null;
            feed_back.style.top = null;
            feed_back.classList.remove(classeBG);
            feed_back.innerHTML = '';

        }, time);
    }
}

var dev = new Dev();