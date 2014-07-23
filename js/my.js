
$(document).ready(function(){
	
try {
	logClient();
	$(".catToggle").click(function () {
		alert("here");
		$("#categories").hide("slide",{ percent: 90 });
	});
} catch (err) {
	//alert(err);
}
	
});

function logClient() {
	var ref = $.cookie("client-id");
	if (isNaN(ref))
		ref = "";
	var url = "app/logClient.php?action=doit&ref="+ref;
	ref = serverGet(url);	
	$.cookie("client-id", ref, { expires: 1 });
}
