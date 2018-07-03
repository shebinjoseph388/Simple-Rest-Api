<?php
/**
 * Possible Error Codes
 */
class ErrorCode{
	const USERNAME_EXISTS = 1;
	const PASSWORD_CANNOT_EMPTY = 2;
	const USERNAME_CANNOT_EMPTY = 3;
	const REGISTER_FAIL = 4;                
	const USERNAME_OR_PASSWORD_INVALID = 5;

	const POST_TITLE_CANNOT_EMPTY = 6;
	const POST_CONTENT_CANNOT_EMPTY = 7;
	const POST_CREATE_FAIL = 8;   
	const POST_ID_CANNOT_EMPTY = 9;
	const POST_NOT_FOUND = 10;
	const PERMISSION_DENIED = 11;
	const POST_EDIT_FAIL = 12;
	const POST_DELETE_FAIL = 13;       
	const PAGE_SIZE_TO_BIG = 14;
	
	const SERVER_INTERNAL_ERROR = 15;    
	const DATABASE_ERROR = 16;       
}