 /*
 *	copyright Phonogram Co. 2012
 *	file: bootstrap-datetime.js
 *	author: Joe Savona
 *
 *	日時入力 Datetime Input

 	<input type="datetime" class="datetime" value="2012-04-01 14:33:59">
 
	<div class="datetime-input modal hide">
	  <div class="modal-header">
	    <a class="close" data-dismiss="modal" title="キャンセル">×</a>
	    <h3>日時入力</h3>
	  </div>
	  <div class="modal-body">
	    <table class="table table-bordered x-table-condensed datetime-date">
	      <thead>
	        <tr>
	          <th class="prev"><a href="#" class="datetime-prev" title="前の月へ"><i class="icon-arrow-left"></i></a></th>
	          <th class="year-month" colspan="5"><a href="#" title="年月の選択" class="btn datetime-yyyymm-label">2012年4月</a>
	            <span class="datetime-yyyymm-input">
	              <input type="text" size="4" name="year" value="2012">
	              <input type="text" size="2" name="month" value="4">
	              <a href="#" class="btn datetime-yyyymm-save">年月へ飛ぶ</a>
	            </span>
	          </th>
	          <th class="next"><a href="#" class="datetime-next" title="次の月へ"><i class="icon-arrow-right"></i></a></th>
	        </tr>
	        <tr>
	          <th class="day">日</th>
	          <th class="day">月</th>
	          <th class="day">火</th>
	          <th class="day">水</th>
	          <th class="day">木</th>
	          <th class="day">金</th>
	          <th class="day">土</th>
	        </tr>
	      </thead>
	      <tbody>
	      </tbody>
	    </table>
	    <table class="table table-bordered x-table-condensed datetime-time">
	      <thead>
	        <tr>
	          <th>時</th>
	          <th>分</th>
	        </tr>
	      </thead>
	      <tbody>
	        <tr>
	          <td><select name="hour">
	            <option value="10">10時</option>
	            <option value="11">11時</option>
	            <option value="12">12時</option>
	            <option value="13">13時</option>
	            <option value="14">14時</option>
	            <option value="15">15時</option>
	            <option value="16">16時</option>
	          </select></td>
	          <td><select name="minute">
	            <option value="00">0分</option>
	            <option value="30">30分</option>
	          </select></td>
	        </tr>
	      </tbody>
	    </table>
	  </div>
	  <div class="modal-footer">
	    <p class="pull-left">
	      <a href="#" class="btn datetime-now">現在の日時</a>
	    </p>
	    <a href="#" class="btn btn-primary datetime-close">保存する</a>
	  </div>
	</div><!--/datetime-input-->
 */
(function($) {
	var DatetimeInput;

	DatetimeInput = function($el) {
		this.$el = $el;
		this.$inputYear = this.$el.find('.datetime-yyyymm-input [name=year]');
		this.$inputMonth = this.$el.find('.datetime-yyyymm-input [name=month]');
		this.$inputHour = this.$el.find('.datetime-time [name=hour]');
		this.$inputMinute = this.$el.find('.datetime-time [name=minute]');
		this.$yearMonthLabel = this.$el.find('.datetime-yyyymm-label');
		this.$currentInput = null;
		this.activeDate = null;
		this.init();
	}

	DatetimeInput.html = '<div class="datetime-input modal hide"> <div class="modal-header"> <a class="close" data-dismiss="modal" title="キャンセル">×</a> <h3>日時入力</h3> </div> <div class="modal-body"> <table class="table table-bordered x-table-condensed datetime-date"> <thead> <tr> <th class="prev"><a href="#" class="datetime-prev" title="前の月へ"><i class="icon-arrow-left"></i></a></th> <th class="year-month" colspan="5"><a href="#" title="年月の選択" class="btn datetime-yyyymm-label">2012年4月</a> <span class="datetime-yyyymm-input"> <input type="text" size="4" name="year" value="2012"> <input type="text" size="2" name="month" value="4"> <a href="#" class="btn datetime-yyyymm-save">年月へ飛ぶ</a> </span> </th> <th class="next"><a href="#" class="datetime-next" title="次の月へ"><i class="icon-arrow-right"></i></a></th> </tr> <tr> <th class="day">日</th> <th class="day">月</th> <th class="day">火</th> <th class="day">水</th> <th class="day">木</th> <th class="day">金</th> <th class="day">土</th> </tr> </thead> <tbody> </tbody> </table> <table class="table datetime-time"> <thead> <tr> <th>時</th> <th>分</th> </tr> </thead> <tbody> <tr> <td><select name="hour"> <option value="10">10時</option> <option value="11">11時</option> <option value="12">12時</option> <option value="13">13時</option> <option value="14">14時</option> <option value="15">15時</option> <option value="16">16時</option> </select></td> <td><select name="minute"> <option value="00">0分</option> <option value="30">30分</option> </select></td> </tr> </tbody> </table> </div> <div class="modal-footer"> <p class="pull-left"> <a href="#" class="btn datetime-now"><i class="icon-time"></i> 現在の日時</a> </p> <a href="#" class="btn btn-primary datetime-close"><i class="icon-ok-sign icon-white"></i> 保存する</a> </div> </div>';

	DatetimeInput.instance = null;

	DatetimeInput.FORMAT_DATA_ATTR = 'datetime-format';
	DatetimeInput.FORMAT_DATETIME = 'datetime';
	DatetimeInput.FORMAT_DATE = 'date';

	DatetimeInput.datetimeFormat = /^\s*(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\s*$/;
	DatetimeInput.dateFormat = /^\s*(\d{4})-(\d{2})-(\d{2})\s*$/;

	DatetimeInput.millisecondsInWeek = 1000 * 60 * 60 * 24 * 7; //1000 ms/sec, 60 secs/min, 60 mins/hr, 24 hrs/day, 7 days/wk

	DatetimeInput.getInstance = function() {
		var $base;
		if (DatetimeInput.instance === null) {
			$base = $(DatetimeInput.html);
			$('body').append($base);
			DatetimeInput.instance = new DatetimeInput($base);
		}
		return DatetimeInput.instance;
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

	DatetimeInput.prototype = {
		constructor: DatetimeInput

		, init: function() {
			this.$el.on('click.dt', '.datetime-yyyymm-label', $.proxy(this.openYearMonth, this));
			this.$el.on('click.dt', '.datetime-yyyymm-save', $.proxy(this.saveYearMonth, this));
			this.$el.on('click.dt', '.datetime-prev', $.proxy(this.goPreviousMonth, this));
			this.$el.on('click.dt', '.datetime-next', $.proxy(this.goNextMonth, this));
			this.$el.on('click.dt', '.datetime-now', $.proxy(this.goNow, this));
			this.$el.on('click.dt', '.datetime-close', $.proxy(this.close, this));
			this.$el.on('click.dt', '.datetime-date td a', $.proxy(this.calendarChoice, this));
			this.$inputHour.on('change.dt, blur.dt', $.proxy(this.updateTime, this));
			this.$inputMinute.on('change.dt, blur.dt', $.proxy(this.updateTime, this));
		}

		, uninit: function() {
			this.$el.off('.dt');
			this.$inputHour.off('.dt');
			this.$inputMinute.off('.dt');
		}

		, openYearMonth: function() {
			this.$el
				.find('.datetime-date').addClass('datetime-yyyymm-on')
				.find('.datetime-yyyymm-input input:first').focus();
		}

		, saveYearMonth: function() {
			var year = parseInt(this.$inputYear.val(), 10),
				month = parseInt(this.$inputMonth.val(), 10);
			if (isNaN(year) || isNaN(month) || year < 2000 || year > 2050 || month < 0 || month >= 12) {
				this.$inputYear.val(this.activeDate.getFullYear());
				this.$inputMonth.val(this.activeDate.getMonth()+1);
			} else {
				month -= 1;
				this.goDate(new Date(year, month, 1));
			}
			this.$el.find('.datetime-date').removeClass('datetime-yyyymm-on');
		}

		, calendarChoice: function(e) {
			var date = $(e.target).attr('title');
			date = this._parseDate(date);
			if (date === null) {
				return;
			}
			this.goDate(date);
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
			this.$inputHour.find('option[value=' + date.getHours() + ']').prop('selected', true);
			if (date.getMinutes() < 30) {
				this.$inputMinute.find('option[value=00]').prop('selected', true);
			} else {
				this.$inputMinute.find('option[value=30]').prop('selected', true);
			}
		}

		, goPreviousMonth: function() {
			this.goDate(new Date(this.activeDate.getFullYear(), this.activeDate.getMonth() - 1, 1));
		}

		, goNextMonth: function() {
			this.goDate(new Date(this.activeDate.getFullYear(), this.activeDate.getMonth() + 1, 1));
		}

		, goNow: function() {
			var date = new Date();
			this.setTime(date);
			this.goDate(date);
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
			this.$inputHour.find('option[value=' + this.activeDate.getHours() + ']').prop('selected', true);
			if (this.activeDate.getMinutes() < 30) {
				this.$inputMinute.find('option[value=00]').prop('selected', true);
			} else {
				this.$inputMinute.find('option[value=30]').prop('selected', true);
			}
			while (currentWeekIndex < weekCount) {
				currentDayIndex = 0;
				bodyHtml += '<tr>';
				while (currentDayIndex < 7) {
					currentDate = new Date(firstSunday.getFullYear(), firstSunday.getMonth(), firstSunday.getDate() + (currentWeekIndex * 7) + currentDayIndex);
					currentDateClass = (currentDate.getMonth() !== date.getMonth() ? 'inactive' : (currentDate.getDate() !== date.getDate() ? 'active' : 'selected'));
					bodyHtml += '<td class="' + currentDateClass + '"><a title="' + this._formatDate(currentDate) + '">' + currentDate.getDate() + '</a></td>';
					currentDayIndex += 1;
				}
				bodyHtml += '</tr>';
				currentWeekIndex += 1;
			}
			this.$el.find('.datetime-date tbody').html(bodyHtml);
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

		, _isDatetimeInput: function() {
			return this.$currentInput.data(DatetimeInput.FORMAT_DATA_ATTR) !== DatetimeInput.FORMAT_DATE;
		}

		, _isDateInput: function() {
			return this.$currentInput.data(DatetimeInput.FORMAT_DATA_ATTR) === DatetimeInput.FORMAT_DATE;
		}

		, close: function() {
			this.$el.modal('hide');
			this.$currentInput.val(this._formatDate(this.activeDate));
			this.$currentInput.trigger('change');
			this.activeDate = null;
			this.$currentInput = null;
		}

		, openFor: function($input) {
			var date;
			this.$currentInput = $input;
			date = this._parseDate($input.val());
			if (date === null) {
				date = new Date();
			}
			this._isDatetimeInput() ? this.$el.find('.datetime-time').show() : this.$el.find('.datetime-time').hide();
			this.setTime(date);
			this.goDate(date);
			this.$el.modal('show');
		}

	};

	$(function() {
		$('body').on('click', 'input.datetime', function() {
			var datetime = DatetimeInput.getInstance();
			datetime.openFor($(this));
		});
	});

})(jQuery);