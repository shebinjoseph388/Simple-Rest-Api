
window.addEventListener('load', function () {
	var button = document.getElementById('newPost');

button.addEventListener("click", function(){
	newPost();
});

function newPost(){
	var title = document.getElementById('title').value;
	var content = document.getElementById('content').value;

	var postmessage = '{"title":"'+title +'","content":"'+content +'"}';
	postData(postmessage), "json" }

function postData(postmessage){
	$.ajax({
		url: 'api/postmessage/',
		type: 'post',
		data: postmessage,
		contentType: 'application/json; charset=utf-8',

		success: function (data){
			$.get("api/postmessage/", function(data){
				document.getElementById('title').value = '';
				document.getElementById('content').value = '';
				updateTable(data);
			}, "json" );
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
