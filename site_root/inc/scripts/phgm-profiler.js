(function($){
	var showProfile = function() {
		var profile,
			$toggle,
			$display;
		try {
			profile = $.parseJSON($('#phgm-profiler').text());
			if (!profile) {
				return;
			}
		} catch (e) {
			return;
		}

		//	PHP情報を表示するリンクを作成してページに埋め込む
		$toggle = $('<button class="btn btn-info btn-large hide" id="phgm-profiler-toggle"><i class="icon-wrench"></i> PHP情報を表示</button>');
		$toggle.css({
			'position': 'fixed',
			'left': 0,
			'bottom': 0
		});
		$toggle.on('click', function() {
			$('#phgm-profiler-display').modal('show');
		});
		$('body').append($toggle);
		$toggle.fadeIn(300);

		//	PHP情報の表示するパネルを埋め込む
		$display = '<div class="modal fade hide" id="phgm-profiler-display"> <div class="modal-header"> <button class="close" data-dismiss="modal">×</button> <h3>PHP情報</h3> </div> <div class="modal-body"> <ul class="nav nav-tabs" id=""> <li class="active"><a href="#phgm-profiler-summary" data-toggle="tab">サマリー</a></li><li class=""><a href="#phgm-profiler-http" data-toggle="tab">HTTPタイミング</a></li> <li><a href="#phgm-profiler-php" data-toggle="tab">PHPタイミング・メモリー・ファイル</a></li> <li><a href="#phgm-profiler-db" data-toggle="tab">DB・SQL</a></li> </ul> <div class="tab-content"> <div class="tab-pane active" id="phgm-profiler-summary"> {SUMMARY} </div><div class="tab-pane " id="phgm-profiler-http"> {TIMING} </div> <div class="tab-pane" id="phgm-profiler-php"> {PHP} </div> <div class="tab-pane" id="phgm-profiler-db"> {DB} </div> </div> </div> </div>';
		$display = $display.replace('{SUMMARY}', buildSummaryDisplay(profile));
		$display = $display.replace('{TIMING}', buildTimingDisplay(profile));
		$display = $display.replace('{PHP}', buildPhpDisplay(profile));
		$display = $display.replace('{DB}', buildDbDisplay(profile));
		$display = $($display);
		$('body').append($display);
	}

	var buildSummaryDisplay = function(profile) {
		var html;
		if (!window.performance) {
			return 'Chrome・Safari・Firefoxを使用する場合はページ読み込みのタイミングを見えます。';
		}
		html = '<dl>';
		html += '<dt>ブラウザ　１　スタート→接続</dt><dd>' + (window.performance.timing.requestStart - window.performance.timing.navigationStart) + ' ms</dd>';
		html += '<dt>サーバー　２　接続→回答開始</dt><dd>' + (window.performance.timing.responseStart - window.performance.timing.requestStart) + ' ms (Apache・PHP)</dd>';
		html += '<dt>ブラウザ　３　回答読込</dt><dd>' + (window.performance.timing.responseEnd - window.performance.timing.responseStart) + ' ms</dd>';
		html += '<dt>ブラウザ　４　回答済み→表示</dt><dd>' + (window.performance.timing.loadEventEnd - window.performance.timing.responseEnd) + ' ms (CSS・JavaScript・画像の読込と解析)</dd>';
		html += '<dt>経過時間　５　合計</dt><dd>' + (window.performance.timing.loadEventEnd - window.performance.timing.navigationStart) + ' ms</dd>';
		html += '</dl>';
		return html;
	}

	var buildTimingDisplay = function(profile) {
		var events = 'navigationStart unloadEventStart unloadEventEnd redirectStart redirectEnd fetchStart domainLookupStart domainLookupEnd connectStart connectEnd secureConnectionStart requestStart responseStart responseEnd domLoading domInteractive domContentLoadedStart domContentLoadedEnd domComplete loadEventStart loadEventEnd',
			html,
			eventIndex,
			time,
			displayTime,
			firstTime = null;
		if (!window.performance) {
			return 'Chrome・Safari・Firefoxを使用する場合はページ読み込みのタイミングを見えます。';
		}
		html = '<h4>経過時間：　' + (window.performance.timing.loadEventEnd - window.performance.timing.navigationStart) + ' ms (ミリ秒)</h4>';
		html += '<dl>';	
		events = events.split(' ');
		for (eventIndex = 0; eventIndex < events.length; eventIndex++) {
			if (!window.performance.timing.hasOwnProperty(events[eventIndex])) {
				continue;
			}
			time = window.performance.timing[events[eventIndex]];
			if (time === 0) {
				continue;
			}
			if (firstTime == null) {
				firstTime = time;
				displayTime = 0;
			} else {
				displayTime = '+' + (time - firstTime);
			}
			html += '<dt>' + events[eventIndex] + '</dt><dd>' + displayTime + ' (' + time + ')' + '</dd>';
		}
		html += '</dl>';
		return html;
	}

	var buildPhpDisplay = function(profile) {
		var html,
			fileIndex,
			files = profile.files.sort();
		html = '<h3>サマリー</h3>';
		html += '<dl>';
		html += '<dt>PHP全体の経過時間</dt><dd>' + (profile.elapsed * 1000) + ' ms (ミリ秒)</dd>';
		html += '<dt>メモリー</dt><dd>' + profile.peakMemory + ' B (バイト)</dd>';
		html += '<dt>ファイル数</dt><dd>' + files.length + '</dd>';
		html += '</dl>';

		html += '<h3>部分別の経過時間</h3>';
		html += '<dl>';
		html += '<dt>フレームワーク</dt><dd>' + ((profile.elapsed - (profile.snapshots.handleRequest + profile.snapshots.rendering)) * 1000) + ' ms (' + Math.ceil(100 * ((profile.elapsed - (profile.snapshots.handleRequest + profile.snapshots.rendering)) / profile.elapsed)) + '%)</dd>';
		html += '<dt>コントローラ読込（モデル・バリデーション含め）</dt><dd>' + (profile.snapshots.loadController * 1000) + ' ms (' + Math.ceil(100 * (profile.snapshots.loadController / profile.elapsed)) + '%)</dd>';
		html += '<dt>コントローラのアクション処理</dt><dd>' + (profile.snapshots.controller * 1000) + ' ms (' + Math.ceil(100 * (profile.snapshots.controller / profile.elapsed)) + '%)</dd>';
		html += '<dt>ビュー（Smartyなど）</dt><dd>' + (profile.snapshots.rendering * 1000) + ' ms (' + Math.ceil(100 * (profile.snapshots.rendering / profile.elapsed)) + '%)</dd>';
		html += '</dl>';

		html += '<h3>ファイル (' + files.length + '個)</h3>';
		html += '<ul class="unstyled">';
		for (fileIndex = 0; fileIndex < files.length; fileIndex++) {
			html += '<li>' + files[fileIndex] + '</li>';
		}
		html += '</ul>';

		return html;
	}

	var buildDbDisplay = function(profile) {
		var html = '',
			queryHtml = '',
			totalTime = 0,
			count = 0;
		$.each(profile.dbQueries, function(id, query) {
			var elapsed = 0,
				memory = 0,
				abbrev = '',
				fromIndex = '';

			if (!isNaN(query.elapsed)) {
				elapsed = query.elapsed * 1000;
			}

			if (!isNaN(query.memory_end) && !isNaN(query.memory_start)) {
				memory = query.memory_end - query.memory_start;
			}

			if (typeof query.sql !== 'string') {
				return;
			}
			abbrev = query.sql.substr(0, query.sql.length < 40 ? null : 40),
			fromIndex = query.sql.indexOf('FROM');

			count++;
			totalTime += elapsed;
			abbrev += '...' + query.sql.substr(fromIndex, query.sql.length < fromIndex + 20 ? null : 20) + '...';
			queryHtml += '<h3>' + abbrev + '</h3>';
			queryHtml += '<dl>';
			queryHtml += '<dt>経過時間</dt><dd>' + elapsed + ' ms</dd>';
			queryHtml += '<dt>メモリー消化</dt><dd>' + memory + ' B</dd>';
			queryHtml += '<dt>SQL文</dt><dd>' + query.sql + '</dd>';
			queryHtml += '<dt>パラム</dt><dd>' + query.data + '</dd>';
			queryHtml += '</dl>';
		});
		html = '<h3>サマリー</h3>';
		html += '<dl>';
		html += '<dt>クエリの総経過時間</dt><dd>' + totalTime + ' ms</dd>';
		html += '<dt>クエリ数</dt><dd>' + count + '</dd>';
		html += '</dl>';
		html += queryHtml;
		return html;
	}

	$(function() {
		window.setTimeout(showProfile, 150);
	});
	
})(jQuery);