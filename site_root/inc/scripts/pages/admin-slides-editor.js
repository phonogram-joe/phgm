(function($){
	var init,
		saveChanges,
		queueSaveChanges,
		onDeleteSlide,
		onDeleteOver,
		onDeleteOut,
		doSaveChanges,
		onSaveChangeSuccess,
		onSaveChangeError,
		onStartEdit,
		onFinishEdit,
		$saveXhr = null,
		$slidesForm,
		saveTimeoutId = null,
		alertHtml = '<div class="alert fade in"><button class="close" data-dismiss="alert">×</button><span class="slides-edit-alert"></span></div>',
		ALERT_DISPLAY_TIME = 2000;

	init = function() {
		$slidesForm = $('.slides-edit-form');
		if (!$slidesForm.size()) {
			return;
		}
		$slidesForm.prepend($slidesForm.parent().find('.alert').addClass('fade in'));
		window.setTimeout(function() {
			$slidesForm.find('.alert').alert('close');
		}, ALERT_DISPLAY_TIME);
		$('#slides-list')
			.sortable({
				'update': doSaveChanges,
				'cancel': '.thumbnail'
			});
			//.disableSelection();
		$('#slides-list')
			.on('focus', '[contenteditable=true]', onStartEdit)
			.on('blur keyup paste', '[contenteditable=true]', onFinishEdit);
		$('.slides-edit-deletebox')
			.droppable({
				'drop': onDeleteSlide,
				'over': onDeleteOver,
				'out': onDeleteOut
			})
	};

	onDeleteSlide = function(event, $ui) {
		var $slide = $ui.draggable;
		$slide.find('.slides-field-delete').val(1);
		$slide.hide();
		doSaveChanges();
		onDeleteOut();
	};

	onDeleteOver = function() {
		$(this).addClass('over');
	};

	onDeleteOut = function() {
		$(this).removeClass('over');
	};

	doSaveChanges = function() {
		if (saveTimeoutId) {
			window.clearTimeout(saveTimeoutId);
		}
		if ($saveXhr) {
			saveTimeoutId = window.setTimeout(doSaveChanges, 100);
			return;
		}
		saveChanges();
	};
	queueSaveChanges = _.debounce(doSaveChanges, 1000);

	saveChanges = function() {
		var $alert = $(alertHtml);
		$('#slides-list li').each(function(index) {
			$(this).find('.slides-field-orderby').val(index);
		});
		$saveXhr = $.ajax({
			data: $slidesForm.serialize(),
			dataType: 'json',
			success: onSaveChangeSuccess,
			error: onSaveChangeError,
			cache: false,
			url: $slidesForm.attr('action'),
			type: ($slidesForm.attr('method') || 'POST')
		});
		$slidesForm.find('.alert').remove();
		$alert.find('span').text('保存中...');
		$slidesForm.prepend($alert);
	};

	onSaveChangeSuccess = function() {
		var $alert = $slidesForm.find('.alert');
		$alert.find('span').text('保存しました');
		window.setTimeout(function() {
			$alert.alert('close');
		}, ALERT_DISPLAY_TIME);

		$saveXhr = null;
	};

	onSaveChangeError = function() {
		var $alert = $slidesForm.find('.alert');
		$alert.find('span').text('保存に失敗しました');
		window.setTimeout(function() {
			$alert.alert('close');
		}, ALERT_DISPLAY_TIME);

		$saveXhr = null;
	};

	onStartEdit = function() {
		document.designMode = 'on';
		$(this).data('contenteditable', this.innerHTML);
	};

	onFinishEdit = function() {
		var $item = $(this).parents('li[data-slide-id]'),
			newValue = this.innerHTML;
		document.designMode = 'off';
		if (newValue !== $(this).data('contenteditable')) {
			$(this).data('contenteditable', newValue);
			$item.find('.slides-field-content').val(newValue);
			queueSaveChanges();
		}
	};
	
	$(init);
})(jQuery);