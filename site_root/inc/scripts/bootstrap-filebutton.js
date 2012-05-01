/*
 *	copyright Phonogram Co. 2012
 *	file: bootstrap-init.js
 *	author: Joe Savona
 *
 *	スクリプトにより動く部分の初期化
 */
(function($) {
	var FILE_ALT_BUTTON_CLASS = 'btn-file-alt',
		FILE_CANCEL_BUTTON_CLASS = 'btn-file-cancel',
		FILE_EMPTY_TEXT = '選択されていません';

	var initFile = function() {
		var $file = $(this),
			$btn = $('<button class="btn ' + FILE_ALT_BUTTON_CLASS + '"><i class="icon-file"></i> <span></span></button>'),
			$cancel = $('<button class="btn ' + FILE_CANCEL_BUTTON_CLASS + '"><i class="icon-remove"></i> リセット</button>'),
			$wrapper = $(this).parents('.controls');
		$wrapper.css({
			'position': 'relative'
		});
		$file.css({
			'position': 'relative',
			'z-index': 2,
			'opacity': 0
		});
		$btn.css({
			'position': 'absolute',
			'top': 0,
			'left': 0,
			'text-align': 'left'
		});
		$cancel.css({
		});
		$btn.width($file.width());
		$file.after($btn);
		$btn.after($cancel);
		onFileChange.call(this);
	}

	var onFileChange = function() {
		var $file = $(this),
			$btnText = $file.siblings('.' + FILE_ALT_BUTTON_CLASS).find('span'),
			$cancel = $file.siblings('.' + FILE_CANCEL_BUTTON_CLASS),
			fileText = $file.val().replace("C:\\fakepath\\", "");
		if (fileText.length === 0) {
			fileText = FILE_EMPTY_TEXT;
			$cancel.hide();
		} else {
			$cancel.show();
		}
		$btnText.text(fileText);
	}

	var onFileCancel = function() {
		var $file = $(this).siblings('input[type=file]'),
			$clone = $('<input type="file">');
		$clone.attr({
			'name': $file.attr('name'),
			'class': $file.attr('class'),
			'id': $file.attr('id'),
			'style': $file.attr('style')
		});
		$file.replaceWith($clone);
		onFileChange.call($clone);
		return false;
	}

	$(function() {
		$('form').find('input[type=file]').each(initFile);
		$('body').on('change', 'input[type=file]', onFileChange);
		$('body').on('click', '.' + FILE_CANCEL_BUTTON_CLASS, onFileCancel);
	});

})(jQuery);