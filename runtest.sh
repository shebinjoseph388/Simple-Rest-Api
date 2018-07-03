#!/bin/bash

url="http://ec2-34-228-26-63.compute-1.amazonaws.com/api/postmessage/"

clear

echo -e "\n\n*** getalllists ***"
curl $url"getall"

echo -e "\n\n***postmessage per Id ***"
curl $url"id=2"

echo -e "\n\n***post new message ***"
curl -X POST $url -d '{"title":"testmessagetitle","testmessagecontent"}' -H 'Content-Type: application/json'

echo -e "\n\n*** deleting post message ***"
curl -X DELETE $url"delete/id=2"

exit 0