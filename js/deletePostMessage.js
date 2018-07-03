
window.addEventListener('load', function () {
	var button = document.getElementById('deletePost');

button.addEventListener("click", function(){
	deletePost();
});

function deletePost(){
	var id = document.getElementById('post_id').value;
	// var title = document.getElementById('delete_title').value;

	var postmessage = '{"id":"'+id +'"}';
	deleteData(postmessage, id), "json";
}

function deleteData(postmessage, id){
	$.ajax({
		url: 'api/postmessage/delete/'+ id,
		type: 'delete',
		data: postmessage,
		contentType: 'application/json; charset=utf-8',

		success: function (data){
			$.get("api/postmessage/", function(data){
				document.getElementById('post_id').value = '';
				// document.getElementById('delete_title').value = '';
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
