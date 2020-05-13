var lockExitMessage = '',
    XHRPopState = '',
    XHRPopLastController = '/',
    XHRPopStateScroll = {},
    lockChangePageFn = false,
    lockChangePage = false,
    lockClosePage = false,
    XHRPopStateShowStatus = true;

/* Variáveis do push */
var controlador 	= '/',
    action 			= '',
    respostaAjax 	= '',
    pushLoaderID 	= 'push-loader',
    renderID		= 'content'; /* Aqui irá o ID do elemento no DOM que recebera TODO o conteúdo do request 'HTML'*/

const init = configObj => {

    /* Seta as variáveis por Default */
    var xhrfn = function(){};
    var lockChangePageFn = function(){};
    var lockExitMessage = '';

    if(typeof(configObj.xhrfn) === 'function'){
        xhrfn = configObj.xhrfn;
    }

    if(typeof(configObj.lockChangePageFn) === 'function'){
        lockChangePageFn = configObj.lockChangePageFn;
    }

    if(configObj.lockExitMessage){
        lockExitMessage = configObj.lockExitMessage;
    }

    /* POPSTATE EVENT */
    dev.add('popstate', window, function(evts){

        if(lockChangePage === true){
            lockChangePageFn(window.location.href);
            return false;
        }

        var host = window.location.protocol+'//'+window.location.host;
        var controler = window.location.href.replace(host, '')+'!popstate';
        xhrfn(controler, function(){});

    });

    /* CLICK EVENTS */
    dev.add('click', document, function(evts){

        if(!navigator.onLine){
            return false;
        }

        var elemt = evts.target;

        var expJs = new RegExp('javascript:', 'i');
        var expFTP = new RegExp('ftp:', 'i');
        var expMail = new RegExp('mailto:', 'i');
        var expWhatsapp = new RegExp('whatsapp:', 'i');

        var domain = window.location.hostname;

        if(elemt.parentElement !== null && elemt.nodeName !== 'BUTTON' && elemt.parentElement.nodeName === 'BUTTON'){
            elemt = elemt.parentElement;
        }

        if(elemt.nodeName === 'BUTTON' && elemt.getAttribute('data-href') && elemt.getAttribute('data-href') !== false){

            var hrefDomain = elemt.getAttribute('data-href').replace('http://', '');
            hrefDomain = hrefDomain.replace('https://', '');

            var re = new RegExp('^\/', 'i');

            if(re.test(hrefDomain) === true){
                hrefDomain = domain+hrefDomain;
            }

            var urlIn = new RegExp('^'+domain, 'i');

            if(urlIn.test(hrefDomain) === true){
                goXHR(elemt.getAttribute('data-href'), xhrfn, lockChangePageFn);
            }else{
                var a = document.createElement('a');
                a.href = elemt.getAttribute('data-href');
                dev.trigger('click', a);
            }
        }else{

            var flag = true;
            while(flag === true){

                /* SE NÃO FO UM <a> */
                if(elemt.parentNode !== null && elemt.nodeName !== 'A'){
                    elemt = elemt.parentNode;
                }else{
                    /* SE FOR UM <a> */
                    flag = false;

                    if(elemt.href){

                        /* Remove o protocolo, http:// OU https:// */
                        var hrefDomain = elemt.href.replace('http://', '');
                        hrefDomain = hrefDomain.replace('https://', '');

                        var urlIn = new RegExp('^'+domain, 'i');

                        if(urlIn.test(hrefDomain) === true && !elemt.getAttribute('data-href')){

                            /* GOXHR*/
                            if(expJs.test(elemt.href) === false ||
                                expFTP.test(elemt.href) === false ||
                                expMail.test(elemt.href) === false ||
                                expWhatsapp.test(elemt.href) === false ||
                                !elemt.getAttribute('data-href')){

                                if(evts.stopPropagation){
                                    evts.stopPropagation();
                                }
                                if(evts.preventDefault){
                                    evts.preventDefault();
                                }

                                goXHR(elemt.href, xhrfn, lockChangePageFn);
                            }

                        }
                    }
                }
            }
        }
    });

    /* beforeunload EVENT  */
    dev.add('beforeunload', window, function(evts){
        if(lockClosePage === true){

            evts.cancelBubble = true;

            evts.returnValue = lockExitMessage;

            if(evts.stopPropagation){
                evts.stopPropagation();
            }

            if(evts.preventDefault){
                evts.preventDefault();
            }

            return lockExitMessage;
        }
    });
}

const goXHR = (controler, xhrfn, lockChangePageFn) => {

    if(lockChangePage === true && lockChangePageFn){
        lockChangePageFn(controler);
        return false;
    }

    var host = window.location.protocol+'//'+window.location.host;
    var ctrlpage = window.location.href.replace(host, '');
    ctrlpage = ctrlpage.replace(/\?.*$/, '');
    XHRPopStateScroll[ctrlpage] = window.scrollY || window.pageYOffset || document.documentElement.scrollTop;

    xhrfn(controler, function(){
        history.pushState({}, '', controler);
    });
}

const url = () => {

    /* PEGA O PATHNAME DA URL - TUDO DEPOIS DO PRIMEIRO /, exp /contato */
    let url = window.location.pathname;

    /* IGNORA A VERSÃO MOBILE, EXP: /mobile/contato */
    url = url.replace(mobile, '');

    /* QUEBRA A URL */
    url = url.split('/');

    /* ATUALIZA A VARIAVEL CONTROLADOR */
    if(url[1]){
        controlador = url[1];
    }

    /* ATUALIZA A VARIAVEL ACTION */
    if(url[2]){
        action = url[2];
    }
}

const getAction = () =>{

    /* ATUALIZA A ACTION */
    url();

    return action;
}

const getControlador = () => {

    /* ATUALIZA A CONTROLADOR */
    url();

    return controlador;
}

const getXHRPopStateShowStatus = () => {

    return XHRPopStateShowStatus;
}

const setXHRPopStateShowStatus = st => {

    XHRPopStateShowStatus = st;
}

const seo = res =>{

    /* ATUALIZA O SEO DA PÁGINA PELAS INFORMAÇÕES DA VIEW - CONTROLADOR */
    if(typeof(res) !== 'undefined'){

        if(res.title){
            document.title = res.title;
        }
        if(res.description){
            document.querySelector('meta[name="description"]').setAttribute("content", res.description);
        }
    }
}

const xhrfn = (controler, doneCallFn) => {

    /* pushLoader é o elemento que da o feedback para o usuário de que o push está acontecendo (pré loader) */
    /* render é o elemento no DOM que irá receber todo o HTML do Push */
    const pushLoader 	= document.getElementById(pushLoaderID);
    const render 		= document.getElementById(renderID);

    var expPopstate = /!popstate+$/g;
    var expHash = /#[.*\S]+$/g;
    var expHashExtract = /#([.*\S]+)$/i;
    var atualLocation = XHRPopLastController.replace(expHash, '');

    controler = controler.replace(expPopstate, '');
    var testHash = controler;

    /* SCROLL TO HASH ELEMENT */
    if(expHashExtract.test(controler) === true){
        var idByHash = controler.match(expHashExtract)[1];
        if(a.id(idByHash)){
            var idByHashTop = a.positionAtTop(a.id(idByHash));
            window.scrollTo(0, idByHashTop);
        }else{
            window.scrollTo(0, XHRPopStateScroll[controler]);
        }
    }

    pushLoader.style.width = '10%';
    pushLoader.style.opacity = 1;

    if(XHRPopState){
        if(typeof(XHRPopState.abort) === 'function'){
            if(getXHRPopStateShowStatus() === false){
                console.warn('Cancelando request anterior.');
            }
            setXHRPopStateShowStatus(false);
            XHRPopState.abort();
        }
    }

    setXHRPopStateShowStatus(false);

    XHRPopState = _fetch(controler);

    document.body.style.cursor = 'wait';

    render.classList.remove('push_open');
    render.classList.add('push_close');
    render.innerHTML = `
		<section class="content content_m content_h content_v">
			<h1 class="text-center"><i class="icl ic-spinner-third rotating"></i> aguarde...</h1>
		</section>
		`;
    // 300ms é o tempo da animação



    dev.delay(() => {

        /* SCROLL TO HASH ELEMENT */
        if(expHashExtract.test(testHash) === true){
            var idByHash = testHash.match(expHashExtract)[1];
            if(a.id(idByHash)){
                var idByHashTop = a.positionAtTop(a.id(idByHash));
                window.scrollTo(0, idByHashTop);
            }else{
                window.scrollTo(0, XHRPopStateScroll[testHash]);
            }
        }else{

            controlerscroll = controler.replace(/\?.*$/, '');

            if(XHRPopStateScroll[controlerscroll]){
                window.scrollTo(0, XHRPopStateScroll[controlerscroll]);
            }else if(XHRPopStateScroll['/'+controlerscroll]){
                window.scrollTo(0, XHRPopStateScroll['/'+controlerscroll]);
            }else{
                window.scrollTo(0, 0);
            }
        }
    }, 30);

    let data = XHRPopState
        .then( json => {

            document.body.style.cursor = 'default';
            let metas = '';
            let html = '';

            if(typeof(json.metas) !== 'undefined'){
                metas = json.metas;
            }

            if(json.html){
                html = json.html;
            }

            /* ATUALIZA A VARIAVEL RESPOSTAAJAX */
            respostaAjax = json;

            setXHRPopStateShowStatus(true);

            if(typeof(adminCloseMenu) !== 'undefined'){
                adminCloseMenu();
            }

            doneCallFn();

            pushLoader.style.width = '50%';

            /* ATUALIZA O SEO DA PAGINA */
            seo(metas);

            render.classList.remove('push_close');
            render.innerHTML = html;
            render.classList.add('push_open');

            dev.delay(() => {

                /* SCROLL TO HASH ELEMENT */
                if(expHashExtract.test(testHash) === true){
                    var idByHash = testHash.match(expHashExtract)[1];
                    if(dev.id(idByHash)){
                        var idByHashTop = dev.positionAtTop(dev.id(idByHash));
                        window.scrollTo(0, idByHashTop);
                    }else{
                        window.scrollTo(0, XHRPopStateScroll[testHash]);
                    }
                }else{

                    controlerscroll = controler.replace(/\?.*$/, '');

                    if(XHRPopStateScroll[controlerscroll]){
                        window.scrollTo(0, XHRPopStateScroll[controlerscroll]);
                    }else if(XHRPopStateScroll['/'+controlerscroll]){
                        window.scrollTo(0, XHRPopStateScroll['/'+controlerscroll]);
                    }else{
                        window.scrollTo(0, 0);
                    }
                }

                /* ESTE TRECHO É IMPORTANTISSIMO PARA EXECUTAR O JS DAS VIEWS, SEM ISSO NÃO EXECUTA JS DAS VIEWS! */
                var scripts = render.getElementsByTagName('script');
                var script_code = '';
                for(x in scripts){
                    if(scripts[x].innerHTML){
                        script_code += scripts[x].innerHTML;
                        listaScript.push(scripts[x].innerHTML);
                    }
                }

                /* SE TIVER DE0 FATO, ALGUM SCRIPT */
                if(script_code !== ''){
                    var blob = new Blob([script_code], {type: 'text/javascript'});
                    var urlScript = window.URL || window.webkitURL;
                    var url = urlScript.createObjectURL(blob);
                    pushScript(url);
                }
            }, 30);

            /* CONTROLADOR INDEX */
            if(controlador == "/"){

                pushLoader.style.width = '100%';

                dev.delay(() => pushLoader.style.opacity = 0, 500);

                /* OUTROS */
            }else{

                dev.delay(() => {

                    pushLoader.style.width = '100%';
                    dev.delay(() => pushLoader.style.opacity = 0, 500);
                    dev.delay(() => pushLoader.style.width = '0%', 1000);

                }, 30);
            }

        });
}

/*
	_fetch de fato, é o responsável por fazer o request para o servidor.
	OBS: usa-se o POST['push'] para justamente o backend entender e saber oque fazer com o request.
*/


/* Lista armazena os script qu0e são Ex0ecutados */

var listaScript = new Array();
const pushScript = (url, callback) => {
    /* CRIA O SCRIPT NO HEAD*/
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;

    /* ISSO AQUI, É PARA EVITAR QUE VÁRIOS SCRIPTS FIQUEM NO HEAD */
    var scripts_existentes = head.querySelectorAll('[type="text/javascript"]');
    if(!(scripts_existentes.length <= 0)){
        for(var i = 0; i < scripts_existentes.length; i++){
            scripts_existentes[i].remove();
        }
    }

    /* SE HOUVER UM CALLBACK */
    script.onreadystatechange = callback;
    script.onload = callback;

    /* ADICIONA O SCRIPT / BLOB */
    head.appendChild(script);

}

const _fetch = async (controler) => {

    try {

        return fetch(controler, {
            method: 'POST',
            mode: 'cors',
            headers: {
                'Content-Type':'application/x-www-form-urlencoded'
            },
            body: 'push=push'
        }).then(resposta => {

            let res = resposta.clone().json();
            return res;

        }).catch(error => {
                console.error(error)
            }
        );

    }catch(erro){
        console.error('ERROW: ' + erro);
    }
}

init({
    'lockExitMessage': lockExitMessage,
    'xhrfn': xhrfn,
    'lockChangePageFn': lockChangePageFn
});