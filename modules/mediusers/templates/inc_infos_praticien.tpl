<tr>
  <th colspan="2" class="category">Informations praticien </th>
</tr>

<tr>
  <th>{{mb_label object=$object field="discipline_id"}}</th>
  <td>
    <select name="discipline_id" style="width: 150px;" class="{{$object->_props.discipline_id}}">
      <option value="">&mdash; Choisir une spécialité</option>
      {{foreach from=$disciplines item=curr_discipline}}
      <option value="{{$curr_discipline->discipline_id}}" {{if $curr_discipline->discipline_id == $object->discipline_id}} selected="selected" {{/if}}>
        {{$curr_discipline->_view}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>


<tr>  
  <th>{{mb_label object=$object field="spec_cpam_id"}}</th>
  <td>
    <select name="spec_cpam_id" style="width: 150px;" class="{{$object->_props.spec_cpam_id}}">
      <option value="">&mdash; Choisir une spécialité</option>
      {{foreach from=$spec_cpam item=curr_spec}}
      <option value="{{$curr_spec->spec_cpam_id}}" {{if $curr_spec->spec_cpam_id == $object->spec_cpam_id}} selected="selected" {{/if}}>
        {{$curr_spec->_view}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field="adeli"}}</th>
  <td>{{mb_field object=$object field="adeli"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$object field="rpps"}}</th>
  <td>{{mb_field object=$object field="rpps"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$object field="titres"}}</th>
  <td>{{mb_field object=$object field="titres"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$object field="compte"}}</th>
  <td>{{mb_field object=$object field="compte"}}</td>
</tr>

{{if is_array($banques)}}
<!-- Choix de la banque quand disponible -->
<tr>
  <th>{{mb_label object=$object field="banque_id"}}</th>
  <td>
    <select name="banque_id" style="width: 150px;">
      <option value="">&mdash; Choix d'une banque</option>
      {{foreach from=$banques item="banque"}}
      <option value="{{$banque->_id}}" {{if $object->banque_id == $banque->_id}}selected = "selected"{{/if}}>
        {{$banque->_view}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>
{{/if}}