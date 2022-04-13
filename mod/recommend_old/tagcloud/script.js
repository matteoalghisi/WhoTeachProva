
$(window).load(function() {

	console.log("aiutooooooooo");
	//$("#id_tagcloud").parent().children("span").children("a").addClass("keyword")

	$(".keyword").click(function(){ 
		console.log("ok");
		var keyName = $(this).text(); 
		var formId = $("#id_keywords");
		var formValue = formId.val()

		if(formValue) {
			if (formValue.indexOf(keyName)) {
				formId.val(formValue + "," + keyName); 
			}
		} else {
			formId.val(keyName);
		}  
	});
    
});