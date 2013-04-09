<td colspan="{{if isset($colspan|smarty:nodefaults)}}{{$colspan}}{{/if}}">
  <select name="{{$name}}" style="width: 15em;" 
    {{if $object->_class == "CFactureCabinet" || $object->_class == "CFactureEtablissement"}}onchange="return onSubmitFormAjax(this.form);"
    {{else}}
    onchange="Value.synchronize(this, 'editSejour');"
    {{/if}}>
    <option value="" {{if !$object->$name}}selected="selected" {{/if}}>&mdash; Choisir une assurance</option>
    {{foreach from=$patient->_ref_correspondants_patient item=_assurance}}
      <option value="{{$_assurance->_id}}" {{if $object->$name == $_assurance->_id}} selected="selected" {{/if}}>
        {{$_assurance->nom}}  
        {{if $_assurance->date_debut && $_assurance->date_fin}}
          Du {{$_assurance->date_debut|date_format:"%d/%m/%Y"}} au {{$_assurance->date_fin|date_format:"%d/%m/%Y"}}
        {{/if}}
      </option>
    {{/foreach}}
  </select>
</td>