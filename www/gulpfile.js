/**
** CONFIGURAÇÃÕES PARA O NOTIFICATION
**/
/**
** DEPENDENCIAS


	# Node Versão 8
	curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
	sudo apt-get install -y nodejs

	# NPM ultima versão
	npm install npm@latest -g

	# verificar Node e npm instalado,
	node -v
	npm -v

	# Instalação Gulp
	# Gulp
	sudo npm install gulp-cli -g
	sudo npm install gulp -D

	# Dependencias
	# gulp-uglify-es
	npm install --save-dev gulp-uglify-es

	# gulp-rename
	npm i gulp-rename

	# gulp-concat
	npm install --save-dev gulp-concat

	# gul-sass
	npm install gulp-sass --save-dev
	'Se der problema com o Sass, execute isso'
	npm rebuild node-sass

	# gulp-notify
	npm i gulp-notify

	# gulp-sourcemaps
	npm i gulp-sourcemaps


	# ERRO ESCUTA GULP
	gulp watch fails with error: Error: watch ... ENOSPC

	( SOLUÇÃO )
	- no terminal -
	echo fs.inotify.max_user_watches=524288 | sudo tee -a /etc/sysctl.conf
**/

const projeto = 'Sites',
		msg		 = 'O arquivo "<%= file.relative %>" foi compilado com sucesso!';

let site;

/*
Oque deve conter em site. (arquivo Sites.json)
const sites = {
	"Admin": {
		"nome": "Admin",
		"dominio": "admin.local",
		"namespace": "Admin",
		"link": "http://admin.local:80/",
		"www": "../Sites/Admin/www/"
	}
};
*/

var gulp = require('gulp'),
  connect = require('gulp-connect-php'),
	browserSync = require('browser-sync'),
	sass	 = require('gulp-sass'),
	reload = browserSync.reload,
	uglify = require('gulp-uglify-es').default,
	rename = require('gulp-rename'),
	concat = require('gulp-concat'),
	notify = require('gulp-notify'),
  	fs      = require('fs'),
	argv	 = require('yargs').argv,
	sourcemaps = require('gulp-sourcemaps');

var listaArquivosSiteJS = [];

sites = {
"tibiabook.local":{
		"dominio":"tibiabook.local",
		"nome":"Tibiabook",
		"namespace":"tibiabook",
		"status":1,
		"statics":"\/\/statics.tibiabook.local",
		"link": "http://tibiabook.local:80/",
		"www": "../tibiabook/www/"
	}
};
/**
** FUNÇÕES
**/	

gulp.task('dev_js', function(cb){

  // Função compila o dev.JS com Map para Debugar
  return gulp.src(sites[site].www + 'js/js/dev/dev.js')
    .pipe(sourcemaps.init())
    .pipe(rename('dev.min.js'))
    .pipe(sourcemaps.write('./dev/map'))
    .pipe(gulp.dest(sites[site].www + 'js'))
    .on('error', function(err) {
        notify().write(err);
        this.emit('end');
    })
});

gulp.task('js', function(cb){
  // Função compila o dev.JS com Map para Debugar
  return gulp.src(listaArquivosSiteJS)
    .pipe(sourcemaps.init())
    .pipe(concat('site.min.js'))
    .pipe(sourcemaps.write('./site/map'))
    .pipe(gulp.dest(sites[site].www + 'js'))
      .pipe(reload({ stream:true }))
      .on('error', function(err) {
         /// notify().write(err);
          done(erro); 
      })
});

gulp.task('js_producao', function(cb){
  // Função compila o dev.JS com Map para Debugar
  return gulp.src(listaArquivosSiteJS)
    .pipe(uglify())
    .pipe(rename('site.min.js'))
    .pipe(gulp.dest(sites[site].www + 'js'))
    .on('error', function(err) {
        notify().write(err);
        this.emit('end');
    })
});

gulp.task('icones', function(){
  // Função compila o SCSS com Map para Debugar
  var sassFiles = sites[site].www + 'css/icons/icones.scss',
      cssDest = sites[site].www + 'css';
    gulp.src(sassFiles)
      .pipe(sourcemaps.init())
      .pipe(sass({outputStyle: 'compiled'}))
      .pipe(rename('icones.min.css'))
      .pipe(sourcemaps.write('./map'))
      .pipe(gulp.dest(cssDest))
      .on('error', function(err) {
          notify().write(err);
          this.emit('end');
      })
    //  .pipe(notify({ title:projeto+' - Desenvolvimento', message: msg }));
});

gulp.task('dev_js_producao', function(cb){
  // Função compila o dev.JS com Map para Debugar
  return gulp.src(sites[site].www + 'js/js/dev/dev.js')
    .pipe(uglify())
    .pipe(rename('dev.min.js'))
    .pipe(gulp.dest(sites[site].www + 'js'))
    .on('error', function(err) {
        notify().write(err);
        this.emit('end');
    })
});

gulp.task('scss', function(){

  // Função compila o SCSS com Map para Debugar
  var sassFiles = sites[site].www + 'css/scss/main.scss',
      cssDest = sites[site].www + 'css';

   return gulp.src(sassFiles)
      .pipe(sourcemaps.init())
      .pipe(sass({outputStyle: 'compiled'}))
      .pipe(rename('site.min.css'))
      .pipe(sourcemaps.write('./map'))
      .pipe(gulp.dest(cssDest))
      .pipe(reload({ stream:true }))
      .on('error', function(err) {
         /// notify().write(err);
          done(erro); 
      })
});

gulp.task('scss_producao', function(){

  // Função compila o SCSS com Map para Debugar
  var sassFiles = sites[site].www + 'css/scss/main.scss',
      cssDest = sites[site].www + 'css';
   return gulp.src(sassFiles)
      .pipe(sourcemaps.init())
      .pipe(sass({outputStyle: 'compressed'}))
      .pipe(rename('site.min.css'))
      .pipe(gulp.dest(cssDest))
      .pipe(reload({ stream:true }))
      .on('error', function(err) {
         /// notify().write(err);
          done(erro); 
      })
});

gulp.task('default', function() {
});

gulp.task('prod', function() {
	if(checkSite() === false){
		return false;
	}

	if(!sites[site]){
		console.log("Ops, o site informado não existe.");
		return false;
	}

	/* CSS */
	gulp.watch([sites[site].www + 'css/scss/**/*.scss'], gulp.series('scss_producao'));

	/* JS */
	gulp.watch(sites[site].www + 'js/js/site/**/*.js', gulp.series('js_producao'));

	/* JS DEV */
	gulp.watch(sites[site].www + 'js/js/dev/dev.js', gulp.series('dev_js_producao'));

	lerArquivosJSdoDiretorio();
});

gulp.task('dev', function() {

	if(checkSite() === false){
		return false;
	}

	if(!sites[site]){
		console.log("Ops, o site informado não existe.");
		return false;
	}

	connect.server({}, function (){
		browserSync.init({
			proxy: sites[site].link
		});
	});

	/* ICONES */
	gulp.watch([sites[site].www + 'css/icons/icones.scss'], gulp.series('icones'));

	/* CSS */
	gulp.watch([sites[site].www + 'css/scss/**/*.scss'], gulp.series('scss'));

	/* JS */
	gulp.watch(sites[site].www + 'js/js/site/**/*.js', gulp.series('js'));
	
	/* JS DEV */
	gulp.watch(sites[site].www + 'js/js/dev/dev.js', gulp.series('dev_js'));

	lerArquivosJSdoDiretorio();
});

lerArquivosJSdoDiretorio = f => {

	fs.readdir(sites[site].www + 'js/js/site/', (err, files) => {
	  files.forEach(file => {
	    listaArquivosSiteJS.push(sites[site].www + 'js/js/site/' + file);
	  });
	});
}

checkSite = () => {

  /* Se não for informado o site, ERRO */
  if(argv.site === undefined || !isNaN(argv.site)){
    console.error('\x1b[31m', '\n\n\n########## A T E N Ç Ã O ###########\n\n\nInforme o site que você deseja trabalhar!, exemplo: \nnpm run dev --site dominio.local\n\n\n');

    return false;
  }

  /* Aqui é armazenado o site que está sendo trabalhado, dominio.local*/
  site = argv.site;

  return true;
}