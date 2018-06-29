jQuery(document).ready(function($)
{
	/*var firstCheck = $('[name="agree"]').prop("checked");
	$('[name="agree"]').on("change", function(){
		if($('[name="agree"]').prop("checked")){
			console.log("Флажок установлен");
		}
		else 
		{
 		 console.log("Флажок не установлен");
		}
	});*/


	/*Проверка документов*/
	var boxes = $('[name^="agree"]');

	
	$("input:checkbox").on("change", function()
	{
		
  		var theArray = new Array();
  		var count = 0;
  		for (var i=0;i<boxes.length;i++) 
  		{
  			
   			var box = boxes[i]; 
   			if ($(box).prop('checked')) 
   			{
   				count++;
      		}
		}

		//console.log(count);
		showDocuments(count);
  	
	});

	/*Выбор физ\юр лицо*/

	$('[name^="radio"]').on("change", function(evt){
		var valRadio = evt.target.value;

		

		if(valRadio==4){
			$('.forRadio4').css({"display":"flex", "flex-direction":"column", "justify-content":"flex-start"});
			$('[name="Profile[occupation]"]').val(4);
		}else{
			$('.forRadio4').css("display", "none");
		}
		if(valRadio==5){
			$('.forRadio5').css("display", "block");
			$('[name="Profile[occupation]"]').val(5);
			//$("div.forRadio4").remove()
		}else{
			$('.forRadio5').css("display", "none");
		}

	});
	console.log($('[name="getCheck"]').val());
	console.log($('[name="Profile[occupation]"]').val());

	/*Выбор condition*/

	var countCondition = $('[name^="сonditions"]');
	
	$('[name^="сonditions"]').on("change", function(){

		//console.log(countCondition);
		var count = 0;

		var countOfTags = countCondition.length;

		for (var i=0;i<countCondition.length;i++) 
  		{

  			console.log(countCondition.length);
  			

  			
   			var box = countCondition[i]; 
   			if ($(box).prop('checked')) 
   			{
   				count++;
      		}     		
      		
		}

		if(count != 0){

      			$('[name="5сonditions3"]').attr('disabled', true);
      			
      			
      			
      			$('#profile-name').attr('disabled', true);
      			$('#profile-last_name').attr('disabled', true);
      			$('#profile-middle_name').attr('disabled', true);
      			$('#btnImage').attr('disabled', true);
      			$('[name^="country"]').attr('disabled', true);
      			$('#profile-country').attr('disabled', true);
      			$('#profile-birthday').attr('disabled', true);
      			$('[name="id_number"]').attr('disabled', true);
      			$('#profile-city').attr('disabled', true);
      			$('#profile-address').attr('disabled', true);
      			$('#occupation').attr('disabled', true);
      			$('[name="commercial_relations"]').attr('disabled', true);
      			$('#profile-phone').attr('disabled', true);
      			$('[name="email"]').attr('disabled', true);
      			$('[name="occupation"]').attr('disabled', true);


      		}
      	else
      	{
      			$('[name="5сonditions3"]').attr('disabled', false);
      			$('#profile-name').attr('disabled', false);
      			$('#profile-last_name').attr('disabled', false);
      			$('#profile-middle_name').attr('disabled', false);
      			$('#btnImage').attr('disabled', false);
      			$('[name^="country"]').attr('disabled', false);
      			$('#profile-country').attr('disabled', false);
      			$('#profile-birthday').attr('disabled', false);
      			$('[name="id_number"]').attr('disabled', false);
      			$('#profile-city').attr('disabled', false);
      			$('#profile-address').attr('disabled', false);
      			$('#occupation').attr('disabled', false);
      			$('[name="commercial_relations"]').attr('disabled', false);
      			$('#profile-phone').attr('disabled', false);
      			$('[name="email"]').attr('disabled', false);
      			$('[name="occupation"]').attr('disabled', false);
      	}


	});



	/*Выбор condit для Legal*/

	var countCondit = $('[name^="dition"]');
	
	$('[name^="dition"]').on("change", function(){

		//console.log(countCondition);
		var count = 0;

		var countOfTags = countCondit.length;

		for (var i=0;i<countCondit.length;i++) 
  		{

  			console.log(countCondit.length);
  			

  			
   			var box = countCondit[i]; 
   			if ($(box).prop('checked')) 
   			{
   				count++;
      		}     		
      		
		}

		if(count != 0){

      			$('[name="5сonditions3"]').attr('disabled', true);
      			
      			
      			
      			$('#profile-name').attr('disabled', true);
      			$('#profile-last_name').attr('disabled', true);
      			$('#profile-middle_name').attr('disabled', true);
      			$('#btnImage').attr('disabled', true);
      			$('[name^="country"]').attr('disabled', true);
      			$('#profile-country').attr('disabled', true);
      			$('#profile-birthday').attr('disabled', true);
      			$('[name="id_number"]').attr('disabled', true);
      			$('#profile-city').attr('disabled', true);
      			$('#profile-address').attr('disabled', true);
      			$('#occupation').attr('disabled', true);
      			$('[name="commercial_relations"]').attr('disabled', true);
      			$('#profile-phone').attr('disabled', true);
      			$('[name="email"]').attr('disabled', true);
      			$('[name="occupation"]').attr('disabled', true);


      		}
      	else
      	{
      			$('[name="5сonditions3"]').attr('disabled', false);
      			$('#profile-name').attr('disabled', false);
      			$('#profile-last_name').attr('disabled', false);
      			$('#profile-middle_name').attr('disabled', false);
      			$('#btnImage').attr('disabled', false);
      			$('[name^="country"]').attr('disabled', false);
      			$('#profile-country').attr('disabled', false);
      			$('#profile-birthday').attr('disabled', false);
      			$('[name="id_number"]').attr('disabled', false);
      			$('#profile-city').attr('disabled', false);
      			$('#profile-address').attr('disabled', false);
      			$('#occupation').attr('disabled', false);
      			$('[name="commercial_relations"]').attr('disabled', false);
      			$('#profile-phone').attr('disabled', false);
      			$('[name="email"]').attr('disabled', false);
      			//$('[name="occupation"]').attr('disabled', false);
      	}


	});


	//attr("title", title);

	/*Выбор из 4 стран*/

	var showSpan = $('[data-id]');
	var needleSpan = 0;

	$('[name^="country"]').on("change", function(evt){

		var valRadio = evt.target.value;
		needleSpan = valRadio;


		$('#profile-country').val(valRadio);
		
		//$('[name="Profile[country]"]').val("Canada");

		for (var i=0;i<showSpan.length;i++) 
  		{
  			
   			var box = showSpan[i]; 
   			if (box.dataset.id==valRadio) 
   			{
   				$(box).css("display", "block");
      		}else{
      			$(box).css("display", "none");
      		}
		}
		

		//console.log(valRadio);


	});

	var countryValue = $( "#profile-country2 option:selected" ).text();
	//console.log(countryValue);

	/*Проверка выбора страны*/

	$('#profile-country').on("change", function(){

		var selectedVal = $(this).find(":selected").val();

		console.log(selectedVal);

		/*
		if(selectedVal==168 || selectedVal==83 || selectedVal==160 || selectedVal==208)

		{

		}*/
			$('[name^="country"]').prop("checked", false);

			$('[data-id ='+ needleSpan +']').css("display", "none");
			$('[data-id = 20]').css("display", "block");
			$('[data-id = 21]').css("display", "block");
		

		
		console.log($(this).find(":selected").val());
	});

	/*Legal person*/

		$('[data-id=55]').on("change", function(){
		
		var selectedVal = $(this).find(":selected").val();

		/*
		if(selectedVal==168 || selectedVal==83 || selectedVal==160 || selectedVal==208)

		{

		}*/
			$('[name^="country"]').prop("checked", false);

			$('[data-id ='+ needleSpan +']').css("display", "none");
			$('[data-id = 20]').css("display", "block");
			$('[data-id = 21]').css("display", "block");
		

		
		console.log($(this).find(":selected").val());
	});


	/*Ajax агрузка фото*/

	$('input[type=file]').change(function(){
    	files = this.files;
	});

	 $('#forPhoto').submit(function(e){
        var formData = new FormData($(this)[0]);        
        $.ajax({
            type: 'post',
            url:'/site/testmassage',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                 $('#btnImage').text('Success');
            }
        })  
        e.preventDefault();
         
        return false;
    });


	 /*Ajax агрузка фото Legal*/

	$('input[type=file]').change(function(){
    	files = this.files;
	});

	 $('#forPhoto2').submit(function(e){
        var formData = new FormData($(this)[0]);        
        $.ajax({
            type: 'post',
            url:'/site/testmassage',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                 
                 $('#btnImage2').text('Success');
            }
        })  
        e.preventDefault();
         
        return false;
    });

	 /*Добавление новых полей*/
	 var m = 0;


	 $('#addFields').on('click', function(e){

	 	 e.preventDefault();

	 	m++;

	 	//var telnum = parseInt($("#needleFileds").find("div.add:last").attr("id").slice(3)) + 1;
	 	$("div#needleFileds").append('<div id="needleFileds"><span>Volume of authority</span><input type="text" class="form-control" name="vol_auth' + m + '" value="Volume of authority"><div class="form-group field-profile-birthday required"><label class="control-label" for="profile-birthday">The date the authority</label><input type="text" id="profile-birthday" class="form-control" name="prof' + m + '" value="28.10.1995" data-plugin-inputmask="inputmask_88395699"><p class="help-block help-block-error"></p></div><div id="add1" class="add"><span>Reason the authority was provided</span><input type="text" class="form-control" name="reason' + m + '" value="reason"></div><br><br></div>');

	 });

	 /*Action на чекбокс sale2*/

	 $('[name="sale2"]').on("change", function(){

	 	$('input[value="4"]').prop('checked', true);
	 	$('.forRadio5').css("display", "none");
	 	$('.forRadio4').css({"display":"flex", "flex-direction":"column", "justify-content":"flex-start"});
	 	$('[name="sale2"]').prop('checked', false);

	 	swal({
                title: '25% or more participation in a legal entity',
                confirmButtonColor: '#4fa7f3',
                text: "Natural person",
                type: 'warning'
            });
	 	
	 	
	 });

	 /*Ocupation final*/

	 
	

	 var checkOr = $('[name="getCheck"]').val();
	 $('[data-send="finalSend"]').attr('disabled', true);

	 if(checkOr == 5){
	 	$("[name^='agree']").prop("checked", true);
	 	$('.forCheckbox').css("display", "flex"); 
	 	$('input[value="5"]').prop("checked", true);
	 	$('.forRadio4').css("display", "none");
	 	$('.forRadio5').css({"display":"flex", "flex-direction":"column", "justify-content":"flex-start"});
	 	$("[name='5сondit3']").prop("checked", true);
	 	$("[name='sale']").prop("checked", true);
	 	$("[name='occupation']").prop("checked", true);
	 	$('[data-send="finalSend"]').attr('disabled', false);

	 	$('.forHiddenContent').css("display", "block");
	 	//$('#divForImage').css("display", "none");	 	 	
	 }

	  if(checkOr == 4){
	 	$("[name^='agree']").prop("checked", true);
	 	$('.forCheckbox').css("display", "flex"); 
	 	$('input[value="4"]').prop("checked", true);
	 	$('.forRadio5').css("display", "none");
	 	$('.forRadio4').css({"display":"flex", "flex-direction":"column", "justify-content":"flex-start"});
	 	$("[name='5сonditions3']").prop("checked", true);
	 	//$("[name='sale']").prop("checked", true);
	 	$("[name='occupation']").prop("checked", true);
	 	$('[data-send="finalSend"]').attr('disabled', false);

	 	$('.forHiddenContent').css("display", "block");
	 	//$('#divForImage').css("display", "none");
	 	
	 }

	 var myFile = $("form input[type=file]").val();

	
	 	if(myFile == ""){
	 		$('[data-send="finalSend"]').attr('disabled', true);
	 		$('[name="occupation"]').attr('disabled', true);	 		
	 	}
	 	$("input[type=file]").on("change", function(){
	 		myFile = $("form input[type=file]").val();
	 		console.log(myFile);

	 		if(myFile != ""){
	 		$('[data-send="finalSend"]').attr('disabled', false);
	 		$('[name="occupation"]').attr('disabled', false);	 		
	 		}
	 	});

	 	

	 	/*

	 	$("input[type='file']").on("change", function(){
	 		if(myFile!=""){
	 			$('[name="ocupation"]').attr("disabled", false);
	 		}
	 	});

	 	*/

	 

	 
		
		
		

	  $('[name="occupation"]').on("change", function(){



	  		if($(this).prop("checked")){
	  			 $('[data-send="finalSend"]').attr('disabled', false);
	  		}else{
	  			$('[data-send="finalSend"]').attr('disabled', true);
	  		}
	  

	  });

	  
	  console.log(checkOr);

	  




});

function showDocuments(count){
	if(count==5){
		$('.forCheckbox').css("display", "flex");
		$('.forHiddenContent').css("display", "block");
	}else{
		$('.forCheckbox').css("display", "none");
		$('.forHiddenContent').css("display", "none");
	}
}

function resetCount(prop){
	if(prop >= 6)
	{
		prop = 0;
		return prop;
	}
	else
	{
		return prop;
	}
}





