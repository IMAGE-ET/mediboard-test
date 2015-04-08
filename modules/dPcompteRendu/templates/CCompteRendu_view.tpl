{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{mb_script module=compteRendu script=document ajax=1}}

{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var=document value=$object}}

<script>
  trashDoc = function(form, file_view) {
    return confirmDeletion(form, {typeName: "le document", objName: file_view}, function() {
      if (window.loadAllDocs) {
        loadAllDocs();
      }
    });
  };

  archiveDoc = function(form) {
    if (confirm($T("CFile-comfirm_cancel"))) {
      $V(form.annule, 1);
      return onSubmitFormAjax(form, function() {
        if (window.loadAllDocs) {
          loadAllDocs();
        }
      });
    }
  };

  restoreDoc = function(form) {
    $V(form.annule, 0);
    return onSubmitFormAjax(form, function() {
      if (window.loadAllDocs) {
        loadAllDocs();
      }
    });
  };
</script>
<table class="tbl">
  <tr>
    <th class="title text">
      {{mb_include module=system template=inc_object_idsante400}}
      {{mb_include module=system template=inc_object_history}}
      {{mb_include module=system template=inc_object_notes}}
      {{$object}}
    </th>
  </tr>
</table>

<table class="main">
  <tr>
    {{assign var=file value=$document->_ref_file}}
    {{if $document->object_id && $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs && $file->_id}}
    <td id="thumbnail-{{$document->_id}}" style="text-align: center;">
     <a href="#1" onclick="new Url().ViewFilePopup('{{$document->object_class}}', '{{$document->object_id}}', 'CCompteRendu', '{{$document->_id}}')">
       <img style="width: 64px; height: 92px; background: white; border: 1px solid black;"
            src="?m=files&raw=fileviewer&file_id={{$file->_id}}&phpThumb=1&w=64&h=92" />
     </a>
    </td>

    {{else}}
    <td>
      <img src="images/pictures/medifile.png" />
    </td>
    {{/if}}

    <td style="vertical-align: top;" class="text">
      {{foreach from=$object->_specs key=prop item=spec}}
        {{mb_include module=system template=inc_field_view}}
      {{/foreach}}
      <strong>{{mb_label class=CFile field=_file_size}}</strong> : {{mb_value object=$file field=_file_size}} <br />
      <strong>Nombre de mots</strong> : {{$document->_source|count_words}}
    </td>
  </tr>

  <tr>
    <td class="button" colspan="2">
      {{if $document->_can->edit}}
        {{if !$document->object_id}}
          <a class="button search" href="?m=compteRendu&tab=addedit_modeles&compte_rendu_id={{$document->_id}}">
            {{tr}}Open{{/tr}}
          </a>
        {{else}}
          <button type="button" class="edit" onclick="Document.edit('{{$document->_id}}')">{{tr}}Edit{{/tr}}</button>
          <button type="button" class="print" onclick="
          {{if $conf.dPcompteRendu.CCompteRendu.pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
            Document.printPDF('{{$document->_id}}');
          {{else}}
            Document.print('{{$document->_id}}');
          {{/if}}">{{tr}}Print{{/tr}}</button>

          <form name="actionDoc{{$document->_guid}}" method="post">
            <input type="hidden" name="m" value="compteRendu" />
            <input type="hidden" name="dosql" value="do_modele_aed" />
            {{mb_key object=$document}}
            {{mb_field object=$document field=annule hidden=1}}

            {{if $document->annule}}
              <button type="button" class="undo" onclick="restoreDoc(this.form, '{{$document}}')">{{tr}}Restore{{/tr}}</button>
            {{else}}
              <button type="button" class="archive" onclick="archiveDoc(this.form, '{{$document}}')">{{tr}}Archive{{/tr}}</button>
            {{/if}}
            <button type="button" class="trash" onclick="trashDoc(this.form, '{{$document}}')">{{tr}}Delete{{/tr}}</button>
          </form>
        {{/if}}
      {{/if}}
    </td>
  </tr>
</table>