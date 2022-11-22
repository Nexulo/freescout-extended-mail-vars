/**
 * Module's JavaScript.
 */

function maisenextendetmailvarInit()
{
	$(document).ready(function(){
		
		$('<option value="{%conversation.firstmessage%}">Initial Customer Message</option>').insertAfter($(".summernote-inservar").find("option[value='{%conversation.number%}']"));

	});
}