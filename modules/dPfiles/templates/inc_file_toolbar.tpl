{{**
  * Show a file/document edit/delete/move toolbar
  *}}

{{if $canFile->edit && !$accordDossier}}

 {{if $curr_file->_class_name== "CCompteRendu"}}
 	<!-- Modification -->
   <button class="edit" type="button" onclick="Document.edit({{$elementId}})">
     {{tr}}Edit{{/tr}}
   </button>
 {{/if}}

	<!-- Deletion -->
 {{if $curr_file->_class_name=="CCompteRendu"}}
   <form name="editDoc{{$curr_file->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
   <input type="hidden" name="m" value="dPcompteRendu" />
   <input type="hidden" name="dosql" value="do_modele_aed" />
   <input type="hidden" name="_id" value="{{$curr_file->_id}}" />
   <input type="hidden" name="del" value="0" />
   {{assign var="confirmDeleteType" value="le document"}}
   {{assign var="confirmDeleteName" value=$curr_file->nom}}
 {{/if}}
   
 {{if $curr_file->_class_name=="CFile"}}
   <form name="editFile{{$curr_file->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
   <input type="hidden" name="m" value="dPfiles" />
   <input type="hidden" name="dosql" value="do_file_aed" />
   <input type="hidden" name="_id" value="{{$curr_file->_id}}" />
   <input type="hidden" name="del" value="0" />
   {{assign var="confirmDeleteType" value="le fichier"}}
   {{assign var="confirmDeleteName" value=$curr_file->file_name}}
 {{/if}}
 
	<!-- Deletion -->
  <button type="button" class="trash" onclick="file_deleted={{$elementId}};confirmDeletion(
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
   
	<!-- Move -->
	<button type="button" class="hslip" onclick="this.form.file_category_id.show()">
	  {{tr}}Move{{/tr}}
	</button>
  <select style="display:none" name="file_category_id" onchange="submitFileChangt(this.form)">
    <option value="" {{if !$curr_file->file_category_id}}selected="selected"{{/if}}>Aucune catégorie</option>
    {{foreach from=$listCategory item=curr_cat}}
    <option value="{{$curr_cat->file_category_id}}" {{if $curr_cat->file_category_id == $curr_file->file_category_id}}selected="selected"{{/if}} >
      {{$curr_cat->nom}}
    </option>
    {{/foreach}}
  </select>
 </form>

{{/if}}
