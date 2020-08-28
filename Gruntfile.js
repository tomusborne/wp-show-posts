module.exports = function (grunt) {
    'use strict';
    // Project configuration
    var autoprefixer = require('autoprefixer');

	const sass = require('node-sass');

	var pkgInfo = grunt.file.readJSON('package.json');

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        postcss: {
            options: {
                map: false,
                processors: [
                    autoprefixer({
                        cascade: false
                    })
                ]
            },
            style: {
                expand: true,
                src: [
					'css/wp-show-posts.css',
                ]
            }
        },

        cssmin: {
            options: {
                keepSpecialComments: 0
            },
            css: {
                files: [
                    {
                        src: 'css/wp-show-posts.css',
                        dest: 'css/wp-show-posts.min.css',
					},
                ]
            }
        },

        addtextdomain: {
            options: {
                textdomain: 'wp-show-posts',
            },
            target: {
                files: {
                    src: [
                        '*.php',
                        '**/*.php',
                        '!node_modules/**',
                        '!php-tests/**',
                        '!bin/**',
                    ]
                }
            }
		},

		copy: {
            main: {
                options: {
                    mode: true
                },
                src: [
                    '**',
                    '!node_modules/**',
                    '!build/**',
                    '!css/sourcemap/**',
                    '!.git/**',
                    '!.github/**',
                    '!bin/**',
                    '!.gitlab-ci.yml',
                    '!cghooks.lock',
                    '!tests/**',
                    '!*.sh',
                    '!*.map',
                    '!Gruntfile.js',
                    '!package.json',
                    '!.gitignore',
                    '!.gitattributes',
                    '!phpunit.xml',
                    '!README.md',
                    '!sass/**',
                    '!**/sass/**',
                    '!vendor/**',
                    '!composer.json',
                    '!composer.lock',
                    '!package-lock.json',
                    '!phpcs.xml.dist',
                ],
                dest: 'wp-show-posts/'
            }
        },

        compress: {
            main: {
                options: {
                    archive: 'wp-show-posts-' + pkgInfo.version + '.zip',
                    mode: 'zip',
                    level: 5,
                },
                files: [
                    {
                        src: [
                            './wp-show-posts/**'
                        ]

                    }
                ]
            }
        },

        clean: {
            main: ["wp-show-posts"],
            zip: ["*.zip"]
        },
	});

    // Load grunt tasks
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-wp-i18n');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-contrib-clean');

    // SASS compile
    grunt.registerTask('scss', ['sass']);

    // Style
    grunt.registerTask('style', ['postcss:style']);

    // Style and min
    grunt.registerTask('build', ['style', 'cssmin:css']);

	// Package things up
	grunt.registerTask('package', ['clean:zip', 'copy:main', 'compress:main', 'clean:main']);

    grunt.util.linefeed = '\n';
};
