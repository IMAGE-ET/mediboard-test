{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
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
    $('fast-{{$unique_id}}').update();
  }
});
printFast = function() {
  var from = $("fast-edit-table-{{$uid_fast_mode}}").select(".freetext");
  var to = getForm("create-pdf-form-{{$uid_fast_mode}}");
  
  from.each(function(textarea) {
    $V(to[textarea.name], $V(textarea));
  });
}

linkFields = function(ref) {
  var tab = $('fast-edit-table-{{$uid_fast_mode}}');
  var form = getForm("fastModeForm-{{$uid_fast_mode}}");
  
  return [
    tab.select(".liste"),
    tab.select(".freetext"),
    tab.select(".destinataires"),
    [form.nom, form.file_category_id, form._private]
  ];
}

generatePdf = function(id) {
	oState = $("state");
	oState.className = "loading";
	oState.setStyle({backgroundPosition: "50% 50%", height: '100px', textAlign: "center", marginTop: "1em", fontWeight: "bold"});
	oState.innerHTML = "Génération PDF en cours";
	var form = getForm('create-pdf-form-{{$uid_fast_mode}}'); 
	$V(form.compte_rendu_id, id);
	form.onsubmit();
}

printDoc = function(id, args) {
	var iframe = $("iframe_source{{$uid_fast_mode}}").contentWindow.document;
	if (Prototype.Browser.IE) {
	  iframe.open();
	  iframe.write(args._entire_doc);
	  iframe.close();
	  window.frames['iframe_source{{$uid_fast_mode}}'].focus();
	}
	else {
	  iframe.documentElement.innerHTML = args._entire_doc;
	}
	window.frames['iframe_source{{$uid_fast_mode}}'].print();
	Control.Modal.close();
	Document.refreshList('{{$object_class}}', '{{$object_id}}');
}

streamOrNotStream = function(form) {
	if ($V(form.stream) == 1) {
		$V(form.callback, "streamPDF");
		onSubmitFormAjax(form);
	}
	else {
		onSubmitFormAjax(form, {onComplete: function() {
      Control.Modal.close();
      Document.refreshList('{{$object_class}}', '{{$object_id}}')
    }});
	}
}

streamPDF = function(id) {
	var form = getForm("stream-pdf-{{$uid_fast_mode}}");
	$V(form.file_id, id);
	form.submit();
	Control.Modal.close();
	Document.refreshList('{{$object_class}}', '{{$object_id}}');
}


Main.add(function() {
	{{if $lists|@count == 0 && $noms_textes_libres|@count == 0}}
	  var oForm = getForm('fastModeForm-{{$uid_fast_mode}}');
	  {{if $compte_rendu->fast_edit_pdf}}
      $V(getForm('create-pdf-form-{{$uid_fast_mode}}').stream, 1);
    {{else}}
      $V(oForm.callback, 'printDoc');
    {{/if}}
    	oForm.onsubmit();
	{{/if}}
});


</script>

<iframe style='width: 0px; height: 0px; position: absolute; border: none;' frameborder="no" name="iframe_source{{$uid_fast_mode}}" id="iframe_source{{$uid_fast_mode}}"></iframe>

<form name="stream-pdf-{{$uid_fast_mode}}" method="post" action="?" target="_blank">
  <input type="hidden" name="m" value="dPcompteRendu" />
  <input type="hidden" name="dosql" value="do_pdf_cfile_aed" />
  <input type="hidden" name="file_id" value="" />
</form>

<form style="display: none;" name="create-pdf-form-{{$uid_fast_mode}}" method="post" action="?"
      onsubmit="printFast(); streamOrNotStream(this); return false;">
  <input type="hidden" name="compte_rendu_id" value='' />
	<input type="hidden" name="m" value="dPcompteRendu" />
	<input type="hidden" name="dosql" value="do_pdf_cfile_aed" />
  <input type="hidden" name="stream" value="0" />
  <input type="hidden" name="callback" value="closeModal"/>
  {{if $noms_textes_libres}}
    {{foreach from=$noms_textes_libres item=_nom}}
       <input class="freetext" type="hidden" name="texte_libre[{{$_nom}}]"/>
    {{/foreach}}
  {{/if}}
</form>

<form name="fastModeForm-{{$uid_fast_mode}}" action="?m={{$m}}" method="post"
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
  <input type="hidden" name="fast_edit_pdf" value="0"/>
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
      <th class="title" colspan="2">
        {{tr}}CCompteRendu-fast_edit-title{{/tr}}
      </th>
    </tr>
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
        <button class="hslip" onclick="Control.Modal.close(); Document.create('{{$modele_id}}','{{$object_id}}', null, null, 1, getForm('fastModeForm-{{$uid_fast_mode}}'));" type="button">{{tr}}CCompteRendu.switchEditor{{/tr}}</button>
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
	              {{main}}
	                new AideSaisie.AutoComplete('fastModeForm-{{$uid_fast_mode}}_texte_libre[{{$_nom}}]',
                   {
                      objectClass: '{{$compte_rendu->_class_name}}',
                      contextUserId: User.id,
                      contextUserView: "{{$user_view}}",
		                  timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
		                  resetSearchField: false,
		                  resetDependFields: false,
		                  validateOnBlur: false,
                      property: "_source"
		                });                      
						    {{/main}}
              </td>
            </tr>
            {{/foreach}}
          {{/if}}
          
          <tr>
            <td colspan="2" style="text-align: center;">
              <button class="tick">{{tr}}Save{{/tr}}</button>
              <button class="printPDF singleclick" onclick="$V(getForm('create-pdf-form-{{$uid_fast_mode}}').stream, 1); this.form.onsubmit();" type="button">{{tr}}Save{{/tr}} {{tr}}and{{/tr}} {{tr}}Print{{/tr}}</button>
              <button class="print singleclick" onclick="$V(getForm('fastModeForm-{{$uid_fast_mode}}').callback, 'printDoc'); this.form.onsubmit();" type="button">{{tr}}Save{{/tr}} {{tr}}and{{/tr}} {{tr}}Print{{/tr}}</button>
              <button class="cancel singleclick" onclick="Control.Modal.close();$('fast-{{$unique_id}}').update();" type="button">{{tr}}Close{{/tr}}</button>
              
              <div id="state" style="width: 100%; height: 100%"></div>
            </td>
          </tr>
        </table>
      </td>
      {{if $pdf_thumbnails == 1}}
        <td style="width: 100px; height: 200px;">
          <div id="thumbs" style="overflow-x: hidden; width: 160px; text-align: center; white-space: normal; height: 200px;">
            {{if isset($file|smarty:nodefaults) && $file->_id}}
              <img class="thumbnail" src="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id={{$file->_id}}&phpThumb=1&wl=160&hp=160"
                   onclick="(new Url).ViewFilePopup('CCompteRendu', '{{$modele_id}}', 'CFile', '{{$file->_id}}');"
                   style="width: 113px; height: 160px;"/>
            {{else}}
              {{tr}}CCompteRendu.nothumbs{{/tr}}
            {{/if}}
          </div>
        </td>
      {{/if}}
    </tr>
  </table>
</form>
