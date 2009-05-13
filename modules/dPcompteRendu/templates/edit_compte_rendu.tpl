<script type="text/javascript">
  
{{if $compte_rendu->_id}}
window.opener.Document.refreshList(
  '{{$compte_rendu->object_class}}',
	'{{$compte_rendu->object_id}}'	
);
{{/if}}

function submitCompteRendu(){
  (function(){
    var form = getForm("editFrm");
    if(checkForm(form) && window.userId) {
      form.submit();
    }
  }).defer();
}

document.stopObserving('keydown', closeWindowByEscape);
</script>

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="Url.ping({onComplete: submitCompteRendu}); return false;">

<input type="hidden" name="m" value="dPcompteRendu" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_modele_aed" />
<input type="hidden" name="function_id" value="" />
<input type="hidden" name="chir_id" value="" />
<input type="hidden" name="group_id" value="" />

{{mb_field object=$compte_rendu field="compte_rendu_id" hidden=1 prop=""}}
{{mb_field object=$compte_rendu field="object_id" hidden=1 prop=""}}
{{mb_field object=$compte_rendu field="object_class" hidden=1 prop=""}}

<table class="form">
  <tr>
    <th class="category">
      <div class="idsante400" id="{{$compte_rendu->_guid}}"></div>
    
	    <a style="float:right;" href="#" onclick="guid_log('{{$compte_rendu->_guid}}')">
	      <img src="images/icons/history.gif" alt="historique" />
	    </a>

      <strong>Nom du document :</strong>
      <input name="nom" size="50" value="{{$compte_rendu->nom}}" />
      &mdash;
      <strong>Catégorie :</strong>
      <select name="file_category_id">
      <option value=""{{if !$compte_rendu->file_category_id}} selected="selected"{{/if}}>&mdash; Aucune Catégorie</option>
      {{foreach from=$listCategory item=currCat}}
      <option value="{{$currCat->file_category_id}}"{{if $currCat->file_category_id==$compte_rendu->file_category_id}} selected="selected"{{/if}}>{{$currCat->nom}}</option>
      {{/foreach}}
      </select>
    </th>
  </tr>
  {{if $destinataires|@count}}
  <tr>
    <td colspan="2" class="destinataireCR text" id="destinataire">
      {{foreach from=$destinataires key=curr_class_name item=curr_class}}
        &bull; <strong>{{tr}}{{$curr_class_name}}{{/tr}}</strong> :
        {{foreach from=$curr_class key=curr_index item=curr_dest}}
          <input type="checkbox" name="_dest_{{$curr_class_name}}_{{$curr_index}}" />
          <label for="_dest_{{$curr_class_name}}_{{$curr_index}}">
            {{$curr_dest->nom}} ({{tr}}CDestinataire.tag.{{$curr_dest->tag}}{{/tr}});
          </label>
        {{/foreach}}
        <br />
      {{/foreach}}
    </td>
  </tr>
  {{/if}}

  {{if $lists|@count}}
  <tr>
    <td class="listeChoixCR" id="liste">
        {{foreach from=$lists item=curr_list}}
          <select name="_{{$curr_list->_class_name}}[{{$curr_list->_id}}][]">
            <option value="undef">&mdash; {{$curr_list->nom}}</option>
            {{foreach from=$curr_list->_valeurs item=curr_valeur}}
            <option value="{{$curr_valeur}}" title="{{$curr_valeur}}">{{$curr_valeur|truncate}}</option>
            {{/foreach}}
          </select>
        {{/foreach}}
    </td>
  </tr>
  
  <tr>
    <td class="button text">
      <div id="multiple-info" class="small-info" style="display: none;">
        Pour pouvoir utiliser les choix multiples, cliquez sur les options voulues pour les sélectionner
        tout en maintenant la touche <tt>CTRL</tt> si vous utilisez MS Windows (<tt>CMD</tt> avec Mac OS).
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
      <button class="tick" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  {{/if}}

  <tr>
    <td style="height: 600px">
      <textarea id="htmlarea" name="source">
        {{$templateManager->document}}
      </textarea>
    </td>
  </tr>

</table>

</form>