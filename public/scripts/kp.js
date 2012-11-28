// kp.js 20121113

$(function(){
	var endsWith = function (str, suffix) {
	    return str.indexOf(suffix, str.length - suffix.length) !== -1;
	};
	
	// check over all inputs with jqInput's name if one is checked
	var isAnswerChecked = function (jqInput) {
		var name = jqInput.attr('name');
		
		// use double-quotes in selector as names might end with []
		return $('.answer[name="' + name + '"]:checked').length > 0;
	};
	
	var isOkImg = function (jqNumberImg) {
		return endsWith(jqNumberImg.attr('src'), 'ok.png');
	}
	
	$('.answer').change(function(){
		var jqInput = $(this);
		var isCheckbox = jqInput.is(':checkbox');
		var jqNumberImg = jqInput.closest('tr').find('> .number > img');

		if (isAnswerChecked($(this)) && !isOkImg(jqNumberImg)) {
			// keep the number img src value in numberSrc if checkbox will
			// be fully unchecked again
			if (isCheckbox)
				jqNumberImg.data('numberSrc', jqNumberImg.attr('src'));
			
			jqNumberImg.attr('src', $(document).data('okImgUrl'));
		}
		else if (isCheckbox && !isAnswerChecked(jqInput) && isOkImg(jqNumberImg)) {
			jqNumberImg.attr('src', jqNumberImg.data('numberSrc'));
		}
		
		// no bubling
		return false;
	});
	
	// in case of reload, some of them might be ok
	// check by triggering the first checkbox/radio input change event
	$('td > div:first-child > .answer').trigger('change');
});

// debugging
function show(node) {
	alert($('<div>').append($(node).clone()).remove().html());
}