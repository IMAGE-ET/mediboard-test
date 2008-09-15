<option value="">{{tr}}CPrescription.protocole.select{{/tr}}</option>
<optgroup label="Protocoles du praticien">
{{foreach from=$protocoles_list_praticien item=proto}}
  <option value="{{$proto->_id}}" {{if $selected_id==$proto->_id}}selected="selected"{{/if}}>{{$proto->libelle}}</option>
{{foreachelse}}
  <option value="" disabled="disabled">{{tr}}CPrescription.protocole.none{{/tr}}</option>
{{/foreach}}
</optgroup>
<optgroup label="Protocoles de la fonction">
{{foreach from=$protocoles_list_function item=proto}}
  <option value="{{$proto->_id}}" {{if $selected_id==$proto->_id}}selected="selected"{{/if}}>{{$proto->libelle}}</option>
{{foreachelse}}
  <option value="" disabled="disabled">{{tr}}CPrescription.protocole.none{{/tr}}</option>
{{/foreach}}
</optgroup>