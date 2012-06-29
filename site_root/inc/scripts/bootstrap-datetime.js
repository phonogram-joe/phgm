 /*
 *	copyright Phonogram Co. 2012
 *	file: bootstrap-datetime.js
 *	author: Joe Savona
 *
 *	日時入力 Datetime Input

 	<input type="datetime" class="datetime" value="2012-04-01 14:33:59">
 
	<div class="datetime-input hoverpanel hide">
		<div class="hoverpanel-header">
			<a class="close" data-dismiss="hoverpanel" title="キャンセル">×</a>
			<h3>日時入力</h3>
		</div>
		<div class="hoverpanel-body">
			<table class="table table-bordered x-table-condensed datetime-date">
			<thead>
			<tr>
				<th class="prevyear">
					<a href="#" class="datetime-prevyear" title="前の年へ"><i class="icon-fast-backward"></i></a>
				</th>
				<th class="prev">
					<a href="#" class="datetime-prev" title="前の月へ"><i class="icon-step-backward"></i></a>
				</th>
				<th class="datetime-yyyymm-label" colspan="3">
					2012年4月
				</th>
				<th class="next">
					<a href="#" class="datetime-next" title="次の月へ"><i class="icon-step-forward"></i></a>
				</th>
				<th class="nextyear">
					<a href="#" class="datetime-nextyear" title="次の年へ"><i class="icon-fast-forward"></i></a>
				</th>
			</tr>
			<tr>
				<th class="day">
					日
				</th>
				<th class="day">
					月
				</th>
				<th class="day">
					火
				</th>
				<th class="day">
					水
				</th>
				<th class="day">
					木
				</th>
				<th class="day">
					金
				</th>
				<th class="day">
					土
				</th>
			</tr>
			</thead>
			<tbody>
			</tbody>
			</table>
			<table class="table datetime-time">
			<thead>
			<tr>
				<th>
					時
				</th>
				<th>
					分
				</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td>
					<select name="hour">
						<option value="08">8時</option>
						<option value="09">9時</option>
						<option value="10">10時</option>
						<option value="11">11時</option>
						<option value="12">12時</option>
						<option value="13">13時</option>
						<option value="14">14時</option>
						<option value="15">15時</option>
						<option value="16">16時</option>
						<option value="17">17時</option>
						<option value="18">18時</option>
					</select>
				</td>
				<td>
					<select name="minute">
						<option value="00">0分</option>
						<option value="30">30分</option>
					</select>
				</td>
			</tr>
			</tbody>
			</table>
		</div>
		<div class="hoverpanel-footer">
			<p class="pull-left">
				<a href="#" class="btn datetime-now"><i class="icon-time"></i> 現在の日時</a>
			</p>
			<a href="#" class="btn btn-primary datetime-close"><i class="icon-ok-sign icon-white"></i> 保存する</a>
		</div>
	</div>
 */
(function($) {
	var DatetimeInput;

	DatetimeInput = function($el) {
		this.initialize($el);
		$.fn.hoverpanel.Constructor.prototype.initialize($el, false);
	}

	DatetimeInput.html = '<div class="datetime-input hoverpanel hide"> <div class="hoverpanel-header"> <a class="close" data-dismiss="hoverpanel" title="キャンセル">×</a> <h3>日時入力</h3> </div> <div class="hoverpanel-body"> <table class="table table-bordered x-table-condensed datetime-date"> <thead> <tr> <th class="prevyear"> <a href="#" class="datetime-prevyear" title="前の年へ"><i class="icon-fast-backward"></i></a> </th> <th class="prev"> <a href="#" class="datetime-prev" title="前の月へ"><i class="icon-step-backward"></i></a> </th> <th class="datetime-yyyymm-label" colspan="3"> 2012年4月 </th> <th class="next"> <a href="#" class="datetime-next" title="次の月へ"><i class="icon-step-forward"></i></a> </th> <th class="nextyear"> <a href="#" class="datetime-nextyear" title="次の年へ"><i class="icon-fast-forward"></i></a> </th> </tr> <tr> <th class="day"> 日 </th> <th class="day"> 月 </th> <th class="day"> 火 </th> <th class="day"> 水 </th> <th class="day"> 木 </th> <th class="day"> 金 </th> <th class="day"> 土 </th> </tr> </thead> <tbody> </tbody> </table> <table class="table datetime-time"> <thead> <tr> <th> 時 </th> <th> 分 </th> </tr> </thead> <tbody> <tr> <td> <select name="hour"> <option value="08">8時</option> <option value="09">9時</option> <option value="10">10時</option> <option value="11">11時</option> <option value="12">12時</option> <option value="13">13時</option> <option value="14">14時</option> <option value="15">15時</option> <option value="16">16時</option> <option value="17">17時</option> <option value="18">18時</option> </select> </td> <td> <select name="minute"> <option value="00">0分</option> <option value="30">30分</option> </select> </td> </tr> </tbody> </table> </div> <div class="hoverpanel-footer"> <p class="pull-left"> <a href="#" class="btn datetime-now"><i class="icon-time"></i> 現在の日時</a> </p> <a href="#" class="btn btn-primary datetime-close"><i class="icon-ok-sign icon-white"></i> 保存する</a> </div> </div>';

	DatetimeInput.instance = null;

	DatetimeInput.FORMAT_DATA_ATTR = 'datetime-format';
	DatetimeInput.FORMAT_DATETIME = 'datetime';
	DatetimeInput.FORMAT_DATE = 'date';
	DatetimeInput.HOUR_MINUTE_OPTIONS_ALL = 'all';
	DatetimeInput.DEFAULT_HOUR_OPTIONS = ['08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18'];
	DatetimeInput.DEFAULT_MINUTE_OPTIONS = ['00', '30'];

	DatetimeInput.datetimeFormat = /^\s*(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\s*$/;
	DatetimeInput.dateFormat = /^\s*(\d{4})-(\d{2})-(\d{2})\s*$/;

	DatetimeInput.millisecondsInWeek = 1000 * 60 * 60 * 24 * 7; //1000 ms/sec, 60 secs/min, 60 mins/hr, 24 hrs/day, 7 days/wk

	DatetimeInput.getInstance = function() {
		var $base;
		if (DatetimeInput.instance === null) {
			$base = $(DatetimeInput.html);
			DatetimeInput.instance = new DatetimeInput($base);
		}
		return DatetimeInput.instance;
	}

	DatetimeInput.getEmbeddable = function($input) {
		var $base,
			datetime;
		$base = $('<div class="datetime-input"></div>');
		$base.css('width', 'auto');
		$base.append($(DatetimeInput.html).find('.hoverpanel-body'));
		datetime = new DatetimeInput($base);
		datetime.openFor($input);
		return datetime;		
	}

	DatetimeInput.formatDateStr = function(date) {
		var dateParts = [date.getFullYear(), date.getMonth()+1, date.getDate()],
			index,
			part;
		for (index = 0; index < dateParts.length; index++) {
			part = ''+dateParts[index];
			if (part.length < 2) {
				part = '0' + part;
			}
			dateParts[index] = part;
		}
		return dateParts[0] + '-' + dateParts[1] + '-' + dateParts[2];
	}

	DatetimeInput.formatDatetimeStr = function(date) {
		var dateParts = [date.getFullYear(), date.getMonth()+1, date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds()],
			index,
			part;
		for (index = 0; index < dateParts.length; index++) {
			part = ''+dateParts[index];
			if (part.length < 2) {
				part = '0' + part;
			}
			dateParts[index] = part;
		}
		return dateParts[0] + '-' + dateParts[1] + '-' + dateParts[2] + ' ' + dateParts[3] + ':' + dateParts[4] + ':' + dateParts[5];
	}

	DatetimeInput.formatDateLabel = function(date) {
		return date.getFullYear() + '年' + (date.getMonth()+1) + '月';
	}

	DatetimeInput.prototype = $.extend({}, $.fn.hoverpanel.Constructor.prototype, {
		constructor: DatetimeInput

		, initialize: function($el) {
			var bindAllMethods;
			this.$el = $el;
			this.$inputHour = this.$el.find('.datetime-time [name=hour]');
			this.$inputMinute = this.$el.find('.datetime-time [name=minute]');
			this.$yearMonthLabel = this.$el.find('.datetime-yyyymm-label');
			this.$currentInput = null;
			this.activeDate = null;
			this.bound = false;

			bindAllMethods = 'goPreviousMonth goNextMonth goPreviousYear goNextYear goNow close calendarChoice updateTime'.split(' ');
			bindAllMethods.unshift(this);
			_.bindAll.apply(_, bindAllMethods);
		}

		, init: function() {
			if (this.bound) {
				return;
			}
			this.bound = true;
			this.$el.on('click.dt', '.datetime-prevyear', this.goPreviousYear);
			this.$el.on('click.dt', '.datetime-prev', this.goPreviousMonth);
			this.$el.on('click.dt', '.datetime-nextyear', this.goNextYear);
			this.$el.on('click.dt', '.datetime-next', this.goNextMonth);
			this.$el.on('click.dt', '.datetime-now', this.goNow);
			this.$el.on('click.dt', '.datetime-close', this.close);
			this.$el.on('click.dt', '.datetime-date td a', this.calendarChoice);
			this.$inputHour.on('change.dt blur.dt', this.updateTime);
			this.$inputMinute.on('change.dt blur.dt', this.updateTime);
		}

		, calendarChoice: function(e) {
			var date = $(e.target).data('date');
			date = this._parseDate(date);
			if (date === null) {
				return;
			}
			this.goDate(date);
			this.close();
		}

		, updateTime: function() {
			var date = this.activeDate,
				hour = parseInt(this.$inputHour.val(), 10),
				minute = parseInt(this.$inputMinute.val(), 10);
			if (isNaN(hour) || isNaN(minute) || hour < 0 || hour > 23 || minute < 0 || minute > 59) {
				return;
			}
			this.activeDate = new Date(date.getFullYear(), date.getMonth(), date.getDate(), hour, minute, 0);
		}

		, setTime: function(date) {
			var $matchingItem;

			$matchingItem = this.$inputHour.find('option[value=' + date.getHours() + ']');
			if ($matchingItem.size()) {
				$matchingItem.prop('selected', true);
			} else {
				$matchingItem = this.$inputHour.find('option:first');
				if ($matchingItem.size() && parseInt($matchingItem.attr('value'),10) > date.getHours()) {
					$matchingItem.prop('selected');
				} else {
					$matchingItem = this.$inputHour.find('option:last').prop('selected', true);
				}
			}

			$matchingItem = this.$inputMinute.find('option[value=' + date.getMinutes() + ']');
			if ($matchingItem.size()) {
				$matchingItem.prop('selected', true);
			} else {
				this.$inputMinute.find('option').each(function() {
					var $option = $(this);
					if (parseInt($option.attr('value'), 10) < date.getMinutes()) {
						$option.prop('selected', true);
					}
				});
			}
		}

		, goPreviousYear: function() {
			this.goDate(new Date(this.activeDate.getFullYear() - 1, this.activeDate.getMonth(), 1));
			return false;
		}

		, goPreviousMonth: function() {
			this.goDate(new Date(this.activeDate.getFullYear(), this.activeDate.getMonth() - 1, 1));
			return false;
		}

		, goNextYear: function() {
			this.goDate(new Date(this.activeDate.getFullYear() + 1, this.activeDate.getMonth(), 1));
			return false;
		}

		, goNextMonth: function() {
			this.goDate(new Date(this.activeDate.getFullYear(), this.activeDate.getMonth() + 1, 1));
			return false;
		}

		, goNow: function() {
			var date = new Date();
			this.setTime(date);
			this.goDate(date);
			return false;
		}

		, goDate: function(date) {
			var startOfMonth = new Date(date.getFullYear(), date.getMonth(), 1),
				firstSunday = new Date(startOfMonth.getFullYear(), startOfMonth.getMonth(), startOfMonth.getDate() - startOfMonth.getDay()),
				endOfMonth = new Date(startOfMonth.getFullYear(), startOfMonth.getMonth()+1, 0),
				lastSaturday = new Date(endOfMonth.getFullYear(), endOfMonth.getMonth(), endOfMonth.getDate() + (6 - endOfMonth.getDay())),
				currentDate,
				currentDateClass,
				bodyHtml = '',
				weekCount = Math.ceil((lastSaturday - firstSunday) / DatetimeInput.millisecondsInWeek),
				currentWeekIndex = 0,
				currentDayIndex = 0;
			this.activeDate = date;
			this.updateTime();
			this.$yearMonthLabel.text(DatetimeInput.formatDateLabel(this.activeDate));

			this.setTime(date);

			while (currentWeekIndex < weekCount) {
				currentDayIndex = 0;
				bodyHtml += '<tr>';
				while (currentDayIndex < 7) {
					currentDate = new Date(firstSunday.getFullYear(), firstSunday.getMonth(), firstSunday.getDate() + (currentWeekIndex * 7) + currentDayIndex);
					currentDateClass = (currentDate.getMonth() !== date.getMonth() ? 'inactive' : (currentDate.getDate() !== date.getDate() ? 'active' : 'selected'));
					bodyHtml += '<td class="' + currentDateClass + '"><a data-date="' + this._formatDate(currentDate) + '">' + currentDate.getDate() + '</a></td>';
					currentDayIndex += 1;
				}
				bodyHtml += '</tr>';
				currentWeekIndex += 1;
			}
			this.$el.find('.datetime-date tbody').html(bodyHtml);
			return false;
		}

		, _formatDate: function(date) {
			if (this._isDateInput()) {
				return DatetimeInput.formatDateStr(date);
			} else {
				return DatetimeInput.formatDatetimeStr(date);
			}
		}

		, _parseDate: function(dateStr) {
			var match,
				date,
				index,
				datetimeMatchCount = 7;
			if (this._isDateInput()) {
				match = DatetimeInput.dateFormat.exec(dateStr);
			} else {
				match = DatetimeInput.datetimeFormat.exec(dateStr);
			}
			if (match) {
				for (index = 0; index < datetimeMatchCount; index++) {
					match[index] = match.length > index ? parseInt(match[index], 10) : 0;
				}
				return new Date(match[1], match[2]-1, match[3], match[4], match[5], match[6]);
			}
			return null;
		}

		, _parseTimeOptions: function($input) {
			var hoursOptions,
				minuteOptions,
				optionIndex,
				self;
			self = this;
			//	何時の選択肢
			//	data-datetime-hours="10,11,12,13,14,15,16,17,18"
			hoursOptions = $input.data('datetime-hours') || DatetimeInput.DEFAULT_HOUR_OPTIONS;
			if (hoursOptions === DatetimeInput.HOUR_MINUTE_OPTIONS_ALL) {
				hoursOptions = [];
				for (optionIndex = 0; optionIndex < 24; optionIndex++) {
					hoursOptions.push(optionIndex < 10 ? '0' + optionIndex : '' + optionIndex);
				}
			}
			if (typeof hoursOptions === 'string') {
				hoursOptions = hoursOptions.split(',');
			}
			this.$hourInput = this.$el.find('[name=hour]');
			this.$hourInput.empty();
			$.each(hoursOptions, function(index, item) {
				self.$hourInput.append($('<option></option').attr('value', item).text(item + '時'));
			});

			//	何分の選択肢
			//	date-datetime-minutes="00,15,30,45"
			minuteOptions = $input.data('datetime-minutes') || DatetimeInput.DEFAULT_MINUTE_OPTIONS;
			if (minuteOptions === DatetimeInput.HOUR_MINUTE_OPTIONS_ALL) {
				minuteOptions = [];
				for (optionIndex = 0; optionIndex < 60; optionIndex++) {
					minuteOptions.push(optionIndex < 10 ? '0' + optionIndex : '' + optionIndex);
				}
			}
			if (typeof minuteOptions === 'string') {
				minuteOptions = minuteOptions.split(',');
			}
			this.$minuteInput = this.$el.find('[name=minute]');
			this.$minuteInput.empty();
			$.each(minuteOptions, function(index, item) {
				self.$minuteInput.append($('<option></option>').attr('value', item).text(item + '分'));
			});	
		}

		, _isDatetimeInput: function() {
			return this.$currentInput.data(DatetimeInput.FORMAT_DATA_ATTR) !== DatetimeInput.FORMAT_DATE;
		}

		, _isDateInput: function() {
			return this.$currentInput.data(DatetimeInput.FORMAT_DATA_ATTR) === DatetimeInput.FORMAT_DATE;
		}

		, close: function() {
			this.$currentInput.val(this._formatDate(this.activeDate));
			this.$currentInput.trigger('change');
			this.activeDate = null;
			this.$currentInput = null;
			$.fn.hoverpanel.Constructor.prototype.close.call(this);
		}

		, openFor: function($input) {
			var date;
			this.$currentInput = $input;

			//	インプットから日時を取得する。空である場合は現在日時にする
			date = this._parseDate($input.val());
			if (date === null) {
				date = new Date();
			}

			this._parseTimeOptions($input);

			//	インプット・日時を設定したうえで、UIを初期させる
			this.init();
			this._isDatetimeInput() ? this.$el.find('.datetime-time').show() : this.$el.find('.datetime-time').hide();
			this.setTime(date);
			this.goDate(date);
		}

		, showAt: function($input) {
			$.fn.hoverpanel.Constructor.prototype.open.call(this, $input);
		}
	});

 	$.DatetimeInput = DatetimeInput;

	$(function() {
		$('body').on('click', 'input.datetime', function() {
			var datetime = DatetimeInput.getInstance();
			datetime.openFor($(this));
			datetime.showAt($(this));
		});
	});

})(jQuery);