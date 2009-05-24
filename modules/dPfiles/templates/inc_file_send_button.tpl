<!-- Send File -->
{{if $dPconfig.dPfiles.system_sender}}

{{if $_doc_item->_send_problem}}
<button class="send-problem {{$notext}}" type="button" 
	onclick="alert('{{tr escape=JSAttribute}}CDocumentSender-alert_problem{{/tr}}' 
		+ '\n\t- ' + '{{$_doc_item->_send_problem|smarty:nodefaults|JSAttribute}}' );">
  {{tr}}Send{{/tr}}
</button>

{{else}}
<script type="text/javascript">
submitSendAjax = function(button, confirm_auto, onComplte) {
	if (confirm_auto) {
	  if (!confirm('{{tr escape=JSAttribute}}CDocumentSender-confirm_auto{{/tr}}')) {
	  	return;
	  };
	}
	$V(button.form._send, true);
	
	return onSubmitFormAjax(button.form, { 
		onComplete : onComplte
	} );  	  
}
</script>

<input type="hidden" name="_send" value="" />
{{if $_doc_item->etat_envoi == "oui"}}
  <button class="send-cancel {{$notext}}" type="button" onclick="submitSendAjax(this, false, function () { {{$onComplete}} } )">
    {{tr}}Send{{/tr}}
  </button>
{{elseif $_doc_item->etat_envoi == "obsolete"}}  
  <button class="send-again {{$notext}}" type="button" onclick="submitSendAjax(this, false, function () { {{$onComplete}} } )">
    {{tr}}Send{{/tr}}
  </button>
{{else}}
  {{if $_doc_item->_ref_category->send_auto}}
  <button class="send-auto {{$notext}}" type="button" onclick="submitSendAjax(this, true, function () { {{$onComplete}} } )">
    {{tr}}Send{{/tr}}
  </button>
  {{else}}
  <button class="send {{$notext}}" type="button" onclick="submitSendAjax(this, false, function () { {{$onComplete}} } )">
     {{tr}}Send{{/tr}}
  </button>
  {{/if}}
{{/if}}

{{/if}}

{{/if}}