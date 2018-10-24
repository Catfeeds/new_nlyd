(function( $ ) {
	'use strict';

	$(function() {
        // Demo installation.
        var onePostData = null;
        var importingDemo = false;
        var importingOnePage = false;
        var contentPartId = null;
        var $importingOnePageContainer;
        var originBtnTxt;

        // Add feedback container.
        var $globalFeedbackContainer = $('<div class="the7-installation-status"></div>');
        $globalFeedbackContainer.hide();
        $('.feature-section').append($globalFeedbackContainer);

        function createFeedbackElement(text, $feedbackContainer) {
            var spinnerHTML = '<span class="spinner is-active" style="float: none; margin: 0"></span> ';
            var $feedbackElement = $('<p>' + spinnerHTML + text + '</p>');

            if (typeof $feedbackContainer !== 'undefined') {
                $feedbackContainer.append($feedbackElement);
            }

            return $feedbackElement;
        }

        function getActions($container) {
            var actions = [];
            $('input[type="checkbox"]:checked', $container).each(function () {
                actions.push($(this).attr('name'));
            });

            return actions;
        };

        function splitStr(str) {
            if (!str) {
                return [];
            }

            return str
                .split(',')
                .map(function (val) {
                    return val.trim();
                })
                .filter(function (val) {
                    return !!val;
                })
        };

        function populateOnePagePostTypeSelect($container) {
            var $select = $container.find('select.dt-dummy-content-post-type');
            $select.html('');
            $.each(onePostData, function (key, val) {
                $select.append('<option id="' + key + '"  value="' + key + '">' + key + '</option>');
            })
            populateOnePagePostUrl($container);
        }

        function populateOnePagePostSelect(value, $container) {
            var $select = $container.find('select.dt-dummy-content-posts');
            $select.empty();
            $.each(onePostData[value], function (key, val) {
                $select.append('<option id="' + key + '"  value="' + key + '">' + val.post_title + '</option>');
            })
            populateOnePagePostUrl($container);
        }

        function populateOnePagePostUrl($container){
            var postId = $container.find('select.dt-dummy-content-posts').val();
            var postType = $container.find('select.dt-dummy-content-post-type').val();
            var urlLink = $container.find('.dt-dummy-one-post-url');

            if ((postId == null) || (postType == null)) {
                urlLink.hide();
                return;
            }
            urlLink.attr("href", onePostData[postType][postId].url);
            urlLink.show();
        }

        function drawOnePageImportingInterface($container) {
            $container.empty();
            $container.hide();
            var htmlData =
            '<div class="dt-dummy-controls-block" style="padding-bottom: 1em;">' +
                '<table class="dt-dummy-field dt-dummy-one-post-data">' +
                    '<tr><td>' + dtDummy.import_msg.one_post_importing_choose_posttype + ': </td><td><select name="post-type" id="post-type" class="dt-dummy-content-post-type"></select></td></tr>' +
                    '<tr><td>' + dtDummy.import_msg.one_post_importing_choose_post + ': </td><td><select name="posts" id="posts" class="dt-dummy-content-posts"></select> <a class="the7-tip dt-dummy-one-post-url" href="#" target="_blank">' + dtDummy.import_msg.one_post_importing_url_msg + '</a></td></tr>'+
                '</table>'+
            '</div>';
            var htmlSubmitButton =
                '<div class="dt-dummy-controls-block">' +
                    '<div class="dt-dummy-button-wrap">'+
                        '<button class="button button-primary dt-dummy-button-import-one-page-submit">' + dtDummy.import_msg.one_post_importing_import + '</button>'+
                    '</div>'+
                '</div>';
            if (typeof $container !== 'undefined') {
                $container.append(htmlData);
                $container.append(htmlSubmitButton);
            }
        }


        function getUser( $container ) {
            return [$('.dt-dummy-content-user', $container).first().val()];
        };

        function addInlineMsg($container, msg, type, wrap) {
            if ( typeof wrap === 'undefined' ) {
                wrap = true;
            }

            msg = ( wrap ? '<p>'+msg+'</p>' : msg );

            var $msg = $('<div class="dt-dummy-inline-msg hide-if-js inline ' + type + '">' + msg + '</div>');
            $container.closest('.dt-dummy-control-buttons').before($msg);
            $msg.fadeIn();
        };

        function removeInlineMsgs($container) {
            $container.closest('.dt-dummy-controls').find('.dt-dummy-inline-msg').fadeOut('400', function() {
                $(this).remove();
            });
        };

        function resetImportingOnePageState($container, forceReset){
            var contentPartIdNew = $container.attr( 'data-dummy-id' ) || '0';

            //check if content the same demo, if not, remove previous content
            if (((typeof $importingOnePageContainer !== 'undefined') && ((contentPartIdNew !== contentPartId) || forceReset) ) ) {
                $importingOnePageContainer.slideDown();
                $importingOnePageContainer.parent().find('.dt-dummy-one-page-importing-interface').remove();
            }
            contentPartId  = contentPartIdNew;
        }

        function displayPHPStatus($container){
            removeInlineMsgs($container);
            $.post(ajaxurl, {
                action: 'the7_demo_content_php_status',
                _wpnonce: dtDummy.status_nonce
            }, function(status) {
                if ( status.success && status.data ) {
                    addInlineMsg($container, status.data, 'error', false);
                }
            })
                .fail(function() {
                    addInlineMsg($container, dtDummy.import_msg.msg_import_fail, 'error');
                });
        };

        function setStatus__Importing($container){
            removeInlineMsgs($container);
            setStatus__Default($container);
            $container.addClass('button--importing').text(dtDummy.import_msg.btn_import);
            spinnerOn($container);
        };

        function spinnerOn($container) {
            var $spinner = $container.siblings('.spinner').first();
            $spinner.addClass('is-active');
        };

        function spinnerOff($container) {
            var $spinner = $container.siblings('.spinner').first();
            $spinner.removeClass('is-active');
            $container.removeClass('button--importing');
            $container.text(originBtnTxt);
        };

        function setStatus__Default($container) {
            $container.removeClass( 'button--importing' );
            spinnerOff($container);
        };

        function importOnePage($container, $feedbackContainer, $dataBlockContainer, ajaxParams) {
            if (importingOnePage || importingDemo) return;
            importingOnePage = true;
            setStatus__Importing($container);
            $feedbackContainer.empty();
            $feedbackContainer.fadeIn(function(){
                var actionName = dtDummy.import_msg.one_post_importing_msg + ' "' +  $dataBlockContainer.find('select.dt-dummy-content-posts option:selected').text() + '"';
                var $feedback = createFeedbackElement(actionName, $feedbackContainer);
                ajaxParams.dummy = "import_one_post";
                ajaxParams.post_to_import = $dataBlockContainer.find('select.dt-dummy-content-posts').val();

                var xhr = $.when();
                xhr = xhr.then(function() {
                    return $.post(
                        ajaxurl,ajaxParams
                    )
                        .then(function(response) {
                            var filter = $.Deferred();
                            if ( response.success ) {
                                filter.resolve(response);
                            } else {
                                filter.reject(response);
                            }
                            return filter.promise();
                        })
                        .done(function(response) {
                            $feedback.replaceWith($('<p class="the7-updated">' + actionName + '</p>'));
                            return response;
                        } )
                        .fail(function(response) {
                            $feedback.replaceWith($('<p class="the7-error">' + actionName + '</p>'));
                            return response;
                        });
                });

                xhr.fail(function(response) {
                    if ( typeof response.data !== 'undefined' && typeof response.data.error_msg !== 'undefined' && response.data.error_msg ) {
                        addInlineMsg($container, response.data.error_msg, 'error');
                    } else {
                        displayPHPStatus($container);
                    }
                });

                xhr.always(function(response) {
                    importingOnePage = false;
                    $feedbackContainer.delay(1000).fadeOut();
                });
            });
        }

	    //import one page
        $('.dt-dummy-control-buttons .dt-dummy-button-import-one-page').on('click', function(event) {
            event.preventDefault();

            if (importingOnePage || importingDemo) return;
            importingDemo = true;
            var $this = $(this);

            originBtnTxt = $this.text();
            var $blockContainer = $this.closest('.dt-dummy-content');

            //prepare variables
            var actions = getActions($blockContainer);
            var users = getUser($blockContainer);
            var xhr = $.when();

            setStatus__Importing($this);

            $globalFeedbackContainer.empty();
            $globalFeedbackContainer.fadeIn();

            resetImportingOnePageState($blockContainer,false);

            var pluginsInstalled = false;
            if (-1 !== actions.indexOf('install_plugins')) {
                pluginsInstalled = true;
                var pluginsToInstall = splitStr($blockContainer.find('input[name=plugins_to_install]').val());
                var pluginsToActivate = splitStr($blockContainer.find('input[name=plugins_to_activate]').val());
                xhr = installPlugins(xhr, $globalFeedbackContainer, pluginsToInstall, pluginsToActivate);
            }

            actions = ["download_package","get_posts"];


            var $feedback;
            var actionName;
            var ajaxParams = {
                action: 'the7_import_demo_content',
                _wpnonce: dtDummy.import_nonce,
                imported_authors: ['admin'],
                user_map: users,
                content_part_id: contentPartId
            };
            actions.forEach(function(action) {
                xhr = xhr.then(function() {
                    actionName = action;
                    if (typeof dtDummy.import_msg[action] !== 'undefined') {
                        actionName = dtDummy.import_msg[action];
                    }
                    ajaxParams.dummy = action;
                    $feedback = createFeedbackElement(actionName, $globalFeedbackContainer);

                    return $.post(
                        ajaxurl,ajaxParams
                    )
                        .then(function(response) {
                            var filter = $.Deferred();

                            if ( response.success ) {
                                filter.resolve(response);
                            } else {
                                filter.reject(response);
                            }

                            return filter.promise();
                        })
                        .done(function(response) {
                            $feedback.replaceWith($('<p class="the7-updated">' + actionName + '</p>'));
                            return response;
                        } )
                        .fail(function(response) {
                            $feedback.replaceWith($('<p class="the7-error">' + actionName + '</p>'));
                            return response;
                        });
                });
            });

            xhr.done(function(response) {
                if ( typeof response.data !== 'undefined'){
                    if ((typeof response.data.response !== 'undefined') && response.data.response === 'getPostsList'){

                            onePostData = response.data.data;
                            $importingOnePageContainer = $this.closest('.dt-dummy-controls-block-import-one-page');

                            $importingOnePageContainer.delay(1100).slideUp(400, function() {
                            //draw one page importing interface
                            var $onePageDataContainer = $('<div class="dt-dummy-one-page-importing-interface"></div>');
                            $importingOnePageContainer.after($onePageDataContainer);
                            drawOnePageImportingInterface($onePageDataContainer);
                            populateOnePagePostTypeSelect($onePageDataContainer);
                            $onePageDataContainer.slideDown();
                            populateOnePagePostSelect($onePageDataContainer.find('select.dt-dummy-content-post-type').val() , $onePageDataContainer);

                            $onePageDataContainer.find('select.dt-dummy-content-post-type').on('change', function(event) {
                                populateOnePagePostSelect(this.value, $onePageDataContainer)
                            });
                            $onePageDataContainer.find('select.dt-dummy-content-posts').on('change', function(event) {
                                populateOnePagePostUrl($onePageDataContainer);
                            });
                            $onePageDataContainer.find('button.dt-dummy-button-import-one-page-submit').on('click', function(event) {
                                importOnePage($this, $globalFeedbackContainer, $onePageDataContainer, ajaxParams);
                            });
                        });
                    }
                }
            });

            xhr.fail(function(response) {
                if ( typeof response.data !== 'undefined' && typeof response.data.error_msg !== 'undefined' && response.data.error_msg ) {
                    addInlineMsg($this, response.data.error_msg, 'error');
                } else {
                    displayPHPStatus($this);
                }
            });

            xhr.always(function() {
                $globalFeedbackContainer.delay(1000).fadeOut();
                setStatus__Default($this);
                importingDemo = false;
            });
        });

		$('.dt-dummy-control-buttons .dt-dummy-button-import').on('click', function(event) {
			event.preventDefault();

            if (importingOnePage || importingDemo) return;

            importingDemo = true;

			var $this = $(this);

            originBtnTxt = $this.text();

			setStatus__Importing($this);

            $globalFeedbackContainer.empty();
            $globalFeedbackContainer.fadeIn();

			var $blockContainer = $this.closest('.dt-dummy-content');
			var actions = getActions($blockContainer);
			var users = getUser($blockContainer);
            var xhr = $.when();

            resetImportingOnePageState($blockContainer, true);

            var pluginsInstalled = false;
            if (-1 !== actions.indexOf('install_plugins')) {
                // Remove plugins installation from actions list.
                actions = actions.filter(function(item) { return 'install_plugins' !== item; });
                pluginsInstalled = true;

                var pluginsToInstall = splitStr($blockContainer.find('input[name=plugins_to_install]').val());
                var pluginsToActivate = splitStr($blockContainer.find('input[name=plugins_to_activate]').val());
                xhr = installPlugins(xhr, $globalFeedbackContainer, pluginsToInstall, pluginsToActivate);
            }

            if (actions.length > 0) {
                actions.unshift('download_package');
                actions.push('cleanup');
            }

			actions.forEach(function(action) {
                xhr = xhr.then(function() {
                    var actionName = action;
                    if (typeof dtDummy.import_msg[action] !== 'undefined') {
                        actionName = dtDummy.import_msg[action];
                    }

                    var $feedback = createFeedbackElement(actionName, $globalFeedbackContainer);

                    return $.post(
                        ajaxurl,
                        {
                            action: 'the7_import_demo_content',
                            dummy: action,
                            _wpnonce: dtDummy.import_nonce,
                            imported_authors: ['admin'],
                            user_map: users,
                            content_part_id: contentPartId
                        }
                    )
                        .then(function(response) {
                            var filter = $.Deferred();

                            if ( response.success ) {
                                filter.resolve(response);
                            } else {
                                filter.reject(response);
                            }

                            return filter.promise();
                        })
                        .done(function(response) {
                            $feedback.replaceWith($('<p class="the7-updated">' + actionName + '</p>'));
                        } )
                        .fail(function() {
                            $feedback.replaceWith($('<p class="the7-error">' + actionName + '</p>'));
                        });
				});
			});

            xhr.done(function() {
                addInlineMsg($this, dtDummy.import_msg.msg_import_success, 'the7-updated');
            });

			xhr.fail(function(response) {
                if ( typeof response.data !== 'undefined' && typeof response.data.error_msg !== 'undefined' && response.data.error_msg ) {
                    addInlineMsg($this, response.data.error_msg, 'error');
                } else {
                    displayPHPStatus($this);
                }
            });

			xhr.always(function() {
                $globalFeedbackContainer.delay(500).fadeOut();
                setStatus__Default($this);
                importingDemo = false;
			});

			if (pluginsInstalled) {
                // Reload page.
                xhr.then(function() { window.scroll(0, 0); window.location.reload(); });
            }

			return false;
		});

		// Import settings deactivation toggle.
		$('#the7-dashboard input[name=install_plugins]').on('click', function() {
            var $this = $(this);
            var $controlBlocks = [
                $this.closest('.dt-dummy-field').siblings('.dt-dummy-import-settings'),
                $this.closest('.dt-dummy-controls').find('.dt-dummy-control-buttons')
            ];

            if ($this.is(':checked')) {
                $controlBlocks.map(function($block) {
                    $block.removeClass('disabled').find(':input').removeAttr('disabled');
                });
            } else {
                $controlBlocks.map(function($block) {
                    $block.addClass('disabled').find(':input').attr('disabled', 'disabled');
                });
            }
        });

        // Bulk install required plugins.
        function installPlugins(xhr, $feedbackContainer, pluginsToInstall, pluginsToActivate) {
            var ajaxUrl = dtDummy.plugins_page_url;
			var $failMsg = $('<p class="the7-error">' + dtDummy.import_msg.plugins_installation_error + '</p>');

            // Install plugins.
            pluginsToInstall.forEach(function(plugin) {
                xhr = xhr.then(function() {
                	var pluginName = plugin;
                	if ( typeof dtDummy.plugins[plugin] !== 'undefined' ) {
                		pluginName = dtDummy.plugins[plugin];
					}

					var $feedback = createFeedbackElement(dtDummy.import_msg.installing_plugin + ' ' + pluginName, $feedbackContainer);

                    return $.post(ajaxUrl, { action: 'tgmpa-bulk-install', just_install: true, noheader: true, plugin: plugin }).done(function(response) {
                    	var $message = $(response).find('.update-php div.error p, .update-php div.updated p').addClass('the7-updated');
                    	// Cleanup message.
                        $message.find('.js-update-details-toggle').remove();
                        $feedback.replaceWith($message);
                    }).fail(function() {
                        $feedback.replaceWith($failMsg);
                        window.location.reload();
					});
                });
            });

            // Activate plugins.
            $.merge(pluginsToActivate, pluginsToInstall);
            xhr = xhr.then(function() {
                var $feedback = createFeedbackElement(dtDummy.import_msg.activating_plugin, $globalFeedbackContainer);

                return $.post(ajaxUrl, { action: 'tgmpa-bulk-activate', noheader: true, plugin: pluginsToActivate }).done(function(response) {
                    var $message = '<p class="the7-updated">' + dtDummy.import_msg.plugins_activated + '</p>';
                    $feedback.replaceWith($message);
                }).fail(function() {
                    $feedback.replaceWith($failMsg);
                    window.location.reload();
                });
            });

            // Catch redirection.
            xhr = xhr.then(function() {
                var $feedback = createFeedbackElement(dtDummy.import_msg.rid_of_redirects, $globalFeedbackContainer);

                return $.get(ajaxUrl, {noheader: true}).done(function() {
                    var $message = '<p class="the7-updated">' + dtDummy.import_msg.rid_of_redirects + '</p>';
                    $feedback.replaceWith($message);
                }).fail(function() {
                    $feedback.replaceWith($failMsg);
                    window.location.reload();
                });
            });

            return xhr;
        }

        var $dummyContentBlocks = $('.dt-dummy-content');

        // Search demo.
        $('#dt-dummy-search-input').on('search keyup', function() {
            var val = $(this).val().toLowerCase();

            if (1 == val.length) {
                return;
            }

            $dummyContentBlocks.each(function() {
                var $block = $(this);
                var content = $block.find('h3').first().text().toLowerCase();
                if ( content.includes(val) ) {
                    $block.show();
                } else {
                    $block.hide();
                }
            });
        });
	});
})( jQuery );
