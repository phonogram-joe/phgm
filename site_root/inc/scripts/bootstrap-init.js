/*
 *	copyright Phonogram Co. 2012
 *	file: bootstrap-init.js
 *	author: Joe Savona
 *
 *	スクリプトにより動く部分の初期化
 */
(function($) {

	/*
	 *	複数のレコードを一気に入力できる場合、レコード数を増やすようにボタンを下に置く。クリックすると新規の空白レコードインプットが出てくる
	 *
	 *	<div class="data-item">...<input>...</div>
	 *	<div class="data-item">...<input>...</div>
	 *	<div class="data-item">...<input>...</div>
	 *	<button data-show-more="data-item">増やす</button>
	 *
	 *	注意
	 *		ボタンのdata-show-moreアトリビュートは項目の親タグのクラスと一致するべき
	 *
	 */
	var showMore = function(e) {
		var $el = $(e.target),
			$nextRow = $('.' + $el.data('show-more')).filter(':hidden');
		if ($nextRow.size()) {
			$nextRow.filter(':first').fadeIn();
			if ($nextRow.size() === 1) {
				$el.hide();
			}
		}
	}

	var indexFormSubmit = function(e) {
		var $el = $(e.target),
			$form;
		$form = $el.closest('form');
		if ($form.size()) {
			$form.submit();
		}
	}

	$(function() {
		$('.js-conditional').fadeIn();
		//	項目が複数ある場合の「もっと表示」ボタン
		$('body').on('click', 'button[data-show-more]', showMore);

		//	一覧画面の絞り込む条件が変わる際にフォームを投稿する
		$('.index-form').on('change', 'select, .datetime', indexFormSubmit);
	});
	
})(jQuery);