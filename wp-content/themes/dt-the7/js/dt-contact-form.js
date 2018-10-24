jQuery(function($){
	
	// Init form validator
	//function dtInitContactForm () {
	$.fn.dtInitContactForm = function() {
		return this.each(function() {
			var $form = $(this),
				$term = '';
			if($form.hasClass('privacy-form')){
				$term = '<p class="dt-privacy-message">' + dtLocal.contactMessages.terms+ '<p>';
			}else{
				$term = '';
			}
			$form.validationEngine( {
				binded: false,
				promptPosition: 'inline',
				scroll: false,
				autoHidePrompt: false,
				maxErrorsPerField: 1,
				//showOneMessage: true,
				'custom_error_messages' : {
			        'required': {
			            'message': dtLocal.contactMessages.required + $term
			        },
			     },
			    fadeDuration: 500,
			    addPromptClass : "run-animation",
			    onAjaxFormComplete:function( ) {},
				addSuccessCssClassToField: "field-success",
				onBeforeAjaxFormValidation: function( form, status ) {
					var $form = $(form);
					$form.find(".formError").removeClass("first");
					$form.find('input').removeClass('error-field');
					$form.find('textarea').removeClass('error-field');
				},
				onFailure: function( form, status ) {
					var $form = $(form);
					if($form.find(".formError .close-message").length <= 0) {
						$form.find(".formError").append('<span class="close-message"></span>');
					}
				},
				onValidationComplete: function( form, status ) {
					var $form = $(form);
					if($form.find(".greenPopup").length > 0) {
						$form.find(".greenPopup").remove();
					}
					$form.find(".formError").removeClass("first");
					$form.find('input').removeClass('error-field');
					$form.find('textarea').removeClass('error-field');
					
					$form.find(".formError").each(function(i, el){
                        $(el).eq(i).addClass("first");
                        $(el).prev().addClass('error-field');
                    });
					$('.formError .close-message').remove();
					if($form.find(".formError .close-message").length <= 0) {
						$form.find(".formError").append('<span class="close-message"></span>');
					}
					if($form.find('input.the7-form-terms').hasClass("field-success")){
						$form.find(".dt-privacy-message").addClass("hide-privacy-message");
					}

					// If validation success
					if ( status ) {

						var data = {
							action : 'dt_send_mail',
							widget_id: $('input[name="widget_id"]', $form).val(),
							send_message: $('input[name="send_message"]', $form).val(),
							fields : {}
						};

						$form.find('input[type="text"], textarea').each(function(){
							var $this = $(this);

							data.fields[ $this.attr('name') ] = $this.val();
						});

						$.post(
							dtLocal.ajaxurl,
							data,
							function (response) {
								var _caller = $(form),
									msgType = response.success ? 'pass' : 'error',
									messageTimeoutDelete;
								// Show message
								$('input[type="hidden"]', _caller).last().validationEngine( 'showPrompt', response.errors, msgType, 'inline' );
								
								$form.find(".formError").addClass("field-success")
								// set promptPosition again
								_caller.validationEngine( 'showPrompt', '', '', 'topRight' );

								// Clear fields if success
								if ( response.success ) {
									_caller.find('input[type="text"], textarea').val("");
									_caller.find('input[type="checkbox"]').removeProp('checked');

									if($form.find(".formError .close-message").length <= 0) {
										$form.find(".formError").append('<span class="close-message"></span>');
										$form.find(".formError .close-message").on('click', function(){
											$form.find(".greenPopup").remove();
											clearTimeout(messageTimeoutDelete);
										})
									}

									clearTimeout(messageTimeoutDelete);
									messageTimeoutDelete = setTimeout(function (){
										$form.find(".greenPopup").remove();
									},11000)
								}
							}
						);
					}

				} // onValidationComplete
			} );

			$form.find( '.dt-btn.dt-btn-submit' ).on( 'click', function( e ) {
				e.preventDefault();

				var $form = $(this).parents( 'form' );
				$form.submit();
			} );

			$form.find( '.clear-form' ).on( 'click' ,function( e ) {
				e.preventDefault();

				var $form = $(this).parents( 'form' );

				if ( $form.length > 0 ) {
					$form.find( 'input[type="text"], textarea' ).val( "" );
					$form.validationEngine( 'hide' );
				}
			} );
		})
	}

	$( 'form.dt-contact-form.dt-form' ).dtInitContactForm();
});