<tr>
  <td colspan="2" class="text">
    <div class="small-info">
      Informations pertinentes pour les seuls 
      <strong>professionnels de santé</strong>.
    </div>
  </td>
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
  <th>{{mb_label object=$object field="cps"}}</th>
  <td>{{mb_field object=$object field="cps"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$object field="mail_apicrypt"}}</th>
  <td>{{mb_field object=$object field="mail_apicrypt"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$object field="secteur"}}</th>
  <td>{{mb_field object=$object field="secteur" emptyLabel="Choose"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$object field="cab"}}</th>
  <td>{{mb_field object=$object field="cab"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$object field="conv"}}</th>
  <td>{{mb_field object=$object field="conv"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$object field="zisd"}}</th>
  <td>{{mb_field object=$object field="zisd"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$object field="ik"}}</th>
  <td>{{mb_field object=$object field="ik"}}</td>
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

{{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}}}
  <tr>
    <th>{{mb_label object=$object field="ean"}}</th>
    <td>{{mb_field object=$object field="ean"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$object field="rcc"}}</th>
    <td>{{mb_field object=$object field="rcc"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$object field="adherent"}}</th>
    <td>{{mb_field object=$object field="adherent"}}</td>
  </tr>
{{/if}}