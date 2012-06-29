(function($) {
	var CONVERSIONS = {
		hankaku: 'hankaku'
		, zenkaku : 'zenkaku'
		, hankakuDigits: 'hankakuDigits'
		, zenkakuDigits: 'zenkakuDigits'
	};
	$.convertKana = function(input, toType) {
		var hankaku = new Array("ｶﾞ", "ｷﾞ", "ｸﾞ", "ｹﾞ", "ｺﾞ", "ｻﾞ", "ｼﾞ", "ｽﾞ", "ｾﾞ", "ｿﾞ", "ﾀﾞ", "ﾁﾞ", "ﾂﾞ", "ﾃﾞ", "ﾄﾞ", "ﾊﾞ", "ﾊﾟ", "ﾋﾞ", "ﾋﾟ", "ﾌﾞ", "ﾌﾟ", "ﾍﾞ", "ﾍﾟ", "ﾎﾞ", "ﾎﾟ", "ｳﾞ", "ｧ", "ｱ", "ｨ", "ｲ", "ｩ", "ｳ", "ｪ", "ｴ", "ｫ", "ｵ", "ｶ", "ｷ", "ｸ", "ｹ", "ｺ", "ｻ", "ｼ", "ｽ", "ｾ", "ｿ", "ﾀ", "ﾁ", "ｯ", "ﾂ", "ﾃ", "ﾄ", "ﾅ", "ﾆ", "ﾇ", "ﾈ", "ﾉ", "ﾊ", "ﾋ", "ﾌ", "ﾍ", "ﾎ", "ﾏ", "ﾐ", "ﾑ", "ﾒ", "ﾓ", "ｬ", "ﾔ", "ｭ", "ﾕ", "ｮ", "ﾖ", "ﾗ", "ﾘ", "ﾙ", "ﾚ", "ﾛ", "ﾜ", "ｦ", "ﾝ", "｡", "｢", "｣", "､", "･", "ｰ", "ﾞ", "ﾟ", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"),
			hankakuDigits = new Array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", ".", "."),
			zenkaku  = new Array("ガ", "ギ", "グ", "ゲ", "ゴ", "ザ", "ジ", "ズ", "ゼ", "ゾ", "ダ", "ヂ", "ヅ", "デ", "ド", "バ", "パ", "ビ", "ピ", "ブ", "プ", "ベ", "ペ", "ボ", "ポ", "ヴ", "ァ", "ア", "ィ", "イ", "ゥ", "ウ", "ェ", "エ", "ォ", "オ", "カ", "キ", "ク", "ケ", "コ", "サ", "シ", "ス", "セ", "ソ", "タ", "チ", "ッ", "ツ", "テ", "ト", "ナ", "ニ", "ヌ", "ネ", "ノ", "ハ", "ヒ", "フ", "ヘ", "ホ", "マ", "ミ", "ム", "メ", "モ", "ャ", "ヤ", "ュ", "ユ", "ョ", "ヨ", "ラ", "リ", "ル", "レ", "ロ", "ワ", "ヲ", "ン", "。", "「", "」", "、", "・", "ー", "゛", "゜", "０", "１", "２", "３", "４", "５", "６", "７", "８", "９"),
			zenkakuDigits = new Array("０", "１", "２", "３", "４", "５", "６", "７", "８", "９", "。", "．"),
			index,
			count = hankaku.length,
			fromKana,
			toKana;
		if (toType == CONVERSIONS.hankaku) {
			toKana = hankaku;
			fromKana = zenkaku;
		} else if (toType == CONVERSIONS.zenkaku) {
			toKana = zenkaku;
			fromKana = hankaku;
		} else if (toType == CONVERSIONS.hankakuDigits) {
			toKana = hankakuDigits;
			fromKana = zenkakuDigits;
		} else if (toType == CONVERSIONS.zenkakuDigits) {
			toKana = zenkakuDigits;
			fromKana = hankakuDigits;
		}
		for (index = 0; index < count; index++) {
			while (input.indexOf(fromKana[index]) >= 0) {
				input = input.replace(fromKana[index], toKana[index]);
			}
		}	
		return input;
	}
	for (var prop in CONVERSIONS) {
		if (!CONVERSIONS.hasOwnProperty(prop)) {
			continue;
		}
		$.convertKana[prop] = CONVERSIONS[prop];
	}
})(jQuery);