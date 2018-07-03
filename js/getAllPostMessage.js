
window.addEventListener('load', function () {
	var button = document.getElementById('getAll');

button.addEventListener("click", function(){
	console.log('clicked');
	requestPostMessages();
});

document.addEventListener("DOMContentLoaded", function(){
	requestPostMessages();
});


function requestPostMessages(){
	$.ajax({
		url: 'api/postmessage/getall',
		type: 'get',
		contentType: 'application/json; charset=utf-8',
		success: function (data){
			console.log(data[0]);
			updateTable(data);
		}
	});
}

function updateTable(data){
	$('#postmessage td').parent().remove();

	for(i=0; i<data.length; i++){
		$('#postmessage').append("<tr><td>" +data[i].id +"</td> <td>"+data[i].title +" </td><td>" +data[i].content +"</td>"  +"</tr>");
	}
}
}, false);
