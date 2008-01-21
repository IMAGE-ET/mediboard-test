{{if $listEtab|@count}}
<select name="_etablissement_transfert_id" onchange="if(this.form.sejour_id){ submitFormSejour(this.value, this.form.sejour_id.value) } else { submitFormSejour(this.value) } ;">
<option value="">&mdash; Etablissement de transfert</option>
{{foreach from=$listEtab item="etab"}}
<option value="{{$etab->_id}}" {{if $etab->_id == $_transfert_id}}selected="selected"{{/if}}>{{$etab->_view}}</option>
{{/foreach}}
</select>
{{/if}}