name = ${APP}/js/${ACTION}/main
out = ${ACTION}/build.js
baseUrl = ./

dev:
	r.js -o name=$(name) out=$(out) baseUrl=$(baseUrl) optimize=none

prod:
	r.js -o name=$(name) out=$(out) baseUrl=$(baseUrl) optimize=uglify