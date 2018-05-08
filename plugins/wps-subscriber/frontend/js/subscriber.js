var wps_subscriber = function() {
	var $ = jQuery;

    return {
		init: function() {
			var $form = $('.wps-subscriber-wrap');

			$form.on('submit', function(e) {
				e.preventDefault();

				var $email = $form.find('input[name=email]'),
				email = $email.val();
				if ($.trim(email) == '') {
					$email.val('');
					$email.focus();
					return;
				}
				var postData = {
					action: 'wps_post_subscriber',
					security : wpsSubscribers.security,
					email: email
				};
				$.ajax({
					url: wpsSubscribers.ajaxurl,
					data: postData,
					dataType: 'json'
				}).done(function( jsonData ) {
					console.log(jsonData);
				});
			});
		}
    }
}();

$(document).ready(function(){
	wps_subscriber.init();
});