 /*
 *	copyright Phonogram Co. 2012
 *	file: bootstrap-datetime.js
 *	author: Joe Savona
 */
(function($) {
	var HoverPanel;

	HoverPanel = function($el, options) {
		this.initialize($el, options);
	}

	HoverPanel.CANCEL_EVENT = 'hoverpanel-cancel';
	HoverPanel.CLOSE_EVENT = 'hoverpanel-close';
	HoverPanel.OPEN_EVENT = 'hoverpanel-open';

	HoverPanel.prototype = {
		constructor: HoverPanel

		, initialize: function($el, options) {
			var $parent;
			this.$el = $el;
			$('body').append($el);
			$parent = options && options.parent ? options.parent : null;
			if ($parent && $parent.size()) {
				this.show($parent);
			}
		}

		, _show: function($parent) {
			if (!$parent.size()) {
				return;
			}
			this.$el
				.removeClass('hide')
				.position({
					of: $parent,
					my: 'center top',
					at: 'center bottom',
					collision: 'fit fit'
				})
				.on(
					'click.hp', 
					'.hoverpanel-header .close', 
					$.proxy(this.cancel, this)
				)
				.on(
					'click.hp', 
					$.proxy(this.cancelEvent, this)
				);
				
			$('body')
				.on(
					'click.hp', 
					$.proxy(this.cancel, this)
				);
		}

		, _hide: function() {
			this.$el.off('.hp');
			$('body').off('.hp');
			this.$el.addClass('hide');
		}

		, cancelEvent: function($event) {
			$event.preventDefault();
			$event.stopPropagation();
		}

		, cancel: function() {
			this._hide();
			this.$el.trigger({type: HoverPanel.CANCEL_EVENT});
			return false;
		}

		, close: function() {
			this._hide();
			this.$el.trigger({type: HoverPanel.CLOSE_EVENT});
			return false;
		}

		, open: function($parent) {
			this._show($parent);
			this.$el.trigger({type: HoverPanel.OPEN_EVENT});
			return false;
		}
	}

	$.fn.hoverpanel = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data('hoverpanel'),
				options = typeof option == 'object' && option;
			if (!data) {
				$this.data('hoverpanel', (data = new HoverPanel($this, options)));
			}
			if (typeof option == 'string') {
				data[option]();
			}
		});
	}

	$.fn.hoverpanel.Constructor = HoverPanel;

})(jQuery);