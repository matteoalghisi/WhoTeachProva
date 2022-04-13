//funzione che chiama RS


function getRecommendation(id, keys){

	//disabilito il bottone per evitare pressioni ripetute
	document.getElementById("id_submit_recommend").disabled = true;
	//lo riabilito dopo 3 secondi per riprovare in caso di problemi
	setTimeout(function(){document.getElementById("id_submit_recommend").disabled = false},3000);
	
	var json = {"type":"recommend", "userid":id, "keywords":keys};	
   
//old (chiamata diretta a Cloud Function)

	$.ajax({
		//url:'http://localhost:7071/api/HttpExample',
		url:'https://socialthings-rs-ml.westeurope.cloudapp.azure.com:5001/api/rs',
            
		
		//url:'//socialthings-rs.azurewebsites.net/api/rs',

		type:'POST',
		dataType: 'json',
		data: JSON.stringify(json),
		//contentType: "application/json; charset=utf-8",
		success: showRecommendation,
		failure: function() {
			console.error('errore di chiamata al RS');
		}
	});
	

/*
//chiamata a rs_connection, per ottenere le raccomandazioni dalla Cloud Function
	$.ajax({
		url:'../mod/recommend/rs_connection.php',
		type:'POST',
		dataType: 'json',
		data: JSON.stringify(json),
		//contentType: "application/json; charset=utf-8",
		success: showRecommendation,
		failure: function() {
			console.error('errore di chiamata al RS');
		}
	});
*/

}

//var resources;

//questa funzione mostra le risorse suggerite dal recommend attraverso la chiamata AJAX precedente
function showRecommendation(data){
	
	var resid = [];
	Object.keys(data.response).forEach(function(key) {
		resid.push(key);
		//console.log(resid);
	});
	
	$.ajax({
    type: "POST",
    url: '../mod/recommend/getRecommendation.php',
    dataType: 'json',
	//richiamo la funzione getRS contenuta in un file php passandogli le risorsse
	//ritorna il nome delle risorse e il loro link per anteprima
    data: {functionname: 'getRS', arguments: resid},

    success: function (result) {
            resources = result;
			console.log(resources);
			if (result.hasOwnProperty('error'))
				alert(result.error);
			else
				showResources(resources);
		},
	failure: function() {
			console.error('errore funzione showReccomendation()');
		}
	});
		
	

}

//aggiunge il div contente le risorse da visualizzare
function showResources(resources){
	$( ".mform" ).css("display","none");
	//$( "#region-main" ).append( "<div></div>" );
	$.get("../mod/recommend/resources_list.html", function(data){
		$("#region-main").append(data);
		add_resources_list(resources);
		
		//inserimento script che ernao in resources_list.html
		//servono per far funzionare le box multiselect
		$(document).ready(function(){
		$('#res_list').multiSelect();

		$("#flip").click(function(){
			$("#hideMe").slideToggle("slow");
			$('.arrow-img').toggleClass('slideUp');
			$('.arrow-img').toggleClass('slideDown');
		});

		var res_to_info = new Array();
		$('#callbacks').multiSelect({
			afterSelect: function (values) {
				var arr = values + "";
				arr = arr.split(";");
				var id = arr[0];
			},
		});
		
		
		/*  ================ FORM ======================= */
		
		
		
		$('#myformEnd').on('submit',function(e){
				e.preventDefault();
				//metto spinner di caricamento
				$("#submit_button").hide();
				$("#back_recomm_button").hide();
				$("#form_footer").append("<img src='../mod/recommend/spinner/ajax-loader.gif'></img>");

				var myObject = {};
				var rsinput = [];
				
				$('.rs[selected="selected"]').each(function () {
					
					rsinput.push({"my-select": $(this).val()});
				   
				});
				
				myObject['listaCampi'] = rsinput;
				myObject['id_course'] = course_id;
				myObject['id_section'] = section_id;

				$.ajax({
				 type     : "POST",
				 cache    : false,
				 url      : $(this).attr('action'),
				 data     : JSON.stringify(myObject),
				 contentType: "application/json; charset=utf-8",
				 success  : function(data) {
					window.onbeforeunload = null;
					window.location = "../course/view.php?id="+course_id;
				 }
				});
			});
		});
	});
}
//aggiunge le risorse alla lista dentro al div
function add_resources_list(resources){
	resources.forEach(function(r){ 
		//$("#res_list").append('<li style="padding-bottom: 10px;"><input type="checkbox" id='+r.id+' name="my-select" value='+r.module+'-'+r.id+'><label class="tab" onclick="openPreview(`'+r.url+'`)">'+r.name+'</span></li>');
		$("#res_list").append('<option class="rs" name="my-select" value="'+r.module+'-'+r.id+'" onmouseover="Tip(TooltipTxt(`'+r.url+'`,`'+r.name+'`),CLOSEBTN,\'true\',STICKY,\'true\',FONTSIZE,\'11.5pt\',BGCOLOR,\'#d1e0e7\',BORDERCOLOR,\'#5392b3\',CLOSEBTNCOLORS,\'#74A9CC\',DELAY,\'300\',DURATION,\'2000\',FOLLOWMOUSE,\'false\',EXCLUSIVE,\'true\',TITLE,`'+r.name+'`)" onmouseout="UnTip()" value="'+r.module+'-'+r.id+'">'+r.name+'</option>')
	});
} 
	//<option onmouseover="Tip('<a href=\'javascript:void(0)\' onclick=\'openPreview(`<?php print $url; ?>`,`<?php print $name; ?>`); return false; \'><?php print convert_RS('Resource Preview'); ?> </a>',CLOSEBTN,'true',STICKY,'true',FONTSIZE,'11.5pt',BGCOLOR,'#d1e0e7',
		//		BORDERCOLOR,'#5392b3',CLOSEBTNCOLORS,'#74A9CC',DELAY,'300',DURATION,'2000',FOLLOWMOUSE,'false',EXCLUSIVE,'true',TITLE,'<?php print $name; ?>')" onmouseout="UnTip()" value="<?php print $id_risorsa; ?>;<?php print $name_orig; ?>;<?php print $module; ?>;" id="<?php print $id_risorsa; ?>"><?php print $name; ?>
	//</option>


function openPreview(url, name) {
	window.open(url, "Preview: " + name, "width=500,height=500");
}

function TooltipTxt(url, name){
    return "<a href='javascript:void(0)' onclick='openPreview(`"+url+"`,`"+name+"`); return false;'>Anteprima Risorsa</a>";
}

// jQuery script library loading

$(window).on('load', function(){
	//$.getScript('https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', function(	
	
	$.getScript('https://code.jquery.com/jquery-3.5.1.min.js', function()
	{
		$.getScript('../mod/recommend/boxselect/js/jquery.multi-select.js', function()
		{
			var tag = document.createElement("script");
			tag.src = "../mod/recommend/boxselect/js/wz_tooltip.js";
			document.body.insertBefore(tag, document.body.firstChild);
			console.log("loaded javascript");
		});
	});
	
});







