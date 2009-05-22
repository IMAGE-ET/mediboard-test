{{**
  * Show a file/document edit/delete/move/send toolbar
  *}}

{{if $canFile->edit && !$accordDossier}}

 {{if $_doc_item->_class_name== "CCompteRendu"}}
 	<!-- Modification -->
   <button class="edit {{$notext}}" type="button" onclick="Document.edit({{$elementId}})">
     {{tr}}Edit{{/tr}}
   </button>
 {{/if}}

	<!-- Deletion -->
 {{if $_doc_item->_class_name=="CCompteRendu"}}
   <form name="editDoc{{$_doc_item->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
   <input type="hidden" name="m" value="dPcompteRendu" />
   <input type="hidden" name="dosql" value="do_modele_aed" />
   <input type="hidden" name="compte_rendu_id" value="{{$_doc_item->_id}}" />
   <input type="hidden" name="del" value="0" />
   {{assign var="confirmDeleteType" value="le document"}}
   {{assign var="confirmDeleteName" value=$_doc_item->nom}}
 {{/if}}
   
 {{if $_doc_item->_class_name=="CFile"}}
   <form name="editFile{{$_doc_item->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
   <input type="hidden" name="m" value="dPfiles" />
   <input type="hidden" name="dosql" value="do_file_aed" />
   <input type="hidden" name="file_id" value="{{$_doc_item->_id}}" />
   <input type="hidden" name="del" value="0" />
   {{assign var="confirmDeleteType" value="le fichier"}}
   {{assign var="confirmDeleteName" value=$_doc_item->file_name}}
 {{/if}}
 
	<!-- Deletion -->
  <button type="button" class="trash  {{$notext}}" onclick="file_deleted={{$elementId}};confirmDeletion(
    this.form, {
      typeName:'{{$confirmDeleteType}}',
      objName:'{{$confirmDeleteName|smarty:nodefaults|JSAttribute}}',
      ajax:1,
      target:'systemMsg'
    },{
      onComplete:reloadAfterDeleteFile
    } );">
    {{tr}}Delete{{/tr}}
  </button>
  
  <!-- Send File -->
  {{if $dPconfig.dPfiles.system_sender}}
    {{if $_doc_item->_send_problem}}
	    <button class="send-problem {{$notext}}" type="button" 
	    	onclick="alert('L\'envoi de ce fichier n\'est pas possible pour le raison suivante : \n\t- ' + '{{$_doc_item->_send_problem|smarty:nodefaults|JSAttribute}}' );">
	      {{tr}}Send{{/tr}}
	    </button>
	    <div class="big-info" id="SendProblem-{{$_doc_item->_guid}}" style="display: none">
	      L'envoi de ce fichier n'est pas possible pour le raison suivante :
	      <ul>
	      	<li>{{$_doc_item->_send_problem}}</li>
	      </ul>
	    </div>
    {{else}}
  	<script type="text/javascript">
  	onSubmitSendAjax = function(button) {
			$V(button.form._send, true);
			return onSubmitFormAjax(button.form, { 
				onComplete : function () { 
					Document.refreshList('{{$_doc_item->_class_name}}','{{$_doc_item->_id}}'); 
				} 
			} );  	  
  	}
  	</script>

    <input type="hidden" name="_send" value="" />
    {{if $_doc_item->etat_envoi == "oui"}}
      <button class="send-cancel {{$notext}}" type="button" onclick="onSubmitSendAjax(this)">
        {{tr}}Send{{/tr}}
      </button>
    {{elseif $_doc_item->etat_envoi == "obsolete"}}  
      <button class="send-again {{$notext}}" type="button" onclick="onSubmitSendAjax(this)">
        {{tr}}Send{{/tr}}
      </button>
    {{else}}
      <button class="send {{$notext}}" type="button" onclick="onSubmitSendAjax(this)">
         {{tr}}Send{{/tr}}
      </button>
    {{/if}}

  	{{/if}}
  {{/if}}
  
	<!-- Move -->
	<button type="button" class="hslip  {{$notext}}" onclick="this.form.file_category_id.show()">
	  {{tr}}Move{{/tr}}
	</button>
  <select style="display: none; width: 90px;" name="file_category_id" onchange="submitFileChangt(this.form)">
    <option value="" {{if !$_doc_item->file_category_id}}selected="selected"{{/if}}>&mdash; Aucune catégorie</option>
    {{foreach from=$listCategory item=curr_cat}}
    <option value="{{$curr_cat->file_category_id}}" {{if $curr_cat->file_category_id == $_doc_item->file_category_id}}selected="selected"{{/if}} >
      {{$curr_cat->nom}}
    </option>
    {{/foreach}}
  </select>
    
 </form>
 {{/if}}