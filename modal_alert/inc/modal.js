var $ = jQuery.noConflict();

$(document).ready(function(){
	
	// Show / Hide select box on page load
	for(var i = 0; i < $(".openSelect").length; i++) {
		var selectElement = "."+$(".openSelect").eq(i).attr("id")
		
		if( $(".openSelect").eq(i).is(":checked") ) {
			$(selectElement).show();
		}

	}
	
	// Open modal on page load
	$("#openModal").modal({
		escapeClose: false,
		clickClose: false,
		fadeDuration: 500,
		fadeDelay: 1.2
	});
	
	// Show / Hide select box on checkbox click
	$(document).on("click", ".openSelect", function(){
		var selectElement = "."+$(this).attr("id");
		$(selectElement).slideToggle();
		if( $(this).is(":checked") === false ) {
			$(selectElement + " option:selected").removeAttr("selected");
		}

	})
	
});
