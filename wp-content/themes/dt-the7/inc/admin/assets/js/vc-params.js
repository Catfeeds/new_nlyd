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