/*
 *	copyright Phonogram Co. 2012
 *	file: bootstrap-iconify.js
 *	author: Joe Savona
 *
 *	サブミットボタン（インプットタイプ）をボタンタグに変換する。
 * <input type="submit"... />にはHTMLを入れないので、
 * アイコン使えない。アイコンのクラスをdata-iconifyで指定したら、
 * スクリプトがもとのインプットを隠して、変わりのボタンタグを作成して
 * アイコンを入れる。自動的に変換されます！
 *
 *	Example:
 *	<input type="submit" data-iconify="icon-search" value="search" />
 *	->
 *	<button type="submit"><i class="icon-search"></i> search</button>
 */
(function($) {

	$(function() {
		$('input[data-iconify]').each(function() {
			var $input = $(this),
				$btn = $('<button></button>');
			$btn.addClass($input.attr('class'));
			if ($input.data('iconify-placement') === 'after') {
				$btn
					.text($input.val() + ' ')
					.append('<i class="' + $input.data('iconify') + '"></i>');
			} else {
				$btn
					.text(' ' + $input.val())
					.prepend('<i class="' + $input.data('iconify') + '"></i>');
			}
			$btn.click(function() {
				$input.click();
			});
			$input
				.hide()
				.after($btn);
		});
	});
	
})(jQuery);