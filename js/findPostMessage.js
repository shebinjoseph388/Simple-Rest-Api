
window.addEventListener('load', function () {
	var button = document.getElementById('getPosts');

button.addEventListener("click", function(){
	findPostMessage();
});

function findPostMessage(){
	$.ajax({
		url: 'api/postmessage/message/' +document.getElementById('find_title').value,
		type: 'get',
		contentType: 'application/json; charset=utf-8',
		success: function (data){
			document.getElementById('find_title').value = '';
			updateTable(data);
		}, error: function(data) {
			updateTable(data);
		}
	});
}

function updateTable(data){
	$('#postmessage td').parent().remove();
	var pal = "<b style='color:red'>Its not a Palindrom</b>";

	for(i=0; i<data.length; i++){
		if (data[i].palindrom) {
			pal = "<b style='color:red'>Its a Palindrom</b>";
		}
		$('#postmessage').append("<tr><td>" +data[i].id +"</td> <td>"+data[i].title + "(" + pal + ")" +" </td><td>" +data[i].content +"</td>"  +"</tr>");
	}
}
}, false);
