(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	
	$(document).ready(function(event) {
		$(".js-mcc").each(function(){
			$(this).select2({
				minimumResultsForSearch: 20,
				dropdownParent: $(this).next('.dropDownSelect2')
			});
		
		
			$(".js-mcc").each(function(){
				$(this).on('select2:close', function (e){
					if($(this).val() == "Please choose") {
						$('.js-show-service').slideUp();
					}
					else {
						$('.js-show-service').slideUp();
						$('.js-show-service').slideDown();
					}
				});
			});
		})
		
		$("div#myUploader").dropzone({
			url:"upload-target",
		});

		$('#mccAutomatedForm1Step1').submit(mccAutomatedForm1Step1Submit);
		$('#mccAutomatedForm1Step2').submit(mccAutomatedForm1Step2Submit);

		function mccAutomatedForm1Step1Submit() {
			
			var formData = new FormData();

			formData.append("bill_one", $(this).find('input[name=file]').prop('files')[0]);
			formData.append("bill_two", $(this).find('input[name=file]').prop('files')[1]);
			formData.append("fname", $(this).find('input[name=fname]').val());
			formData.append("lname", $(this).find('input[name=lname]').val());
			formData.append("email", $(this).find('input[name=email]').val());
			formData.append("phone", $(this).find('input[name=phone]').val());
			formData.append("carrier", $(this).find('select[name=carrier]').val());
			
			formData.append("action", 'mcc_automated_form_1_step_1');

			$(this).find('button[type=submit]').attr('disabled', true);
			$(this).find('button[type=submit]').find('.btn-element').addClass('mcc-auto-hide');
			$(this).find('button[type=submit]').find('.loader').removeClass('mcc-auto-hide');

			$.ajax({
				action:  		'mcc_automated_form_1_step_1',
				type:    		"POST",
				url:     		mccAutomatedObj.ajaxurl,
				data:    		formData,
				cache: 			false,
				processData: 	false,
				contentType: 	false,
				dataType: 		'json',
				success: function(data) {
					$(".mcc_automated_form_1_step_1").addClass('mcc-auto-hide');
					$(".mcc_automated_form_1_step_2").removeClass('mcc-auto-hide');
					
					$("#recID").val(data.rec_id);
					$("#rdd_report").val(data.rdd_report);
					$("#device_report").val(data.device_report);

					if(typeof data.PhoneNumberTotalCost != 'undefined') {
						console.log(PhoneNumberTotalCost);
						$("#PhoneNumberTotalCost").val((typeof data.PhoneNumberTotalCost != 'undefined')? data.PhoneNumberTotalCost : '');
						
					}

					if(typeof data.phone_number_count != 'undefined') {
						console.log(phone_number_count);
						$("#phone_number_count").val((typeof data.phone_number_count != 'undefined')? data.phone_number_count : '');
						
					}

					if(typeof data.savings != 'undefined') {
						console.log(savings);
						$("#savings").val((typeof data.savings != 'undefined')? data.savings : '');
					}
					
					if(typeof data.giga != 'undefined') {
						$("#usedData").val((typeof data.giga != 'undefined')? data.giga : '');
						
					}
				}
			});
			return false;
		}

		function mccAutomatedForm1Step2Submit() {
			var formData = new FormData();
			formData.append("recID", $(this).find('input[id=recID]').val());
			formData.append("rdd_report", $(this).find('input[id=rdd_report]').val());
			formData.append("device_report", $(this).find('input[id=device_report]').val());
			formData.append("PhoneNumberTotalCost", $(this).find('input[id=PhoneNumberTotalCost]').val());
			formData.append("phone_number_count", $(this).find('input[id=phone_number_count]').val());
			formData.append("savings", $(this).find('input[id=savings]').val());
			formData.append("giga", $(this).find('input[id=usedData]').val());
			formData.append("action", 'mcc_automated_form_1_step_2');

			$(this).find('button[type=submit]').attr('disabled', true);
			$(this).find('button[type=submit]').find('.btn-element').addClass('mcc-auto-hide');
			$(this).find('button[type=submit]').find('.loader').removeClass('mcc-auto-hide');

			$.ajax({
				action:  		'mcc_automated_form_1_step_2',
				type:    		"POST",
				url:     		mccAutomatedObj.ajaxurl,
				data:    		formData,
				cache: 			false,
				processData: 	false,
				contentType: 	false,
				dataType: 		'json',
				success: function(data) {
					$(".mcc_automated_form_1_step_2").addClass('mcc-auto-hide');
					$('.mcc_automated_form_1_step_2_message').removeClass('mcc-auto-hide');
				}
			});
			return false;
		}

	});

})( jQuery );