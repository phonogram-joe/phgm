/*
 *	Copyright Phonogram Co. Ltd 2012
 *	author: Joe Savona
 *	date: 2012.04.05
 *	
 *	管理者用の依頼編集画面
 */
!function( $ ) {

	var $pointTotal,
		$pointRate,
		$currentStatus,
		$pointValues,
		$statusTimes,
		onPointChange,
		onStatusChange;

	onPointChange = function() {
		var sum = 0,
			current = NaN,
			rate = parseFloat($pointRate.find('option:selected').text(), 10);
		$pointValues.each(function() {
			current = parseFloat($(this).val(), 10);
			if (!isNaN(current)) {
				sum += current;
			}
		});
		if (!isNaN(rate)) {
			sum *= rate;
		}
		$pointTotal.text(sum);
	};

	onStatusChange = function() {
		var latestTime = '',
			$latestTimeNode = null;
		$statusTimes.each(function() {
			var $timeNode = $(this);
			if ($timeNode.val() > latestTime) {
				latestTime = $timeNode.val();
				$latestTimeNode = $timeNode;
			}
		});
		if ($latestTimeNode) {
			$currentStatus.text($latestTimeNode.closest('.controls').find('.request-status-value option:selected').text());
		} else {
			$currentStatus.text('未入力です。受付待ち状況になります。');
		}
	};

	$(function() {
		$pointTotal = $('#request-point-current');
		$pointRate = $('.request-point-rate');
		$currentStatus = $('#request-status-current');
		$pointValues = $('.request-point-value');
		$statusTimes = $('.request-status-date');

		$pointRate.on('change', onPointChange);
		$('.request-point-value').on('change', onPointChange).trigger('change');
		$('.request-status-date, .request-status-value').on('change', onStatusChange).trigger('change');
	});

}( jQuery );