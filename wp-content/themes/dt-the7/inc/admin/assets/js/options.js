(function ($) {
	if (!window.The7Options) {
		window.The7Options = {};
	}

	if (window.The7Options.slider) {
		return;
	}

	window.The7Options.slider = function (element) {
		var $sliderContainer = $(element);
		var $sliderInput = $sliderContainer.next('input.of-slider-value');

		if (!$sliderInput.length) {
			return;
		}

		$sliderContainer.slider({
			value: parseInt($sliderInput.attr('data-value')),
			min: parseInt($sliderInput.attr('data-min')),
			max: parseInt($sliderInput.attr('data-max')),
			step: parseInt($sliderInput.attr('data-step')),
			range: 'min',
			slide: function (event, ui) {
				$sliderInput.val(ui.value).trigger('change.of-slider-slide');
			}
		});
		$sliderInput.val($sliderContainer.slider('value'));
		$sliderInput.on('change', function (event) {
			var value = parseInt($sliderInput.val());
			if (isNaN(value)) {
				value = $sliderContainer.slider('option', 'min');
			}

			$sliderContainer.slider('value', value);
			$sliderInput.val($sliderContainer.slider('value'));
		});
	}
})(jQuery);
;
/**
 * Alpha Color Picker JS
 */

(function ($) {
	if (!window.The7Options) {
		window.The7Options = {};
	}

	if (window.The7Options.alphaColor) {
		return;
	}

	window.The7Options.alphaColor = function(element) {
		$(element).alphaColorPicker();
	}

	/**
	 * Override the stock color.js toString() method to add support for
	 * outputting RGBa or Hex.
	 */
	Color.prototype.toString = function (flag) {

		// If our no-alpha flag has been passed in, output RGBa value with 100% opacity.
		// This is used to set the background color on the opacity slider during color changes.
		if ('no-alpha' == flag) {
			return this.toCSS('rgba', '1').replace(/\s+/g, '');
		}

		// If we have a proper opacity value, output RGBa.
		if (1 > this._alpha) {
			return this.toCSS('rgba', this._alpha).replace(/\s+/g, '');
		}

		// Proceed with stock color.js hex output.
		var hex = parseInt(this._color, 10).toString(16);
		if (this.error) {
			return '';
		}
		if (hex.length < 6) {
			for (var i = 6 - hex.length - 1; i >= 0; i--) {
				hex = '0' + hex;
			}
		}

		return '#' + hex;
	};

	/**
	 * Given an RGBa, RGB, or hex color value, return the alpha channel value.
	 */
	function acp_get_alpha_value_from_color(value) {
		var alphaVal;

		// Remove all spaces from the passed in value to help our RGBa regex.
		value = value.replace(/ /g, '');

		if (value.match(/rgba\(\d+\,\d+\,\d+\,([^\)]+)\)/)) {
			alphaVal = parseFloat(value.match(/rgba\(\d+\,\d+\,\d+\,([^\)]+)\)/)[1]).toFixed(2) * 100;
			alphaVal = parseInt(alphaVal);
		} else {
			alphaVal = 100;
		}

		return alphaVal;
	}

	/**
	 * Force update the alpha value of the color picker object and maybe the alpha slider.
	 */
	function acp_update_alpha_value_on_color_input(alpha, $input, $alphaSlider, update_slider) {
		var iris, colorPicker, color;

		iris = $input.data('a8cIris');
		colorPicker = $input.data('wpWpColorPicker');

		// Set the alpha value on the Iris object.
		iris._color._alpha = alpha;

		// Store the new color value.
		color = iris._color.toString();

		// Set the value of the input.
		$input.val(color);

		// Update the background color of the color picker.
		colorPicker.toggler.css({
			'background-color': color
		});

		// Maybe update the alpha slider itself.
		if (update_slider) {
			acp_update_alpha_value_on_alpha_slider(alpha, $alphaSlider);
		}

		// Update the color value of the color picker object.
		$input.wpColorPicker('color', color);
	}

	/**
	 * Update the slider handle position and label.
	 */
	function acp_update_alpha_value_on_alpha_slider(alpha, $alphaSlider) {
		$alphaSlider.slider('value', alpha);
		$alphaSlider.find('.ui-slider-handle').text(alpha.toString());
	}

	$.fn.alphaColorPicker = function () {

		return this.each(function () {

			// Scope the vars.
			var $input, startingColor, paletteInput, showOpacity, defaultColor, palette,
				colorPickerOptions, $container, $alphaSlider, alphaVal, sliderOptions;

			// Store the input.
			$input = $(this);

			// We must wrap the input now in order to get our a top level class
			// around the HTML added by wpColorPicker().
			$input.wrap('<div class="alpha-color-picker-wrap"></div>');

			// Get some data off the input.
			paletteInput = $input.attr('data-palette') || 'true';
			showOpacity = $input.attr('data-show-opacity') || 'true';
			defaultColor = $input.attr('data-default-color') || '';

			// Process the palette.
			if (paletteInput.indexOf('|') !== -1) {
				palette = paletteInput.split('|');
			} else if ('false' == paletteInput) {
				palette = false;
			} else {
				palette = true;
			}

			// Get a clean starting value for the option.
			startingColor = $input.val().replace(/\s+/g, '');

			// If we don't yet have a value, use the default color.
			if ('' == startingColor) {
				startingColor = defaultColor;
			}

			// Set up the options that we'll pass to wpColorPicker().
			colorPickerOptions = {
				change: function (event, ui) {
					var key, value, alpha, $transparency;

					key = $input.attr('data-customize-setting-link');
					value = $input.wpColorPicker('color');

					// Set the opacity value on the slider handle when the default color button is clicked.
					if (defaultColor == value) {
						alpha = acp_get_alpha_value_from_color(value);
						$alphaSlider.find('.ui-slider-handle').text(alpha);
					}

					// If we're in the Customizer, send an ajax request to wp.customize
					// to trigger the Save action.
					if (typeof wp.customize != 'undefined') {
						wp.customize(key, function (obj) {
							obj.set(value);
						});
					}

					$transparency = $container.find('.transparency');

					// Always show the background color of the opacity slider at 100% opacity.
					$transparency.css('background-color', ui.color.toString('no-alpha'));
				},
				palettes: palette // Use the passed in palette.
			};

			// Create the colorpicker.
			$input.wpColorPicker(colorPickerOptions);

			$container = $input.parents('.wp-picker-container:first');

			// Insert our opacity slider.
			$('<div class="alpha-color-picker-container">' +
				'<div class="min-click-zone click-zone"></div>' +
				'<div class="max-click-zone click-zone"></div>' +
				'<div class="alpha-slider"></div>' +
				'<div class="transparency"></div>' +
				'</div>').appendTo($container.find('.wp-picker-holder'));

			$alphaSlider = $container.find('.alpha-slider');

			// If starting value is in format RGBa, grab the alpha channel.
			alphaVal = acp_get_alpha_value_from_color(startingColor);

			// Set up jQuery UI slider() options.
			sliderOptions = {
				create: function (event, ui) {
					var value = $(this).slider('value');

					// Set up initial values.
					$(this).find('.ui-slider-handle').text(value);
					$(this).siblings('.transparency ').css('background-color', startingColor);
				},
				value: alphaVal,
				range: 'max',
				step: 1,
				min: 0,
				max: 100,
				animate: 300
			};

			// Initialize jQuery UI slider with our options.
			$alphaSlider.slider(sliderOptions);

			// Maybe show the opacity on the handle.
			if ('true' == showOpacity) {
				$alphaSlider.find('.ui-slider-handle').addClass('show-opacity');
			}

			// Bind event handlers for the click zones.
			$container.find('.min-click-zone').on('click', function () {
				acp_update_alpha_value_on_color_input(0, $input, $alphaSlider, true);
			});
			$container.find('.max-click-zone').on('click', function () {
				acp_update_alpha_value_on_color_input(100, $input, $alphaSlider, true);
			});

			// Bind event handler for clicking on a palette color.
			$container.find('.iris-palette').on('click', function () {
				var color, alpha;

				color = $(this).css('background-color');
				alpha = acp_get_alpha_value_from_color(color);

				acp_update_alpha_value_on_alpha_slider(alpha, $alphaSlider);

				// Sometimes Iris doesn't set a perfect background-color on the palette,
				// for example rgba(20, 80, 100, 0.3) becomes rgba(20, 80, 100, 0.298039).
				// To compensante for this we round the opacity value on RGBa colors here
				// and save it a second time to the color picker object.
				if (alpha != 100) {
					color = color.replace(/[^,]+(?=\))/, (alpha / 100).toFixed(2));
				}

				$input.wpColorPicker('color', color);
			});

			// Bind event handler for clicking on the 'Default' button.
			$container.find('.button.wp-picker-default').on('click', function () {
				var alpha = acp_get_alpha_value_from_color(defaultColor);

				acp_update_alpha_value_on_alpha_slider(alpha, $alphaSlider);
			});

			// Bind event handler for typing or pasting into the input.
			$input.on('input', function () {
				var value = $(this).val();
				var alpha = acp_get_alpha_value_from_color(value);

				acp_update_alpha_value_on_alpha_slider(alpha, $alphaSlider);
			});

			// Update all the things when the slider is interacted with.
			$alphaSlider.slider().on('slide', function (event, ui) {
				var alpha = parseFloat(ui.value) / 100.0;

				acp_update_alpha_value_on_color_input(alpha, $input, $alphaSlider, false);

				// Change value shown on slider handle.
				$(this).find('.ui-slider-handle').text(ui.value);
			});
		});
	}
})(jQuery);
;
/**
 @author Matt Crinklaw-Vogt (tantaman)
 */
(function ($) {
	if (!window.The7Options) {
		window.The7Options = {};
	}

	if (window.The7Options.gradientPicker) {
		return;
	}

	window.The7Options.gradientPicker = function (element) {
		var $this = $(element);
		$this.gradientPicker({
			'change': function (result, $el) {
				var angle = parseInt($el.find('.of-gradient_picker-angle').val()) + "deg";
				var colorStops = result.map(function (el) {
					return el.color + ' ' + (el.position * 100 | 0) + '%';
				});
				var value = [angle].concat(colorStops);
				$el.find('.of-gradient_picker-value').val(value.join('|'));
				$el.siblings('.of-gradient_picker-preview-box').first().css('background-image', 'linear-gradient(' + value.join(',') + ')');
			},
			controlPoints: $this.find('.of-gradient_picker-value').val().split('|').slice(1),
			previewWidth: 252,
			previewHeight: 25
		});
		$this.find('.of-gradient_picker-angle').on('change.of-slider-slide', function () {
			$this.gradientPicker('update', {});
		});
	}

	if (!$.event.special.destroyed) {
		$.event.special.destroyed = {
			remove: function (o) {
				if (o.handler) {
					o.handler();
				}
			}
		}
	}

	function ctrlPtComparator(l, r) {
		return l.position - r.position;
	}

	function bind(fn, ctx) {
		if (typeof fn.bind === "function") {
			return fn.bind(ctx);
		} else {
			return function () {
				fn.apply(ctx, arguments);
			}
		}
	}

	var browserPrefix = "";
	var agent = window.navigator.userAgent;
	if (agent.indexOf('WebKit') >= 0)
		browserPrefix = "-webkit-"
	else if (agent.indexOf('Mozilla') >= 0)
		browserPrefix = "-moz-"
	else if (agent.indexOf('Microsoft') >= 0)
		browserPrefix = "-ms-"
	else
		browserPrefix = ""

	function GradientSelection($el, opts) {
		this.$el = $el;
		this.$el.css("position", "relative");
		this.opts = opts;

		var $preview = $("<canvas class='gradientPicker-preview'></canvas>");
		this.$el.append($preview);
		var canvas = $preview[0];
		canvas.width = this.opts.previewWidth;
		canvas.height = this.opts.previewHeight;
		this.g2d = canvas.getContext("2d");

		var $ctrlPtContainer = $("<div class='gradientPicker-ctrlPts'></div>");
		$ctrlPtContainer.css("width", this.opts.previewWidth);
		this.$el.append($ctrlPtContainer)
		this.$ctrlPtContainer = $ctrlPtContainer;

		this.updatePreview = bind(this.updatePreview, this);
		this.controlPoints = [];
		this.ctrlPtConfig = new ControlPtConfig(this.$el, opts);
		for (var i = 0; i < opts.controlPoints.length; ++i) {
			var ctrlPt = this.createCtrlPt(opts.controlPoints[i]);
			this.controlPoints.push(ctrlPt);
		}
		this._maybeLockCtrlPoints();

		this.docClicked = bind(this.docClicked, this);
		this.destroyed = bind(this.destroyed, this);
		$(document).bind("click", this.docClicked);
		this.$el.bind("destroyed", this.destroyed);
		this.previewClicked = bind(this.previewClicked, this);
		$preview.click(this.previewClicked);

		this.updatePreview();
	}

	GradientSelection.prototype = {
		docClicked: function () {
			this.ctrlPtConfig.hide();
		},

		createCtrlPt: function (ctrlPtSetup) {
			return new ControlPoint(this.$ctrlPtContainer, ctrlPtSetup, this.opts.orientation, this, this.ctrlPtConfig)
		},

		destroyed: function () {
			$(document).unbind("click", this.docClicked);
		},

		updateOptions: function (opts) {
			$.extend(this.opts, opts);
			this.updatePreview();
		},

		updatePreview: function () {
			var result = [];
			this.controlPoints.sort(ctrlPtComparator);
			var grad = this.g2d.createLinearGradient(0, 0, this.g2d.canvas.width, 0);
			for (var i = 0; i < this.controlPoints.length; ++i) {
				var pt = this.controlPoints[i];
				grad.addColorStop(pt.position, pt.color);
				result.push({
					position: pt.position,
					color: pt.color
				});
			}

			this.g2d.fillStyle = grad;
			this.g2d.fillRect(0, 0, this.g2d.canvas.width, this.g2d.canvas.height);

			if (this.opts.generateStyles)
				var styles = this._generatePreviewStyles();

			this.opts.change(result, this.$el, styles);
		},

		removeControlPoint: function (ctrlPt) {
			if (this.ctrlPointsIsLocked) {
				return;
			}

			var cpidx = this.controlPoints.indexOf(ctrlPt);

			if (cpidx != -1) {
				this.controlPoints.splice(cpidx, 1);
				ctrlPt.$el.remove();
			}

			this._maybeLockCtrlPoints();
		},

		previewClicked: function (e) {
			var offset = $(e.target).offset();
			var x = e.pageX - offset.left;
			var y = e.pageY - offset.top;

			var imgData = this.g2d.getImageData(x, y, 1, 1);
			var colorStr = "rgb(" + imgData.data[0] + "," + imgData.data[1] + "," + imgData.data[2] + ")";

			var cp = this.createCtrlPt({
				position: x / this.g2d.canvas.width,
				color: colorStr
			});

			this.controlPoints.push(cp);
			this.controlPoints.sort(ctrlPtComparator);
			this._maybeLockCtrlPoints();
		},

		_generatePreviewStyles: function () {
			//linear-gradient(top, rgb(217,230,163) 86%, rgb(227,249,159) 9%)
			var str = this.opts.type + "-gradient(" + ((this.opts.type == "linear") ? (this.opts.fillDirection + ", ") : "");
			var first = true;
			for (var i = 0; i < this.controlPoints.length; ++i) {
				var pt = this.controlPoints[i];
				if (!first) {
					str += ", ";
				} else {
					first = false;
				}
				str += pt.color + " " + ((pt.position * 100) | 0) + "%";
			}

			str = str + ")"
			var styles = [str, browserPrefix + str];
			return styles;
		},

		_maybeLockCtrlPoints: function () {
			if (this.controlPoints.length <= this.opts.minCtrlPts) {
				this.ctrlPtConfig.lockCtrlPoints();
				this.ctrlPointsIsLocked = true;
			} else {
				this.ctrlPtConfig.unlockCtrlPoints();
				this.ctrlPointsIsLocked = false;
			}
		}
	};

	function ControlPoint($parentEl, initialState, orientation, listener, ctrlPtConfig) {
		this.$el = $("<div class='gradientPicker-ctrlPt'></div>");
		$parentEl.append(this.$el);
		this.$parentEl = $parentEl;
		this.configView = ctrlPtConfig;

		if (typeof initialState === "string") {
			initialState = initialState.split(" ");
			this.position = parseFloat(initialState[1]) / 100;
			this.color = initialState[0];
		} else {
			this.position = initialState.position;
			this.color = initialState.color;
		}

		this.listener = listener;
		this.outerWidth = this.$el.outerWidth();

		this.$el.css("background-color", this.color);
		if (orientation == "horizontal") {
			var pxLeft = ($parentEl.width() - this.$el.outerWidth()) * (this.position);
			this.$el.css("left", pxLeft);
		} else {
			var pxTop = ($parentEl.height() - this.$el.outerHeight()) * (this.position);
			this.$el.css("top", pxTop);
		}

		this.drag = bind(this.drag, this);
		this.stop = bind(this.stop, this);
		this.clicked = bind(this.clicked, this);
		this.colorChanged = bind(this.colorChanged, this);
		this.$el.draggable({
			axis: (orientation == "horizontal") ? "x" : "y",
			drag: this.drag,
			stop: this.stop,
			containment: $parentEl
		});
		this.$el.css("position", 'absolute');
		this.$el.click(this.clicked);
	}

	ControlPoint.prototype = {
		changePosition: function (position) {
			this.position = position;
			this.listener.updatePreview();
			this.$el.css('left', this.position * (this.$parentEl.width() - this.outerWidth));
		},

		drag: function (e, ui) {
			// convert position to a %
			var left = ui.position.left;
			this.position = (left / (this.$parentEl.width() - this.outerWidth));
			this.listener.updatePreview();
			this.configView.setPosition(this.position);
		},

		stop: function (e, ui) {
			this.listener.updatePreview();
			this.configView.show(this.$el.position(), this.color, this);
		},

		clicked: function (e) {
			this.configView.show(this.$el.position(), this.color, this);
			e.stopPropagation();
			return false;
		},

		colorChanged: function (c) {
			this.color = c;
			this.$el.css("background-color", this.color);
			this.listener.updatePreview();
		},

		removeClicked: function () {
			this.listener.removeControlPoint(this);
			this.listener.updatePreview();
		}
	};

	function ControlPtConfig($parent, opts) {
		//color-chooser
		this.$el = $('<div class="gradientPicker-ptConfig" style="visibility: hidden"></div>');
		$parent.append(this.$el);
		var $cpicker = $('<input class="color-chooser"></input>');
		this.$el.append($cpicker);
		var $rmEl = $("<div class='gradientPicker-close'></div>");
		this.$el.append($rmEl);

		this.colorChanged = bind(this.colorChanged, this);
		this.removeClicked = bind(this.removeClicked, this);
		$cpicker.alphaColorPicker();
		$cpicker.wpColorPicker("option", "clear", false);
		var $wpColorPickerInstance = $cpicker.wpColorPicker("instance");
		var originChangeCallback = $wpColorPickerInstance.options.change;
		var self = this;
		$cpicker.wpColorPicker("option", "change", function (event, ui) {
			originChangeCallback(event, ui);
			self.colorChanged(event, ui);
		})
		this.$cpicker = $cpicker;
		this.opts = opts;
		this.visible = false;

		$rmEl.click(this.removeClicked);

		this.$cpicker.parents(".wp-picker-container:first").find(".wp-color-result").off("click");

		this.onChangePosition = bind(this.onChangePosition, this);
		this.position = new ControlPtPositionUI();
		this.position.onChangePosition(this.onChangePosition);
		this.$cpicker.closest(".wp-picker-input-wrap").after(this.position.getControls());
	}

	ControlPtConfig.prototype = {
		show: function (position, color, listener) {
			this.listener = listener;
			this.$el.css("visibility", "visible");
			this.$cpicker.wpColorPicker("color", color);
			this.$cpicker.trigger('input');

			if (!this.visible) {
				this.$cpicker.wpColorPicker("open");
			}

			if (this.opts.orientation === "horizontal") {
				this.$el.css("left", 0);
			} else {
				this.$el.css("top", position.top);
			}

			this.visible = true;
			this.setPosition(listener.position);
		},

		hide: function () {
			if (this.visible) {
				this.$el.css("visibility", "hidden");
				this.visible = false;
			}
		},

		colorChanged: function (event, ui) {
			var color = ui.color.toString();
			this.listener.colorChanged(color);
		},

		removeClicked: function () {
			this.listener.removeClicked();
			this.hide();
		},

		lockCtrlPoints: function () {
			this.$el.addClass("gradientPicker-ctrlPointsLocked");
		},

		unlockCtrlPoints: function () {
			this.$el.removeClass("gradientPicker-ctrlPointsLocked");
		},

		setPosition: function (position) {
			this.position.setPosition(position);
		},

		onChangePosition: function (position) {
			position = parseInt(position);
			if (isNaN(position)) {
				return;
			}
			position = window.Math.max(0, position);
			position = window.Math.min(100, position);

			this.listener.changePosition(position / 100);
		}
	};

	function ControlPtPositionUI() {
		this.$positionParent = $('<span class="wp-picker-input-wrap wp-picker-position"><label><span class="screen-reader-text">Control point position</span><input class="point-position" type="number" min="0" max="100" step="1" size="3">%</label></span>');
		this.$input = this.$positionParent.find('.point-position');
	}

	ControlPtPositionUI.prototype = {
		getControls: function () {
			return this.$positionParent;
		},

		setPosition: function (position) {
			this.$input.val(window.Math.round(position * 100));
		},

		getPosition: function () {
			return this.$input.val() | 0;
		},

		onChangePosition: function (callback) {
			this.$input.on('change', {'callback': callback}, this.changePosition);
		},

		changePosition: function (event) {
			event.data.callback($(this).val());
		}
	};

	var methods = {
		init: function (opts) {
			opts = $.extend({
				controlPoints: ["#FFF 0%", "#000 100%"],
				minCtrlPts: 2,
				orientation: "horizontal",
				type: "linear",
				fillDirection: "left",
				generateStyles: true,
				previewWidth: 200,
				previewHeight: 20,
				change: function () {
				}
			}, opts);

			this.each(function () {
				var $this = $(this);
				var gradSel = new GradientSelection($this, opts);
				$this.data("gradientPicker-sel", gradSel);
			});
		},

		update: function (opts) {
			this.each(function () {
				var $this = $(this);
				var gradSel = $this.data("gradientPicker-sel");
				if (gradSel != null) {
					gradSel.updateOptions(opts);
				}
			});
		}
	};

	$.fn.gradientPicker = function (method, opts) {
		if (typeof method === "string" && method !== "init") {
			methods[method].call(this, opts);
		} else {
			opts = method;
			methods.init.call(this, opts);
		}
	};
})(jQuery);
;
(function($) {
	$(document).ready(function() {

		window.optionsframework_add_file = optionsframework_add_file;
		window.optionsframework_remove_file = optionsframework_remove_file;
		window.optionsframework_file_bindings = optionsframework_file_bindings;

		function optionsframework_add_file(event, selector) {
			var upload = $(".uploaded-file"), frame;
			var $el = $(this);

			event.preventDefault();

			// If the media frame already exists, reopen it.
			if ( frame ) {
				frame.open();
				return;
			}

			// Create the media frame.
			frame = wp.media({
				// Set the title of the modal.
				title: $el.data('choose'),

				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: $el.data('update'),
					// Tell the button not to close the modal, since we're
					// going to refresh the page when the image is selected.
					close: false
				}
			});

			// When an image is selected, run a callback.
			frame.on( 'select', function() {

				// Grab the selected attachment.
				var attachment = frame.state().get('selection').first();

				frame.close();
				
				selector.find('.upload').val(attachment.attributes.url);
				selector.find('.upload-id').val(attachment.attributes.id);
				if ( attachment.attributes.type == 'image' ) {
					selector.find('.screenshot').empty().hide().append('<img src="' + attachment.attributes.url + '"><a class="remove-image">Remove</a>').slideDown('fast');
				}
				selector.find('.upload-button').unbind().addClass('remove-file').removeClass('upload-button').val(optionsframework_l10n.remove);
				selector.find('.of-background-properties').slideDown();
				optionsframework_file_bindings(selector);
			});

			// Finally, open the modal.
			frame.open();
		}
        
		function optionsframework_remove_file(selector) {
			selector.find('.upload').val('');
			selector.find('.of-background-properties').hide();
			selector.find('.screenshot').slideUp();
			selector.find('.remove-image').unbind();
			selector.find('.remove-file').unbind().addClass('upload-button').removeClass('remove-file').val(optionsframework_l10n.upload);
			selector.find('.upload-id').val(0);
			// We don't display the upload button if .upload-notice is present
			// This means the user doesn't have the WordPress 3.5 Media Library Support
			if ( $('.section-upload .upload-notice').length > 0 ) {
				$('.upload-button').remove();
			}
			
			optionsframework_file_bindings(selector);
		}
		
		function optionsframework_file_bindings() {

			var selector = arguments[0] || $('#optionsframework');

			$('.remove-image, .remove-file', selector).on('click', function() {
				optionsframework_remove_file( $(this).parents('.controls') );
	        });
	        
	        $('.upload-button', selector).click( function( event ) {
	        	optionsframework_add_file(event, $(this).parents('.controls'));
	        });
        }
        
        optionsframework_file_bindings();
    });
	
})(jQuery);
;
(function ($) {

	if (!window.The7Options) {
		window.The7Options = {};
	}

	if (window.The7Options.codeEditor) {
		return;
	}

	window.The7Options.codeEditor = function (element) {
		if (!wp.codeEditor || !wp.codeEditor.defaultSettings) {
			return null;
		}

		var $self = $(element);
		var editorSettings = $.extend({}, wp.codeEditor.defaultSettings);
		editorSettings.codemirror = $.extend(
			{},
			editorSettings.codemirror,
			{
				indentUnit: 2,
				tabSize: 2,
				mode: $self.attr('data-code-style')
			}
		);

		return wp.codeEditor.initialize($self, editorSettings);
	}
})(jQuery);
;
/*
 * Viewport - jQuery selectors for finding elements in viewport
 *
 * Copyright (c) 2008-2009 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *  http://www.appelsiini.net/projects/viewport
 *
 */
(function($) {

    $.belowthefold = function(element, settings) {
        var fold = $(window).height() + $(window).scrollTop();
        return fold <= $(element).offset().top - settings.threshold;
    };

    $.abovethetop = function(element, settings) {
        var top = $(window).scrollTop();
        return top >= $(element).offset().top + $(element).height() - settings.threshold;
    };

    $.rightofscreen = function(element, settings) {
        var fold = $(window).width() + $(window).scrollLeft();
        return fold <= $(element).offset().left - settings.threshold;
    };

    $.leftofscreen = function(element, settings) {
        var left = $(window).scrollLeft();
        return left >= $(element).offset().left + $(element).width() - settings.threshold;
    };

    $.inviewport = function(element, settings) {
        return !$.rightofscreen(element, settings) && !$.leftofscreen(element, settings) && !$.belowthefold(element, settings) && !$.abovethetop(element, settings);
    };

    $.extend($.expr[':'], {
        "below-the-fold": function(a, i, m) {
            return $.belowthefold(a, {threshold : 0});
        },
        "above-the-top": function(a, i, m) {
            return $.abovethetop(a, {threshold : 0});
        },
        "left-of-screen": function(a, i, m) {
            return $.leftofscreen(a, {threshold : 0});
        },
        "right-of-screen": function(a, i, m) {
            return $.rightofscreen(a, {threshold : 0});
        },
        "in-viewport": function(a, i, m) {
            return $.inviewport(a, {threshold : 0});
        }
    });


})(jQuery);

 /**
 * Prints out the inline javascript needed for the colorpicker and choosing
 * the tabs in the panel.
 */

jQuery(document).ready(function($) {
	// dependenies
	var dependencyAPI = {
		fields: [],
		dependencies: {},
		_depFields: [],
		init: function (fields, dependencies) {
			if ( ! dependencies || ! fields ) {
				return;
			}

			this.fields = fields;
			this.dependencies = dependencies;

			this._setDepFields();
			this._triggerDependencies();
			this._addEvents();
		},
		_addEvents: function() {
			var self = this;

			// radio, select
			this.fields.find(':radio, select').on('change.of.dependency', function() {
				var $this = $(this);
				var fieldID = self._getFieldID($this);

				self._setFieldValue(fieldID, $this.val());
				self._triggerDependencies(fieldID);
			});

            this.fields.find(':checkbox').on('change.of.dependency', function() {
                var $this = $(this);
                var fieldID = self._getFieldID($this);
				var value = '0';

                if ($this.is(':checked')) {
                    value = '1';
                }

                self._setFieldValue(fieldID, value);
                self._triggerDependencies(fieldID);
            });
		},
		_fieldIsVisible: function(dependency) {
			var self = this;
			// and
			return dependency.reduce(function(prev, cur) {
				// or
				return prev || cur.reduce(function(prev, cur) {
					var val = self._getFieldValue(cur.field);

					if ( '==' === cur.operator ) {
						return prev && val === cur.value;
					} else if ( '!=' === cur.operator ) {
						return prev && val !== cur.value;
					} else if ( 'IN' === cur.operator ) {
						return cur.value.indexOf(val) !== -1;
					} else if ( 'NOT_IN' === cur.operator ) {
						return cur.value.indexOf(val) === -1;
					} else if ( typeof cur.bool_value !== 'undefined' ) {
						return prev && !!cur.bool_value;
					}

					return prev;
				}, true);
			}, false);
		},
		_triggerDependencies: function(fieldID) {
			if ( typeof fieldID !== 'undefined' ) {
				if ( ! this._depFields[ fieldID ] ) {
					return;
				}

				// if fieldID is set then fill dependencies only with child fields deps
				var dependencies = [];
				var depLen = this._depFields[ fieldID ].length;
				for ( var i = 0; i < depLen; i++ ) {
					var depFieldID = this._depFields[ fieldID ][ i ];
					if ( this.dependencies[ depFieldID ] ) {
						dependencies[ depFieldID ] = this.dependencies[ depFieldID ];
					}
				}
			} else {
				var dependencies = this.dependencies;
			}

			for ( depField in dependencies ) {
				if ( this._fieldIsVisible(dependencies[ depField ]) ) {
					this._showField(depField);
                    this._showBox(depField);
				} else {
					this._hideField(depField);
                    this._hideBox(depField);
				}
			}

		},
		_setDepFields: function() {
			// setup additional dependencies list
			// it contains parent field id as property name and array of child fields ids as property value
			for ( var fieldName in this.dependencies ) {
				var orDep = this.dependencies[ fieldName ];
				var orLen = orDep.length;

				// or dependecy
				for ( var i = 0; i < orLen; i++ ) {
					var andDep = orDep[ i ];
					var andLen = andDep.length;

					// and dependency
					for ( var j = 0; j < andLen; j++ ) {
						var dep = andDep[ j ];
						var depField = dep.field;

						if ( ! this._depFields[ depField ] ) {
							this._depFields[ depField ] = new Array();
						}
						this._depFields[ depField ].push( fieldName );
					}
				}
			}
		},
		_getFieldID: function(item) {
			return item.closest('.section').attr('id').replace('section-', '');
		},
		_showField: function(fieldID) {
			this._findField(fieldID).show();
		},
		_hideField: function(fieldID) {
			this._findField(fieldID).hide();
		},
		_findField: function(fieldID) {
			return this.fields.find('#section-'+fieldID);
		},
		_showBox: function(fieldID) {
            this._findBox(fieldID).removeClass('block-disabled');
		},
		_hideBox: function(fieldID) {
            this._findBox(fieldID).addClass('block-disabled');
		},
		_findBox: function(fieldID) {
            return this.fields.filter('#'+fieldID+'.postbox');
        },
		_getFieldValue: function(fieldID) {
			return this._findField(fieldID).attr('data-value');
		},
		_setFieldValue: function(fieldID, value) {
			if ( typeof value !== 'undefined' ) {
                this._findField(fieldID).attr('data-value', value);
			}
        }
	};

	dependencyAPI.init( $('#optionsframework .section'), optionsframework.dependencies );

	// Blocks and tabs dependencies
	var blockDependency = $.extend(true, {}, dependencyAPI, {
		_showField: function(fieldID) {
			$('#optionsframework-wrap .'+fieldID).removeClass('block-disabled');
		},
		_hideField: function(fieldID) {
			$('#optionsframework-wrap .'+fieldID).addClass('block-disabled');
		}
	});

	blockDependency.init( $('#optionsframework .section'), optionsframework.blockDependencies );

	// Fade out the save message
	$('.fade').delay(5000).fadeOut(1000);

	$('.of-hex-color').wpColorPicker();
    $('.of-rgba-color').each(function() {
    	window.The7Options.alphaColor(this);
    });

	// Switches option sections
	$('.nav-tab-wrapper').show();
	$('.group').hide();

	$('.group .collapsed').each(function(){
		$(this).find('input:checked').parent().parent().parent().nextAll().each( 
			function(){
				if ($(this).hasClass('last')) {
					$(this).removeClass('hidden');
						return false;
					}
				$(this).filter('.hidden').removeClass('hidden');
			});
	});

    /**
	 * Option tabs.
     */

    /**
	 * Update _wp_http_referer value so the same tab will be opened after options save.
	 *
     * @param val
     */
    function updateWPHttpReferer(val) {
    	var $link = $('<a></a>').attr('href', val);
        $('#optionsframework input[name="_wp_http_referer"]').val($link[0].pathname + $link[0].search);
	}

    /**
	 * Convert tab id to group id.
	 *
     * @param tabID
     */
	function convertTab2Group(tabID) {
    	return tabID.replace('-tab', '-group');
	}

    /**
	 * Shop options tab.
	 *
     * @param tabID
     */
	function showOptionsTab(tabID) {
        var groupID = convertTab2Group(tabID);

        $('.group').hide().filter('#' + groupID).fadeIn();
        $('.nav-tab-wrapper a').removeClass('nav-tab-active').filter('#' + tabID).addClass('nav-tab-active').blur();
        $('body').trigger('the7.options.tabChange');
	}

	$('.nav-tab-wrapper a').click(function(evt) {
        evt.preventDefault();

        var $this = $(this);

        showOptionsTab($this.attr('id'));

		// Editor Height (needs improvement)
		$('.wp-editor-wrap').each(function() {
			var editor_iframe = $(this).find('iframe');
			if ( editor_iframe.height() < 30 ) {
				editor_iframe.css({'height':'auto'});
			}
		});

        history.pushState({activeGroupId: '#' + convertTab2Group($this.attr('id'))}, '', $this.attr('href'));
        // Open the same tab after options save.
        updateWPHttpReferer($this.attr('href'));
		setFlow();
	});

	(function() {
        var activeGroupId = convertTab2Group($('a.nav-tab-active').attr('id'));
        $('#' + activeGroupId).fadeIn();
	})();

	$(window).on('popstate', function(event) {
        var queryDict = {};
        location.search.substr(1).split('&').forEach(function(item) {queryDict[item.split('=')[0]] = item.split('=')[1]})

		if(queryDict['tab']) {
            var tabId = queryDict['tab'];
		} else {
        	// Very first tab in history.
            var tabId = $('.nav-tab-wrapper a').first().attr('id');
		}

        showOptionsTab(tabId);
        updateWPHttpReferer($('#' + tabId).attr('href'));
	});

	// Options search.
	var optionsSearch = $('#optionsframework-search').autocomplete({
        minLength: 3,
        delay: 500,
		select: function(event, ui) {
            if (ui.item.value !== 'none') {
            	window.location.href = ui.item.value;
			}

			return false;
		},
		change: function(event, ui) {
        	return false;
		},
        focus: function() {
        	return false;
		},
        source: function(request, response) {
			var $spinner = $('#optionsframework-search-spinner').addClass('is-active');
            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'optionsframework_search',
                    search: request.term,
					_ajax_nonce: this.element.attr('data-nonce')
                }
            })
				.done(function(data) {
					response(data);
				})
				.fail(function() {
                    response([]);
                })
				.always(function() {
                	$spinner.removeClass('is-active');
				});
        },
	}).autocomplete('instance');

    optionsSearch._renderItem = function(ul, item) {
    	var itemTpl = '<span>'+item.label+'</span>';
    	if (item.value !== 'none') {
            itemTpl = '<a href="'+item.value+'">'+item.label+'</a>';
		}

        return $("<li>")
            .append(itemTpl)
            .appendTo(ul);
    }
    optionsSearch._renderMenu = function( ul, items ) {
        var that = this;
        $.each( items, function( index, item ) {
            that._renderItemData( ul, item );
        });
        $( ul ).addClass('the7-options-search-ui-menu');
    }

    // Marked option.
	$('.section.marked').each(function() {
		var $this = $(this);
		setTimeout(function() {
            $this.removeClass('marked');
		}, 3000);
	});

	$('.group .collapsed input:checkbox').click(unhideHidden);

	function unhideHidden(){
		if ($(this).attr('checked')) {
			$(this).parent().parent().parent().nextAll().removeClass('hidden');
		}
		else {
			$(this).parent().parent().parent().nextAll().each( 
			function(){
				if ($(this).filter('.last').length) {
					$(this).addClass('hidden');
					return false;		
					}
				$(this).addClass('hidden');
			});
							
		}
	}

	// Hide empty blocks.
	$('.the7-postbox.section').each(function() {
		var $this = $(this);
		if ( $this.find('.section').length <= 0 ) {
			$this.hide();
		}
	});

	// Spacing param.
	var DTSpacingParam = function( valueField, spacingFields ) {
		this.valueField = valueField;
		this.spacing = [];

		this.addHandlers( spacingFields );
	};

	DTSpacingParam.prototype.addSpace = function( index, val, units ) {
		this.spacing[ index ] = {
			units: units,
			val: val,
			getVal: function() {
				this.val = this.val || 0;
				return parseInt(this.val)+this.units;
			}
		};
	};

	DTSpacingParam.prototype.addHandlers = function( spacingFields ) {
		var self = this;
		spacingFields.each(function(index) {
			var $this = $(this);
			var $valueField = $this.find('.dt_spacing-value');
			var $unitsField = $this.find('.dt_spacing-units');

			self.addSpace( index, $valueField.val(), $unitsField.attr('data-units') );

			$valueField.on('blur', function() {
				self.spacing[ index ].val = $(this).val();
				self.updateParamValue();
			});

			$unitsField.on('change', function() {
				self.spacing[ index ].units = $(this).val();
				self.updateParamValue();
			});
		});
	};

	DTSpacingParam.prototype.updateParamValue = function() {
		var val = [];
		this.spacing.forEach(function(_val) {
			val.push(_val.getVal());
		});
		this.valueField.val(val.join(' '));
	};

	$('.section-spacing').each(function(){
		var $this = $(this);
		var $valueField = $this.find('.the7-option-field-value');
		var $spacingFields = $this.find('.dt_spacing-space');

		new DTSpacingParam( $valueField, $spacingFields );
	});

	$('.section-gradient_picker .grad_ex').each(function() {
		window.The7Options.gradientPicker(this);
	});

	// Image Options
	$('.of-radio-img-img').click(function(){
		$(this).parents('.controls').find('.of-radio-img-img').removeClass('of-radio-img-selected');
		$(this).addClass('of-radio-img-selected');		
	});

	// Clear selection if image removed
	$('.section-background_img').on('click', 'a.remove-image, input.button', function(e) {
		e.preventDefault();
		$(this).parents('.controls').find('.of-radio-img-img').removeClass('of-radio-img-selected');
	});

	// radio image label onclick event handler
	$('.of-radio-img-label').on('click', function(e) {
		e.preventDefault();
		$(this).siblings('.of-radio-img-img').trigger('click');
	});

	$('.of-radio-img-img').show();
	$('.of-radio-img-radio').hide();
	
	/* Web fonts
	 * Php source located in options-interface.php 'web_fonts'
	 */

	function dt_is_font_web_safe( font ) {
		var safeFonts = [
				'Andale Mono',
				'Arial',
				'Arial:600',
				'Arial:400italic',
				'Arial:600italic',
				'Arial Black',
				'Comic Sans MS',
				'Comic Sans MS:600',
				'Courier New',
				'Courier New:600',
				'Courier New:400italic',
				'Courier New:600italic',
				'Georgia',
				'Georgia:600',
				'Georgia:400italic',
				'Georgia:600italic',
				'Impact Lucida Console',
				'Lucida Sans Unicode',
				'Marlett',
				'Minion Web',
				'Symbol',
				'Times New Roman',
				'Times New Roman:600',
				'Times New Roman:400italic',
				'Times New Roman:600italic',
				'Tahoma',
				'Trebuchet MS',
				'Trebuchet MS:600',
				'Trebuchet MS:400italic',
				'Trebuchet MS:600italic',
				'Verdana',
				'Verdana:600',
				'Verdana:400italic',
				'Verdana:600italic',
				'Webdings'
			];

		if ( -1 == safeFonts.indexOf( font ) ) {
			return false;
		}

		return true;
	}

	// Preview
	if ( $( ".of-input.dt-web-fonts" ).length > 0 ) {

		var dtPrevFont = 'Arial';

		$( ".of-input.dt-web-fonts" ).on("click", function() {
			dtPrevFont = $(this).val().split(':').reduce(function(cur) { return cur; });
		});

		$( ".of-input.dt-web-fonts" ).on( "change", function() {
			var _this = $( this );
			var value = _this.val();

			if ( ! value ) {
				return;
			}

			var font_header = value.replace( / /g, "+" );
			var font_style = value.split( "&" )[0];
			var _preview = _this.siblings('.dt-web-fonts-preview').first().find('span').first();
			var italic = bold = '';

			font_style = font_style.split( ":" );

			if ( font_style[1] ) {

				var vars = font_style[1].split( 'italic' );

				if ( 2 == vars.length ) { italic = "font-style: italic;"; }

				if ( '700' == vars[0] || 'bold' == vars[0] ) {

					bold = "font-weight: bold;";
				} else if ( '400' == vars[0] || 'normal' == vars[0] ) {

					bold = "font-weight: normal;";
				} else if ( vars[0] ) {

					bold = "font-weight: " + parseInt( vars[0] ) + ";";
				} else {

					bold = "font-weight: normal;";
				}

			}else {

				bold = "font-weight: normal;";
			}

			var protocol = 'http:';
			if ( typeof document.location.protocol != 'undefined' ) {
				protocol = document.location.protocol;
			}

			var linkHref = protocol + '//fonts.googleapis.com/css?family=' + font_header;
			var linkStyle = 'font-family: "' + font_style[0] + '", "' + dtPrevFont + '";' + italic + bold;

			dtPrevFont = font_style[0];

			if ( !dt_is_font_web_safe( value ) ) {
				$('head').append('<link href="' + linkHref + '" rel="stylesheet" type="text/css">');
			}

			_preview.attr('style', linkStyle);
		} );
		$( ".of-input.dt-web-fonts" ).trigger( 'change' );
	}
	/* End Web fonts */
	
	// of_fields_generator script
	
	if ( jQuery('#optionsframework .of_fields_gen_list').length > 0 ) {
		jQuery('#optionsframework .of_fields_gen_list').sortable();
	}

	// add button
	jQuery('button.of_fields_gen_add').click(function(e) {
		e.preventDefault();

		var container = jQuery(this).parent().prev('.of_fields_gen_list'),
			layout = jQuery(this).parents('div.of_fields_gen_controls'),
			del_link = '<div class="submitbox"><a href="#" class="of_fields_gen_del submitdelete">Delete</a></div>';

		if ( !layout.find('.of_fields_gen_title').val() ) return false;
		
		var size = 0;
		container.find('div.of_fields_gen_title').each( function(){
			var index = parseInt(jQuery(this).attr('data-index'));
			if( index >= size )
				size = index;
		});
		size += 1;

		var new_block = layout.clone();
		new_block.find('button.of_fields_gen_add').detach();
		new_block
			.attr('class', '')
			.addClass('of_fields_gen_data menu-item-settings description')
			.append(del_link);
		
		var title = jQuery('<span class="dt-menu-item-title">').text( jQuery('.of_fields_gen_title', layout).val() );
		var div_title = jQuery('<div class="of_fields_gen_title menu-item-handle" data-index="' + size + '"><span class="item-controls"><a class="item-edit"></a></span></div>');
		
		new_block.find('input, textarea, select').each(function(){
			var name = jQuery(this).attr('name').toString();
			
			// this line must be awful, simple horror
			jQuery(this).val(layout.find('input[name="'+name+'"], textarea[name="'+name+'"], select[name="'+name+'"]').val());
			
			name = name.replace("][", "]["+ size +"][");
			jQuery(this).attr('name', name);
			
			var hidden_desc = jQuery(this).next('.dt-hidden-desc');

			if( 'checkbox' == jQuery(this).attr('type') && jQuery(this).attr('checked') && hidden_desc ) {
				div_title.prepend( hidden_desc.clone().removeClass('dt-hidden-desc') );
			}
		});
		container.append(new_block);
		
		div_title.prepend(title);
		
		new_block
			.wrap('<li class="nav-menus-php"></li>')
			.before(div_title);
		
		new_block.hide();
		del_button();
		checkbox_check();
		
		jQuery('.item-edit', div_title).click(function(event) {
			if( jQuery(event.target).parents('.of_fields_gen_title').is('div.of_fields_gen_title') ) {
				jQuery(event.target).parents('.of_fields_gen_title').next('div.of_fields_gen_data').toggle();
			}
		});
		
	});
	
	function del_button() {
		jQuery('.of_fields_gen_del').click(function() {
			var title_container = jQuery(this).parents('li').find('div.of_fields_gen_title');
			title_container.next('div.of_fields_gen_data').hide().detach();
			title_container.hide('slow').detach();
			return false;
		});
	}
	del_button();
		
	function toggle_button() { 
		jQuery('.item-edit').click(function(event) {
			if( jQuery(event.target).parents('.of_fields_gen_title').is('div.of_fields_gen_title') ) {
				jQuery(event.target).parents('.of_fields_gen_title').next('div.of_fields_gen_data').toggle();
			}
		});
	}
	toggle_button();
	
	function checkbox_check() {
		jQuery('.of_fields_gen_data input[type="checkbox"]').on('change', function() {
			var this_ob = jQuery(this);
			var hidden_desc = this_ob.next('.dt-hidden-desc');
			if( !hidden_desc.length ) return true;
			hidden_desc = hidden_desc.clone().removeClass('dt-hidden-desc');
			
			var div_title = jQuery(event.target)
				.parents('div.of_fields_gen_data')
				.prev('div.of_fields_gen_title')
				.children('.dt-menu-item-title');
			
			if( this_ob.attr('checked') ) {
				div_title.after( hidden_desc );
			}else {
				div_title.parent().find('.' + hidden_desc.attr('class')).remove();
			}
			
		});
	}
	checkbox_check();

	// on load indication
	jQuery('.section-fields_generator .nav-menus-php').each( function() {
		var title = jQuery('.dt-menu-item-title', jQuery(this));
		
		jQuery('input[type="checkbox"]:checked', jQuery(this)).each( function() {
			var hidden_desc = jQuery(this).next('.dt-hidden-desc');
			if( hidden_desc.length ) {
				var new_desc = hidden_desc.clone();
				title.after( new_desc.removeClass('dt-hidden-desc') );
			}
		});
	});

	jQuery('div.controls').change(function(event) {
		if( jQuery(event.target).not('div').is('.of_fields_gen_title') ) {
			var title = jQuery(event.target)
				.parents('div.of_fields_gen_data')
				.prev('div.of_fields_gen_title')
				.children('.dt-menu-item-title');
				
			if( title ) {
				title.text( jQuery(event.target).val() );
			}
		}
	});
	// of_fields_generator end

	/*
	 * slider
	 */
	jQuery('.of-slider').each(function () {
		window.The7Options.slider(this);
	});

	// js_hide
	jQuery('#optionsframework .of-js-hider').each(function() {
		var element = jQuery(this),
			target = element.closest('.section').nextAll('.of-js-hide').first(),
			hideThis = jQuery( '.' + element.closest('.section').attr('id').replace('section-', '') );

		/* If checkbox */
		if ( element.is('input[type="checkbox"]') ) {
			element.on('click', function(){
				target.fadeToggle(400);
			});

			if(element.prop('checked')) {
				target.show();
			}
		/* If slider */
		} else if ( element.hasClass('of-slider') ) {
			if(element.hasClass('js-hide-if-not-max')){
				element.on('slidechange', function(e, ui){
					var $this = jQuery(this);

					if(ui.value != $this.slider('option', 'max')) {
						target.show();
					} else {
						target.hide(400);
					}
				});
				if(element.slider('option', 'value') != element.slider('option', 'max')) {
					target.show();
				}
			}
		/* If radio */
		} else if ( element.is('input[type="radio"]') ) {

			if ( element.attr('data-js-target') ) {
				target = element.attr('data-js-target');

				if ( '.' !== target.charAt(0) ) {
					target = '.'+target;
				}

				target = jQuery( target );
			}

			if ( target.length > 0 ) {
				element.on('click', function(){

					if ( hideThis.length > 0 ) {
						hideThis.hide();
					}

					if ( $(this).hasClass('js-hider-show') ) {
						target.show();
					} else {
						target.hide();
					}
				});

				if(element.prop('checked')) {
					element.click();
				}
			}
		}
		
	});
	
	// js_hide_global
	jQuery('#optionsframework input[type="checkbox"].of-js-hider-global').click(function() {
		var element = jQuery(this);
		element.parents('.section-block_begin').next('.of-js-hide').fadeToggle(400);
	});
	
	jQuery('#optionsframework input[type="checkbox"]:checked.of-js-hider-global').each(function(){
		var element = jQuery(this);
		element.parents('.section-block_begin').next('.of-js-hide').show();
	});

	// Share buttons
	jQuery( "#optionsframework .section-social_buttons" ).each(function() {
		var $this = $(this);
		var id = $this.attr('id');

        $this.find('.connectedSortable').sortable({
            connectWith: "#" + id + " .connectedSortable",
            placeholder: "of-socbuttons-highlight",
            cancel: "li.ui-dt-sb-hidden"
        }).disableSelection();
    });

	jQuery('#optionsframework .section-social_buttons .content-holder.connectedSortable').on('sortupdate', function(e, ui) {
		var $input = ui.item.find('input[type="hidden"]'),
			$this = jQuery(this);

		$input.attr('name', $input.attr('data-name'));
	});

	jQuery('#optionsframework .section-social_buttons .tools-palette.connectedSortable').on('sortupdate', function( e, ui) {
		var $input = ui.item.find('input[type="hidden"]');
		$input.removeAttr('name');
	});

	// *********************************************************************************************************************
	// sortable start
	// *********************************************************************************************************************

	jQuery( "#optionsframework .section-sortable .connectedSortable" ).sortable({
	  connectWith: ".connectedSortable",
	  placeholder: "of-socbuttons-highlight",
	  cancel: "li.ui-dt-sb-hidden"
	}).disableSelection();

	jQuery('#optionsframework .section-sortable .content-holder.connectedSortable').on('sortupdate', function(e, ui) {
		var $input = ui.item.find('input[type="hidden"]'),
			$this = jQuery(this);

		$input.attr('name', $this.attr('data-sortable-item-name'));
	});

	jQuery('#optionsframework .section-sortable .tools-palette.connectedSortable').on('sortupdate', function( e, ui) {
		var $input = ui.item.find('input[type="hidden"]');
		$input.removeAttr('name');
	});

	// *********************************************************************************************************************
	// sortable end
	// *********************************************************************************************************************

	var microwidgetSettingsPopup = {
		_vars: {
			tb_icon_bar_open: false,
			tb_unload_binded: false,
			origin_tb_position: null,
			microwidgetSettings: null,
			microwidgetSettingsContainer: null
		},
		_addEvents: function() {
			var _self = this;

			$('.section-sortable .sortConfigIcon').on('click', function(event) {
				event.preventDefault();

				var microwidgetID = $(this).siblings('input').first().attr('value');
				var microwidgetSettings = _self._vars.microwidgetSettings = $('#microwidgets-'+microwidgetID+'-block');

				_self._vars.microwidgetSettingsContainer = microwidgetSettings.parent();
				_self._vars.tb_icon_bar_open = true;

				var popupId = 'presscore-microwidgets-settings';
				var $popupContaner = $('#'+popupId);

				// Fill popup.
				if ($popupContaner.length <= 0) {
					$('body').append('<div id="'+popupId+'" style="display: none;"><div id="optionsframework" class="presscore-modal-content"><div id="microwidget-settings-fields"></div></div></div>');
					$popupContaner = $('#'+popupId);
				}

				if (microwidgetSettings.length > 0) {
					$('#microwidget-settings-fields', $popupContaner).append(microwidgetSettings);
				}

				// Open popup.
				tb_show(microwidgetSettings.find('> h3').hide().text(), '#TB_inline?width=1024&height=768&inlineId='+popupId);
			});
		},
		_extendTBPosition: function() {
			var _self = this;

			_self._vars.origin_tb_position = tb_position;

			tb_position = function() {
				if ( ! _self._vars.tb_icon_bar_open ) {
					_self._vars.origin_tb_position();
				} else {
					var $tbWindow = $('#TB_window'),
						maxW = $(window).width(),
						top = 20,
						W = (840 > maxW ? (maxW - 10) : 840);

					var calculateHeight = function(top) {
						return $(window).height() - (top * 2);
					}

					var H = calculateHeight(top);

					if ( ! $tbWindow.hasClass('presscore-microwidget-modal') ) {
						$tbWindow.addClass('presscore-microwidget-modal');
					}

					if ( ! _self._vars.tb_unload_binded ) {
						_self._vars.tb_unload_binded = true;
						$tbWindow.bind('tb_unload', function () {
							_self._vars.tb_icon_bar_open = false;
							_self._vars.tb_unload_binded = false;

							if (_self._vars.microwidgetSettingsContainer.length > 0) {
								_self._vars.microwidgetSettingsContainer.append(_self._vars.microwidgetSettings);
							}

							$(window).off('resize.presscoreMicrowidgetsTB');
							$('.submit-wrap .button-primary', $tbWindow).off('click.presscoreMicrowidgetsTB');
						});

						$(window).on('resize.presscoreMicrowidgetsTB', function() {
							var H = calculateHeight(top);
							$tbWindow.height(H);
							$('.presscore-modal-content', $tbWindow).height(H - titleHeight - bottomBarHeight );
						});

						$tbWindow.append('<div class="submit-wrap"><div class="optionsframework-submit"><a href="#closeTB" class="button-primary">Change and close</a><div class="clear"></div></div></div>');

						$('.submit-wrap .button-primary', $tbWindow).on('click.presscoreMicrowidgetsTB', function(event) {
							tb_remove();
							event.preventDefault();
						});
					}

					var titleHeight = $('#TB_title', $tbWindow).height();
					var bottomBarHeight = $('.submit-wrap', $tbWindow).height();

					if ( $tbWindow.size() ) {
						$tbWindow.width(W).height(H);
						$('#TB_ajaxContent', $tbWindow).removeAttr('style');
						$tbWindow.css({'left': parseInt(( (maxW - W) / 2 ), 10) + 'px'});
						if ( typeof document.body.style.maxWidth !== 'undefined' ) {
							$tbWindow.css({'top': top + 'px', 'margin-top': '0'});
						}

						$('.presscore-modal-content', $tbWindow).height(H - titleHeight - bottomBarHeight );
					}
				}
			}

		},
		init: function() {
			this._extendTBPosition();
			this._addEvents();
		}
	};
	microwidgetSettingsPopup.init();

	// headers layout
	jQuery('#optionsframework #section-header-layout .controls input.of-radio-img-radio').on('click', function(e) {
		var $this = jQuery(this),
			$target = $this.parents('.section-block_begin');
		
		// hide
		$target.find('.of-js-hide.header-layout').hide();
		
		// show
		if ( $this.prop('checked') ) {
			$target.find('.of-js-hide.header-layout-'+$this.val()).show();
		}
	});
	jQuery('#optionsframework #section-header-layout .controls input:checked.of-radio-img-radio').trigger('click');

	// "Menu icon only" layout fix.
	// Hide unused options.
    (function() {
        var $paddings = $('.header-overlay-elements-top_line-padding');
        var $elementsList = $('.header-overlay-elements-side_top_line');
        var $elementsListTitle = $elementsList.parent().siblings('.sortable-field-title').first();
        var $elementsPalette = $elementsList.parents('.controls').first().find('.tools-palette');
        var $overlayLayoutInput = $('#section-header-overlay-layout input.of-radio-img-radio');

        $overlayLayoutInput.on('click', function() {
            if ( 'top_line' == $(this).val() ) {
				$paddings.slideDown(600);
				$elementsList.slideDown(600);
				$elementsListTitle.slideDown(600);
			} else {
				$paddings.slideUp(600);
				$elementsList.slideUp(600);
				$elementsListTitle.slideUp(600);
				$elementsList.find('li').appendTo($elementsPalette).find('input').attr('name', '');
			}
        });
        $overlayLayoutInput.filter('input:checked').trigger('click');
    })();

	var fontFields = $('.dt-web-fonts');
	fontFields.select2();

	var ajaxedFonts = {
		_XHR: null,
		_cache: {
			all: '',
			safe: '',
			web: ''
		},
		_items: null,
		init: function(items) {
			this._items = items;

			this._addEvents();
		},
		_addEvents: function() {
			var self = this;

			self._items.select2().on('select2:opening', function(e) {
				var $this = $(this);
				var val = $this.val();
				var fontsGroup = $this.data('fontsGroup');

				if ( self._XHR ) {
					return;
				} else if ( $this.data('fontsDone') ) {
					return;
				} else if ( self._cache[ fontsGroup ] ) {
					$this.html(self._cache[ fontsGroup ]).data('fontsDone', true).val(val).trigger('change');
					return;
				}

				self._showSpinner($this, true);

				self._XHR = $.ajax({
					method: 'POST',
					url: ajaxurl,
					data: {
						action: 'of_get_fonts',
						fontsGroup: fontsGroup,
						_wpnonce: optionsframework.ajaxFontsNonce
					}
				}).done(function(response) {
					self._XHR = null;
					if ( ! response.success ) {
						return;
					}
					self._cache[ fontsGroup ] = response.data ? response.data : '';
					$this.html(self._cache[ fontsGroup ]).data('fontsDone', true).val(val).trigger('change');
					$this.select2('open');
					self._showSpinner($this, false);
				});

				e.preventDefault();
			});
		},
		_showSpinner: function($item, show) {
			if ( ! $item.data('select2Arrow') ) {
				$item.data('select2Arrow', $item.siblings('.select2').first().find('.select2-selection__arrow').first());
			}

			var $arrow = $item.data('select2Arrow');

			if ( show ) {
				$arrow.addClass('spinner is-active');
			} else {
				$arrow.removeClass('spinner is-active');
			}
		}
	};
	ajaxedFonts.init(fontFields.filter('[data-fonts-group]'));

    var $wrap = $("#optionsframework"),
        $metabox = $('#optionsframework-metabox'),
        $controls = $("#submit-wrap"),
        $footer = $("#wpfooter"),
        $wrapCustom = $("#optionsframework-wrap");

    function setSize() {
        $controls.css({
            "width" : $wrap.width()
        });
    };

    function setFlow() {
        var wrapBottom = $wrap.offset().top + $wrap.outerHeight(),
            viewportBottom = $(window).scrollTop() + $(window).height();

        if (viewportBottom <= wrapBottom) {
            $controls.addClass("flow");
        }
        else {
            $controls.removeClass("flow");
        };
    };

    function setFlowCustom() {

        var content = $wrapCustom[0].scrollHeight,
            viewport = $wrapCustom.height();

        if (content > viewport) {
            $controls.addClass("flow");
        }
        else {
            $controls.removeClass("flow");
	        //setSize();
        };
/*
        if (content - viewport - $wrapCustom.scrollTop() === 0) {
            $controls.addClass("atBottom");
        } else {
            $controls.removeClass("atBottom");
        };
*/
    };

    $metabox.addClass("optionsframework-ready");
    $wrap.css({
        "padding-bottom" : $controls.height()
    });

	if(!$("body").hasClass("the7-customizer")){
	    setSize();
	    setFlow();
    }
    else {
	    setSize();
	    setFlowCustom();
	    $(".nav-tab-wrapper > a").on("click", function() { setSize(); });
    }

    $(window).on("scroll", function() {
        setFlow();
    });

    $(window).on("resize", function() {
        setSize();
    });
    
    $("#optionsframework-wrap").on("scroll", function() {
	    setFlowCustom();
    });

	$('.code-editor').each(function () {
		var editor = window.The7Options.codeEditor(this);

		if (!editor) {
			return;
		}

		$('body')
			.on('the7.options.tabChange', function () {
				if ($(editor.codemirror.getWrapperElement()).is(':hidden')) {
					return;
				}
				editor.codemirror.refresh();
			})
			.on('the7.options.submit', function () {
				editor.codemirror.save();
			});
	});
});

function dtRadioImagesSetCheckbox( target ) {
	jQuery('#'+target).trigger('click');
}

/**
 * Background image preset images.
 */
jQuery(function($){
	$('.section-background_img .of-radio-img-img').on('click', function() {
		var selector = $(this).parents('.section-background_img'),
			attachment = $(this).attr('data-full-src'),
			preview = $(this).attr('src'),
			uploadButton = selector.find('.upload-button'),
			screenshot = selector.find('.screenshot');

		selector.find('.upload').val(attachment);
		selector.find('.upload-id').val(0);

		if ( screenshot.find('img').length > 0 ) {
			// screenshot.hide();
			screenshot.find('img').attr('src', attachment);
			screenshot.show();
		} else {
			screenshot.empty().append('<img src="' + attachment + '"><a class="remove-image">Remove</a>').slideDown('fast');
		}
		// screenshot.empty().hide().append('<img src="' + attachment + '"><a class="remove-image">Remove</a>').slideDown('fast');

		if ( uploadButton.length > 0 ) {
			uploadButton.unbind().addClass('remove-file').removeClass('upload-button').val(optionsframework_l10n.remove);
			optionsframework_file_bindings(selector);
		}

		selector.find('.of-background-properties').slideDown();

	});
});


/* Device and options panel switch constrols */
jQuery(function($){
	var $desktop = $("#wp-admin-bar-view-desktop > a"),
		$tablet = $("#wp-admin-bar-view-tablet > a"),
		$mobile = $("#wp-admin-bar-view-mobile > a"),
		$preview = $("#the7-customizer-preview"),
		$controls = $("#wp-admin-bar-view-controls"),
		$view = $("#wp-admin-bar-the7-options-preview-switcher > a"),
		$device = $("#wp-admin-bar-view");
		$body = $("body");

	$desktop.on("click", function() {
		$preview.attr("class", "desktop-view");
		$device.attr("class", "menupop hover view-desktop");
		wpCookies.set("the7-options-preview-device", "desktop");
	});
	$tablet.on("click", function() {
		$preview.attr("class", "tablet-view");
		$device.attr("class", "menupop hover view-tablet");
		wpCookies.set("the7-options-preview-device", "tablet");
	});
	$mobile.on("click", function() {
		$preview.attr("class", "mobile-view");
		$device.attr("class", "menupop hover view-mobile");
		wpCookies.set("the7-options-preview-device", "mobile");
	});
	$controls.on("click", function() {
		$body.toggleClass("hide-controls");
		if ($body.hasClass("hide-controls")) {
			$controls.removeClass("panel-shown").addClass("panel-hidden");
		} else {
			$controls.removeClass("panel-hidden").addClass("panel-shown");
		}
	});

	switch (wpCookies.get("the7-options-preview-device")) {
		case "tablet":
			$preview.attr("class", "tablet-view");
			break;
		case "mobile":
			$preview.attr("class", "mobile-view");
			break;
		default:
			$preview.attr("class", "desktop-view");
	}

	$("#the7-customizer-preview").on("load", function() {
		wpCookies.set("the7-options-preview-url", document.getElementById("the7-customizer-preview").contentWindow.location.href);
	});
});
;
(function ($) {
	"use strict";

	// Class that handles errors messages.
	var settingsError = (function() {
		function settingsError(noticeID, insertAfter) {
			this.insertAfterObj = $(insertAfter);
			this.noticeID = noticeID;
			this.noticeTplObj = $('<div id="'+noticeID+'" class="settings-error notice"></div>');
		}

		settingsError.prototype.addError = function(content, type) {
			if (!content) {
				return $();
			}

            $("html, body").animate({ scrollTop: 0}, 400);
            return this.noticeTplObj.clone().html(content).addClass(type).insertAfter(this.insertAfterObj);
		};

		settingsError.prototype.removeErrors = function() {
			$("#"+this.noticeID).remove();
		};

		settingsError.prototype.prepareMsg = function(msg) {
			return "<p><strong>" + msg + "</strong></p>";
		};

		return settingsError;
	}());

	var pageErrors = new settingsError("optionsframework-options-saved", "#optionsframework-wrap > h1");

	var Buttons = (function () {
		function Buttons(buttonsObj) {
			this.buttonsObj = buttonsObj;
			this.activeButton = null;
		}

		Buttons.prototype.setActiveButton = function(button) {
			this.activeButton = this.buttonsObj.filter(button);

			// Show busy text.
			var busyValue = this.activeButton.attr("data-busy-value");
			if (busyValue) {
				this.activeButton.data("originValue", this.activeButton.val());
				this.activeButton.val(busyValue);
			}

			// Keep button pressed.
			this.activeButton.addClass("active");

			// Prevent clicks.
			this.buttonsObj.addClass("busy");
		};

		Buttons.prototype.resetActiveButton = function() {
			if (!this.activeButton) {
				return false;
			}

			if (this.activeButton.data("originValue")) {
				// Reset button text.
				this.activeButton.val(this.activeButton.data("originValue"));
			}

			// Deactivate.
			this.activeButton.removeClass("active");

			this.activeButton = null;

			// Allow click.
			this.buttonsObj.removeClass("busy");

			return true;
		};

		return Buttons;
	}());

	var submitButtons = new Buttons( $("#optionsframework-submit input[type=submit]") );

	submitButtons.buttonsObj.on("click", function (e) {
		var $this = $(this);

		if ($this.is(".reset-button") && !confirm(optionsframework.resetMsg)) {
			e.preventDefault();
			return false;
		}

		if ($this.hasClass("busy")) {
			return false;
		}

		var spinnerClass = ".submit-spinner";
		if ($this.hasClass("button-secondary")) {
			spinnerClass = ".reset-spinner";
		}

		submitButtons.setActiveButton($this);
		pageErrors.removeErrors();
	});

	$("#optionsframework-form").ajaxForm({
        url: ajaxurl,
        type: "post",
		beforeSerialize: function() {
        	$('body').trigger('the7.options.submit');
		},
        success: function (response) {
        	try {
                if (response.data.msg) {
                    response.data.msg = pageErrors.prepareMsg(response.data.msg);
                }

                if (response.success) {
                    var $error = pageErrors.addError(response.data.msg, "updated");
                    setTimeout(function () {
                        $error.fadeOut();
                    }, 5000);

                    if (submitButtons.activeButton.hasClass("of-reload-page-on-click")) {
                        window.location.reload(true);
                    }

                    if (response.data.redirectTo) {
                        window.location.assign(response.data.redirectTo);
                    }
                } else {
                    // Error handling here.
                    pageErrors.addError(response.data.msg, "error");
                }

                var previewIframe = document.getElementById('the7-customizer-preview');
                if (previewIframe) {
                    previewIframe.contentWindow.location.reload(true);
                }
            } catch (error) {
                pageErrors.addError(pageErrors.prepareMsg(optionsframework.serverErrorMsg), "error");
        		console.log(response, error);
			}

            submitButtons.resetActiveButton();
        },
        error: function(jqXHR) {
        	$.post({
				url: ajaxurl,
				data: {
					action: 'get_the7_options_last_error'
				}
			}).done(function(response) {
				try {
                    pageErrors.addError(pageErrors.prepareMsg(response.data.msg), "error");
                    console.log(response.data.msg);
                } catch (error) {
                    pageErrors.addError(pageErrors.prepareMsg(optionsframework.serverErrorMsg), "error");
                    console.log(response, error);
				}
			});
			submitButtons.resetActiveButton();
		},
	});
})(jQuery);
