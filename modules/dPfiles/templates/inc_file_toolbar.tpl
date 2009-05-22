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
  {{assign var=doc_class value=$_doc_item->_class_name}}
  {{assign var=doc_id    value=$_doc_item->_id        }}
	{{mb_include template=inc_file_send_button onComplete="Document.refreshList('$doc_class','$doc_id')"}}
  
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