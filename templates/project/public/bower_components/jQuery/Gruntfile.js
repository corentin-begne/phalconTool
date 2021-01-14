"use strict";

module.exports = function( grunt ) {
	function readOptionalJSON( filepath ) {
		var stripJSONComments = require( "strip-json-comments" ),
			data = {};
		try {
			data = JSON.parse( stripJSONComments(
				fs.readFileSync( filepath, { encoding: "utf8" } )
			) );
		} catch ( e ) {}
		return data;
	}

	var fs = require( "fs" ),
		gzip = require( "gzip-js" ),
		isTravis = process.env.TRAVIS,
		travisBrowsers = process.env.BROWSERS && process.env.BROWSERS.split( "," ),
		CLIEngine = require( "eslint" ).CLIEngine;

	if ( !grunt.option( "filename" ) ) {
		grunt.option( "filename", "jquery.js" );
	}

	grunt.initConfig( {
		pkg: grunt.file.readJSON( "package.json" ),
		dst: readOptionalJSON( "dist/.destination.json" ),
		compare_size: {
			files: [ "dist/jquery.js", "dist/jquery.min.js" ],
			options: {
				compress: {
					gz: function( contents ) {
						return gzip.zip( contents, {} ).length;
					}
				},
				cache: "build/.sizecache.json"
			}
		},
		babel: {
			options: {
				sourceMap: "inline",
				retainLines: true,
				plugins: [ "@babel/transform-for-of" ]
			},
			tests: {
				files: {
					"test/data/core/jquery-iterability-transpiled.js":
						"test/data/core/jquery-iterability-transpiled-es6.js"
				}
			}
		},
		build: {
			all: {
				dest: "dist/jquery.js",
				minimum: [
					"core",
					"selector"
				],

				// Exclude specified modules if the module matching the key is removed
				removeWith: {
					ajax: [ "manipulation/_evalUrl", "deprecated/ajax-event-alias" ],
					callbacks: [ "deferred" ],
					css: [ "effects", "dimensions", "offset" ],
					"css/showHide": [ "effects" ],
					deferred: {
						remove: [ "ajax", "effects", "queue", "core/ready" ],
						include: [ "core/ready-no-deferred" ]
					},
					event: [ "deprecated/ajax-event-alias", "deprecated/event" ]
				}
			}
		},
		jsonlint: {
			pkg: {
				src: [ "package.json" ]
			}
		},
		eslint: {
			options: {
				maxWarnings: 0
			},

			// We have to explicitly declare "src" property otherwise "newer"
			// task wouldn't work properly :/
			dist: {
				src: [ "dist/jquery.js", "dist/jquery.min.js" ]
			},
			dev: {
				src: [
					"src/**/*.js",
					"Gruntfile.js",
					"test/**/*.js",
					"build/**/*.js",

					// Ignore files from .eslintignore
					// See https://github.com/sindresorhus/grunt-eslint/issues/119
					...new CLIEngine()
						.getConfigForFile( "Gruntfile.js" )
						.ignorePatterns.map( ( p ) => `!${ p }` )
				]
			}
		},
		testswarm: {
			tests: [

				// A special module with basic tests, meant for not fully
				// supported environments like jsdom. We run it everywhere,
				// though, to make sure tests are not broken.
				"basic",

				"ajax",
				"animation",
				"attributes",
				"callbacks",
				"core",
				"css",
				"data",
				"deferred",
				"deprecated",
				"dimensions",
				"effects",
				"event",
				"manipulation",
				"offset",
				"queue",
				"selector",
				"serialize",
				"support",
				"traversing",
				"tween"
			]
		},
		karma: {
			options: {
				customContextFile: "test/karma.context.html",
				customDebugFile: "test/karma.debug.html",
				customLaunchers: {
					ChromeHeadlessNoSandbox: {
						base: "ChromeHeadless",
						flags: [ "--no-sandbox" ]
					}
				},
				frameworks: [ "qunit" ],
				middleware: [ "mockserver" ],
				plugins: [
					"karma-*",
					{
						"middleware:mockserver": [
							"factory",
							require( "./test/middleware-mockserver.js" )
						]
					}
				],
				client: {
					qunit: {

						// We're running `QUnit.start()` ourselves via `loadTests()`
						// in test/jquery.js
						autostart: false
					}
				},
				files: [
					"test/data/jquery-1.9.1.js",
					"node_modules/sinon/pkg/sinon.js",
					"node_modules/native-promise-only/lib/npo.src.js",
					"node_modules/requirejs/require.js",
					"test/data/testinit.js",

					"test/jquery.js",

					{
						pattern: "dist/jquery.*",
						included: false,
						served: true,
						nocache: true
					},
					{
						pattern: "src/**",
						type: "module",
						included: false,
						served: true,
						nocache: true
					},
					{
						pattern: "amd/**",
						included: false,
						served: true,
						nocache: true
					},
					{ pattern: "node_modules/**", included: false, served: true },
					{
						pattern: "test/**/*.@(js|css|jpg|html|xml|svg)",
						included: false,
						served: true,
						nocache: true
					}
				],
				reporters: [ "dots" ],
				autoWatch: false,
				concurrency: 3,
				captureTimeout: 20 * 1000,
				singleRun: true
			},
			main: {
				browsers: isTravis && travisBrowsers || [ "ChromeHeadless", "FirefoxHeadless" ]
			},
			esmodules: {
				browsers: isTravis && travisBrowsers || [ "ChromeHeadless" ],
				options: {
					client: {
						qunit: {

							// We're running `QUnit.start()` ourselves via `loadTests()`
							// in test/jquery.js
							autostart: false,

							esmodules: true
						}
					}
				}
			},
			amd: {
				browsers: isTravis && travisBrowsers || [ "ChromeHeadless" ],
				options: {
					client: {
						qunit: {

							// We're running `QUnit.start()` ourselves via `loadTests()`
							// in test/jquery.js
							autostart: false,

							amd: true
						}
					}
				}
			},

			jsdom: {
				options: {
					files: [
						"test/data/jquery-1.9.1.js",
						"test/data/testinit-jsdom.js",

						// We don't support various loading methods like esmodules,
						// choosing a version etc. for jsdom.
						"dist/jquery.js",

						// A partial replacement for testinit.js#loadTests()
						"test/data/testrunner.js",

						// jsdom only runs basic tests
						"test/unit/basic.js",

						{
							pattern: "test/**/*.@(js|css|jpg|html|xml|svg)",
							included: false,
							served: true
						}
					]
				},
				browsers: [ "jsdom" ]
			},

			// To debug tests with Karma:
			// 1. Run 'grunt karma:chrome-debug' or 'grunt karma:firefox-debug'
			//    (any karma subtask that has singleRun=false)
			// 2. Press "Debug" in the opened browser window to start
			//    the tests. Unlike the other karma tasks, the debug task will
			//    keep the browser window open.
			"chrome-debug": {
				browsers: [ "Chrome" ],
				singleRun: false
			},
			"firefox-debug": {
				browsers: [ "Firefox" ],
				singleRun: false
			},
			"ie-debug": {
				browsers: [ "IE" ],
				singleRun: false
			}
		},
		watch: {
			files: [ "<%= eslint.dev.src %>" ],
			tasks: [ "dev" ]
		},
		uglify: {
			all: {
				files: {
					"dist/<%= grunt.option('filename').replace('.js', '.min.js') %>":
						"dist/<%= grunt.option('filename') %>"
				},
				options: {
					preserveComments: false,
					sourceMap: true,
					sourceMapName:
						"dist/<%= grunt.option('filename').replace('.js', '.min.map') %>",
					report: "min",
					output: {
						"ascii_only": true
					},
					banner: "/*! jQuery v<%= pkg.version %> | " +
						"(c) OpenJS Foundation and other contributors | jquery.org/license */",
					compress: {
						"hoist_funs": false,
						loops: false
					}
				}
			}
		}
	} );

	// Load grunt tasks from NPM packages
	require( "load-grunt-tasks" )( grunt );

	// Integrate jQuery specific tasks
	grunt.loadTasks( "build/tasks" );

	grunt.registerTask( "lint", [
		"jsonlint",

		// Running the full eslint task without breaking it down to targets
		// would run the dist target first which would point to errors in the built
		// file, making it harder to fix them. We want to check the built file only
		// if we already know the source files pass the linter.
		"eslint:dev",
		"eslint:dist"
	] );

	grunt.registerTask( "lint:newer", [
		"newer:jsonlint",

		// Don't replace it with just the task; see the above comment.
		"newer:eslint:dev",
		"newer:eslint:dist"
	] );

	grunt.registerTask( "test:fast", "node_smoke_tests" );
	grunt.registerTask( "test:slow", [
		"promises_aplus_tests",
		"karma:jsdom"
	] );

	grunt.registerTask( "test:prepare", [
		"qunit_fixture",
		"babel:tests"
	] );

	grunt.registerTask( "test", [
		"test:prepare",
		"test:fast",
		"test:slow"
	] );

	grunt.registerTask( "dev", [
		"build:*:*",
		"newer:eslint:dev",
		"newer:uglify",
		"remove_map_comment",
		"dist:*",
		"qunit_fixture",
		"compare_size"
	] );

	grunt.registerTask( "default", [
		"eslint:dev",
		"build:*:*",
		"amd",
		"uglify",
		"remove_map_comment",
		"dist:*",
		"test:prepare",
		"eslint:dist",
		"test:fast",
		"compare_size"
	] );
};
