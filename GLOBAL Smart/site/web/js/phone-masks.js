jQuery(document).ready(function($)
{
	updateMask();

   	$("#profile-country").on("change", function()
   	{
   		updateMask();
   	});
});

function updateMask()
{
	var elem = $("#profile-country"),
		mask = elem.find(":selected").attr("data-mask");

	if(mask)
	{
		$("#profile-phone").inputmask(mask);
	}
	else
	{
		$("#profile-phone").val("");
	}
}