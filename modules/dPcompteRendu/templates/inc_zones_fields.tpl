<table>
  {{if $destinataires|@count}}
    <tr>
      <td class="destinataireCR text" id="destinataire" colspan="2">
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
      <td id="liste" colspan="2">
        <!-- The div is required because of a Webkit float issue -->
        <div class="listeChoixCR">
          {{foreach from=$lists item=curr_list}}
            <select name="_{{$curr_list->_class_name}}[{{$curr_list->_id}}][]" class="{{$curr_list->nom}}">
              <option value="undef">&mdash; {{$curr_list->nom}}</option>
              {{foreach from=$curr_list->_valeurs item=curr_valeur}}
                <option value="{{$curr_valeur}}" title="{{$curr_valeur}}">{{$curr_valeur|truncate}}</option>
              {{/foreach}}
            </select>
          {{/foreach}}
        </div>
      </td>
    </tr>
  {{/if}}
  
  {{if $noms_textes_libres|@count}}
    <tr>
      <td colspan="2">
      {{foreach from=$noms_textes_libres item=_nom}}
        <div style="max-width: 200px; display: inline-block;">    
          {{$_nom}}
          <textarea class="freetext {{$_nom}}" name="texte_libre[{{$_nom}}]" id="editFrm_texte_libre[{{$_nom}}]"></textarea>
          </div>
          {{main}}
	          new AideSaisie.AutoComplete('editFrm_texte_libre[{{$_nom}}]',
	          {
	            objectClass: '{{$compte_rendu->_class_name}}',
	            contextUserId: User.id,
	            contextUserView: "{{$user_view}}",
	            timestamp: "{{$dPconfig.dPcompteRendu.CCompteRendu.timestamp}}",
	            resetSearchField: false,
	            resetDependFields: false,
	            validateOnBlur: false,
	            property: "_source"
	          });                      
	        {{/main}}
        {{/foreach}}
      </td>
    </tr>
  {{/if}}
  
  {{if $noms_textes_libres || $lists|@count}}
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
        <button class="tick" type="button" onclick="Url.ping({onComplete: submitCompteRendu});">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
    {{/if}}
</table>