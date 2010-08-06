{{assign var=pdf_thumbnails value=$dPconfig.dPcompteRendu.CCompteRendu.pdf_thumbnails}}

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
  var url = new Url("dPcompteRendu", "ajax_pdf_and_thumbs");
  url.addParam("compte_rendu_id", '{{$modele_id}}');
  url.addParam("suppressHeaders", 1);
  url.addParam("stream", 1);
  url.addParam("generate_thumbs", 0);

  var from = $("fast-edit-table-{{$object_guid}}").select(".freetext");
  var to = getForm("download-pdf-form");
  
  from.each(function(textarea) {
    $V(to[textarea.name], $V(textarea));
  });
}

linkFields = function(ref) {
  var tab = $('fast-edit-table-{{$object_guid}}');
  console.log(tab);
  return new Array(
      tab.select('.liste'),
      tab.select(".freetext"),
      tab.select(".destinataires"),
      new Array($("editFrm_nom"), $("editFrm_file_category_id"), $("editFrm___private")));
}

{{if !$lists|@count && !$noms_textes_libres|@count}}
  Control.Modal.close();
  getForm('download-pdf-form').onsubmit();
  getForm('editFrm').onsubmit();
{{/if}}
</script>


<iframe style='width: 1px; height: 1px;' name="downloadpdf"></iframe>
  
<form style="display: none;" name="download-pdf-form" target="downloadpdf" method="post" action="?m=dPcompteRendu&amp;a=ajax_pdf_and_thumbs"
      onsubmit="printFast(); this.submit();">
  <input type="hidden" name="compte_rendu_id" value='{{$modele_id}}' />
  <input type="hidden" name="object_id" value="{{$compte_rendu->object_id}}"/>
  <input type="hidden" name="suppressHeaders" value="1"/>
  <input type="hidden" name="stream" value="1"/>
  <input type="hidden" name="generate_thumbs" value="0"/>
  {{if $noms_textes_libres}}
    {{foreach from=$noms_textes_libres item=_nom}}
       <input class="freetext" type="hidden" name="texte_libre[{{$_nom}}]"/>
    {{/foreach}}
  {{/if}}
</form>

<form name="editFrm" action="?m={{$m}}" method="post"
      onsubmit="if (checkForm(this) && User.id) {
        return onSubmitFormAjax(this, 
          { onComplete: function() {
              Document.refreshList('{{$object_class}}', '{{$object_id}}'); },
           useDollarV: true})
          }
          return false;"
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
  {{mb_field object=$compte_rendu field="object_id" hidden=1 prop=""}}
  {{mb_field object=$compte_rendu field="object_class" hidden=1 prop=""}}

  <table id="fast-edit-table-{{$object_guid}}" class="form" style="width: 100%; min-height: 200px;">
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
        <button class="hslip" onclick="Control.Modal.close(); Document.create('{{$modele_id}}','{{$object_id}}', null, null, 1, getForm('editFrm'));" type="button">{{tr}}CCompteRendu.switchEditor{{/tr}}</button>
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
              <button class="tick" onclick="Control.Modal.close(); $('fast').update();">{{tr}}Save{{/tr}}</button>
              <button class="print" onclick="Control.Modal.close(); getForm('download-pdf-form').onsubmit();">{{tr}}Save{{/tr}} {{tr}}And{{/tr}} {{tr}}Print{{/tr}}</button>
              <button class="cancel" onclick="Control.Modal.close();$('fast').update();" type="button">{{tr}}Cancel{{/tr}}</button>
            </td>
          </tr>
        </table>
      </td>
      <td id="thumbs_button" style="width: 100px;">
        <div id="thumbs" style="overflow-x: hidden; width: 160px; text-align: center; white-space: normal;">
          {{if $file->_id}}
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
