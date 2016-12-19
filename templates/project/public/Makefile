name = ${APP}/js/${CONTROLLER}/${ACTION}main
out = ${APP}/js/${CONTROLLER}/${ACTION}build.js
baseUrl = ./

dev:
	r.js -o name=$(name) out=$(out) baseUrl=$(baseUrl) optimize=none

preprod:
	r.js -o name=$(name) out=$(out) baseUrl=$(baseUrl) optimize=none

prod:
	r.js -o name=$(name) out=$(out) baseUrl=$(baseUrl) optimize=uglify