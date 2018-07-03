# Simple-Rest-Api
ARCHITECTURE DIAGRAM


RESOURCE METHODS
GET
There is two types of service offered by GET
GET based on id: It returns “postmessage” based on id.
GET based on all List: It returns all the available “postmessage”.
Example of a request
GET api/postmessage/getall
ACCEPT: application/json
$ curl http://ec2-34-228-26-63.compute-1.amazonaws.com/api/postmessage/getall
Response:
[{"id":2,"title":"message1","content":"content1","addtime":1489825793,"user_id":1},{"id":4,"title":"message3","content":"content3","addtime":1489825793,"user_id":1},{"id":11,"title":"sadsadasd","content":"sadsadsad","addtime":null,"user_id":1},{"id":12,"title":"dsfdsfs","content":"sadfdsafdfdsfdsf","addtime":null,"user_id":1},{"id":17,"title":"test","content":"test","addtime":null,"user_id":1},{"id":19,"title":"madam","content":"sadasdsad","addtime":null,"user_id":1}

POST
Post creates a new entry, i.e. “postmessage”.
Example of a request
POST api/postmessage/
RawInput : {"title":"test","content":"test"}
Response:
{"postId":"25","title":"test","content":"test","addtime":null,"userId":"1"}

DELETE
Delete lets you delete the “postmessage” based on the given id.
Example of a request
DELETE: /api/postmessage/delete/2
ACCEPT: application/json
Response:
[{"id":2,"title":"message1","content":"content1","addtime":1489825793,"user_id":1,"palindrom":0}]

USECASE DIAGRAM






UI INTERFACE











