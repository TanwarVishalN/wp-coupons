jQuery(function($){
	$('#coupon-expiry_date').datepicker({
	    dateFormat: 'yy-mm-dd',
	});

	$('#coupon-amount').on('keypress', function(e){
	    if( ! allowDecimalNumber(e) ) {
	    	return false; 
	    }
	    return true;
	});
	
	$('#coupon-code').on('keyup', function(){
    	var uppercaseStr = $(this).val().toUpperCase();
	    $(this).val(uppercaseStr)
	});
});

allowNumber = function(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
allowDecimalNumber = function(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46 ) {
        return false;
    }
    return true;
}