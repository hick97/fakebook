( function ( $ ) {
    "use strict";

	$(function() {	
		slideToggleUsers();
	});

	function slideToggleUsers(){
		$('.users-system h4 i:last-child').on("click", function(){
			$(this).toggleClass('flip');			
			$('#toggle-user').slideToggle();
		});
	}

})(jQuery);



