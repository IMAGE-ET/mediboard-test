{{**
  * Show a file/document edit/delete/move/send toolbar
  *}}

{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}

{{if !$accordDossier}}

  {{if $_doc_item->_can->read}}
    {{if $_doc_item->_class=="CCompteRendu"}}
      <!-- Modification -->
       <button class="edit {{$notext}}" type="button" onclick="Document.edit({{$elementId}})">
         {{tr}}Edit{{/tr}}
       </button>
    {{/if}}
    {{if $_doc_item->file_type == "image/fabricjs" && $_doc_item->_class=="CFile"}}
      <button class="edit {{$notext}}" type="button" onclick="editDrawing({{$elementId}}, null, null, reloadAfterUploadFile);">
        {{tr}}Edit{{/tr}}
      </button>
    {{/if}}

    <!-- Téléchargement du fichier -->
    {{if $_doc_item->_class=="CFile" && $_doc_item->file_type != "image/fabricjs"}}
      <a class="button download notext"
        href="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$_doc_item->_id}}"
        target="_blank" title="{{tr}}CFile.download{{/tr}}"></a>
    {{/if}}

    <!-- Impression -->
    {{if $_doc_item->_class=="CCompteRendu"}}
      <button type="button" class="print notext"
        onclick="
        {{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
          Document.printPDF('{{$_doc_item->_id}}', '{{$_doc_item->factory}}');
        {{else}}
          Document.print('{{$_doc_item->_id}}');
        {{/if}}">
        {{tr}}Print{{/tr}}
      </button>
    {{/if}}
  {{/if}}

  {{if $_doc_item->_can->edit}}
    <!-- Deletion -->
    {{if $_doc_item->_class=="CCompteRendu"}}
      <form name="editDoc{{$_doc_item->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcompteRendu" />
      <input type="hidden" name="dosql" value="do_modele_aed" />
      <input type="hidden" name="compte_rendu_id" value="{{$_doc_item->_id}}" />
      <input type="hidden" name="del" value="0" />
      {{assign var="confirmDeleteType" value="le document"}}
      {{assign var="confirmDeleteName" value=$_doc_item->nom|smarty:nodefaults|JSAttribute}}
    {{/if}}

    {{if $_doc_item->_class=="CFile"}}
      <form name="editFile{{$_doc_item->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPfiles" />
      <input type="hidden" name="dosql" value="do_file_aed" />
      <input type="hidden" name="file_id" value="{{$_doc_item->_id}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="annule" value="0" />
      {{assign var="confirmDeleteType" value="le fichier"}}
      {{assign var="confirmDeleteName" value=$_doc_item->file_name|smarty:nodefaults|JSAttribute}}
    {{/if}}

    {{if $can->admin || ($_doc_item instanceof CCompteRendu && !$_doc_item->_is_locked)}}
      <!-- Deletion -->
      <button type="button" class="trash  {{$notext}}" onclick="file_deleted={{$elementId}};confirmDeletion(
        this.form, {
        typeName:'{{$confirmDeleteType}}',
        objName:'{{$confirmDeleteName}}',
        ajax:1,
        target:'systemMsg'
        },{
        onComplete:reloadAfterDeleteFile.curry('{{$_doc_item->file_category_id}}')
        } );">
        {{tr}}Delete{{/tr}}
      </button>
    {{/if}}
    {{if $_doc_item instanceof CFile && $_doc_item->annule == "0"}}
      <button type="button" class="cancel notext" onclick="cancelFile(this.form, '{{$_doc_item->file_category_id}}')">{{tr}}Annuler{{/tr}}</button>
    {{/if}}

    <!-- Send File -->
    {{assign var=doc_class   value=$_doc_item->_class}}
    {{assign var=doc_id      value=$_doc_item->_id   }}
    {{assign var=category_id value=$_doc_item->file_category_id}}
    {{mb_include template=inc_file_send_button onComplete="Document.refreshList('$category_id')"}}

    <!-- Move -->
    <button type="button" class="hslip  {{$notext}}" onclick="this.form.file_category_id.setVisibility(true)">
      {{tr}}Move{{/tr}}
    </button>
    <br />
    <select style="visibility: hidden; width: 12em;" name="file_category_id" onchange="submitFileChangt(this.form)">
      <option value="" {{if !$_doc_item->file_category_id}}selected="selected"{{/if}}>&mdash; Aucune catégorie</option>
      {{foreach from=$listCategory item=curr_cat}}
        <option value="{{$curr_cat->file_category_id}}" {{if $curr_cat->file_category_id == $_doc_item->file_category_id}}selected="selected"{{/if}} >
          {{$curr_cat->nom}}
        </option>
      {{/foreach}}
    </select>
    
    </form>
    {{if "dmp"|module_active}}
      {{mb_include module=dmp template=inc_buttons_files_dmp}}
    {{/if}}

    {{if "sisra"|module_active}}
      {{mb_include module=sisra template=inc_buttons_files_sisra}}
    {{/if}}
  {{/if}}
{{/if}}