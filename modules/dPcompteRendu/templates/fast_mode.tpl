{{assign var=pdf_thumbnails value=$dPconfig.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{unique_id var=uid_fast_mode}}

<script type="text/javascript">
toggleOptions = function() {
  $$("#liste select").each(function(select) {
    select.size = select.size != 4 ? 4 : 1;
    select.multiple = !select.multiple;
    select.options[0].selected = false;
  } );
  $("multiple-info").toggle();
}
document.observe('keydown', function(e){
  var keycode = Event.key(e);
  if(keycode == 27) {
    Control.Modal.close();
    $('fast').update();
  }
});
printFast = function() {
  var from = $("fast-edit-table-{{$uid_fast_mode}}").select(".freetext");
  var to = getForm("download-pdf-form-{{$uid_fast_mode}}");
  
  from.each(function(textarea) {
    $V(to[textarea.name], $V(textarea));
  });
}

linkFields = function(ref) {
  var tab = $('fast-edit-table-{{$uid_fast_mode}}');
  var form = getForm("fastModeForm");
  
  return [
    tab.select(".liste"),
    tab.select(".freetext"),
    tab.select(".destinataires"),
    [form.nom, form.file_category_id, form._private]
  ];
}

generatePdf = function(id) {
	var form = getForm('download-pdf-form-{{$uid_fast_mode}}'); 
	$V(form.compte_rendu_id, id);
	form.onsubmit();
}

streamOrNotStream = function(form) {
	if ($V(form.stream) == 1)
	  form.submit();
	else
	  onSubmitFormAjax(form);

  Document.refreshList('{{$object_class}}', '{{$object_id}}');
}

{{if !$lists|@count && !$noms_textes_libres|@count}}
Main.add(function() {
  Control.Modal.close();
  $V(getForm('download-pdf-form-{{$uid_fast_mode}}').stream, 1);
  getForm('fastModeForm').onsubmit();
});
{{/if}}

</script>

<iframe style='width: 0px; height: 0px;' border="0" frameborder="no" name="downloadpdf-{{$uid_fast_mode}}"></iframe>
  
<form style="display: none;" name="download-pdf-form-{{$uid_fast_mode}}" method="post" action="?" target="_blank"
      onsubmit="printFast(); streamOrNotStream(this);">
  <input type="hidden" name="compte_rendu_id" value='' />
	<input type="hidden" name="m" value="dPcompteRendu" />
	<input type="hidden" name="dosql" value="do_pdf_cfile_aed" />
  <input type="hidden" name="stream" value="0" />
  {{if $noms_textes_libres}}
    {{foreach from=$noms_textes_libres item=_nom}}
       <input class="freetext" type="hidden" name="texte_libre[{{$_nom}}]"/>
    {{/foreach}}
  {{/if}}
  
  <!-- the div is needed (textarea-container) -->
  <div style="display: none;">
	  <textarea name="content">{{$_source}}</textarea>
  </div>
</form>

<form name="fastModeForm" action="?m={{$m}}" method="post"
      onsubmit="if (checkForm(this) && User.id) {return onSubmitFormAjax(this, { useDollarV: true })}; return false;"
      class="{{$compte_rendu->_spec}}">
  <input type="hidden" name="m" value="dPcompteRendu" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_modele_aed" />
  <input type="hidden" name="function_id" value="" />
  <input type="hidden" name="praticien_id" value="0" />
  <input type="hidden" name="group_id" value="" />
  <input type="hidden" name="modele_id" value="{{$modele_id}}" />
  <input type="hidden" name="compte_rendu_id" value="" />
  <input type="hidden" name="fast_edit" value="1" />
	<input type="hidden" name="callback" value="generatePdf" />
	<input type="hidden" name="suppressHeaders" value="1"/>
	<input type="hidden" name="dialog" value="1"/>
  {{mb_field object=$compte_rendu field="object_id" hidden=1 prop=""}}
  {{mb_field object=$compte_rendu field="object_class" hidden=1 prop=""}}
  
  <!-- the div is needed (textarea-container) -->
  <div style="display: none;">
    <textarea name="_source">{{$_source}}</textarea>
  </div>
  
  <table id="fast-edit-table-{{$uid_fast_mode}}" class="form" style="width: 100%; min-height: 200px;">
    <tr>
      <th class="category" colspan="2">
        {{if $compte_rendu->_id}}
          {{mb_include module=system template=inc_object_idsante400 object=$compte_rendu}}
          {{mb_include module=system template=inc_object_history object=$compte_rendu}}
        {{/if}}
        {{mb_label object=$compte_rendu field=nom}}
        {{mb_field object=$compte_rendu field=nom}}
        
        &mdash;
        {{mb_label object=$compte_rendu field=file_category_id}}
        <select name="file_category_id">
          <option value="" {{if !$compte_rendu->file_category_id}} selected="selected"{{/if}}>&mdash; Aucune Catégorie</option>
          {{foreach from=$listCategory item=currCat}}
            <option value="{{$currCat->file_category_id}}"{{if $currCat->file_category_id==$compte_rendu->file_category_id}} selected="selected"{{/if}}>{{$currCat->nom}}</option>
          {{/foreach}}
        </select>
        
        &mdash;
        <label>
          {{tr}}CCompteRendu-private{{/tr}}
          {{mb_field object=$compte_rendu field=private typeEnum="checkbox"}}
        </label>
      </th>
    </tr>
    <tr>
      <td colspan="2">
        <button class="hslip" onclick="Control.Modal.close(); Document.create('{{$modele_id}}','{{$object_id}}', null, null, 1, getForm('fastModeForm'));" type="button">{{tr}}CCompteRendu.switchEditor{{/tr}}</button>
      </td>
    </tr>
    <tr>
      <td style="width: 70%;">
        <table style="width: 100%;">
          {{if $lists|@count}}
            <tr>
              <td id="liste" colspan="2">
                <!-- The div is required because of a Webkit float issue -->
                <div class="listeChoixCR">
                  {{foreach from=$lists item=curr_list}}
                    <select name="_{{$curr_list->_class_name}}[{{$curr_list->_id}}][]" class="liste">
                      <option value="undef">&mdash; {{$curr_list->nom}}</option>
                      {{foreach from=$curr_list->_valeurs item=curr_valeur}}
                        <option value="{{$curr_valeur}}" title="{{$curr_valeur}}">{{$curr_valeur|truncate}}</option>
                      {{/foreach}}
                    </select>
                  {{/foreach}}
                </div>
              </td>
            </tr>
            <tr>
              <td class="button text" colspan="2">
                <div id="multiple-info" class="small-info" style="display: none;">
                {{tr}}CCompteRendu-use-multiple-choices{{/tr}}
                </div>
                <script type="text/javascript">
                  function toggleOptions() {
                    $$("#liste select").each(function(select) {
                      select.size = select.size != 4 ? 4 : 1;
                      select.multiple = !select.multiple;
                      select.options[0].selected = false;
                    } );
                    $("multiple-info").toggle();
                  }
                </script>
                <button class="hslip" type="button" onclick="toggleOptions();">{{tr}}Multiple options{{/tr}}</button>
              </td>
            </tr>
          {{/if}}
          
          {{if $noms_textes_libres|@count}}
            {{foreach from=$noms_textes_libres item=_nom}}
            <tr>
              <td>
                {{$_nom}}
                <textarea class="freetext" name="texte_libre[{{$_nom}}]"></textarea>
              </td>
            </tr>
            {{/foreach}}
          {{/if}}
          
          <tr>
            <td colspan="2" style="text-align: center;">
              <button class="tick" onclick="Control.Modal.close();">{{tr}}Save{{/tr}}</button>
              <button class="print" onclick="Control.Modal.close(); $V(getForm('download-pdf-form-{{$uid_fast_mode}}').stream, 1);">{{tr}}Save{{/tr}} {{tr}}and{{/tr}} {{tr}}Print{{/tr}}</button>
              <button class="cancel" onclick="Control.Modal.close();$('fast-{{$object_class}}-{{$object_id}}').update();" type="button">{{tr}}Cancel{{/tr}}</button>
            </td>
          </tr>
        </table>
      </td>
      <td id="thumbs_button" style="width: 100px;">
        <div id="thumbs" style="overflow-x: hidden; width: 160px; text-align: center; white-space: normal;">
          {{if isset($file|smarty:nodefaults) && $file->_id}}
            <img class="thumbnail" src="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id={{$file->_id}}&phpThumb=1&wl=200&hp=200"
                 onclick="(new Url).ViewFilePopup('CCompteRendu', '{{$modele_id}}', 'CFile', '{{$file->_id}}');"/>
          {{else}}
            {{tr}}CCompteRendu.nothumbs{{/tr}}
          {{/if}}
        </div>
      </td>
    </tr>
  </table>
</form>
