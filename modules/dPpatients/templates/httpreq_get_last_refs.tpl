<table width="100%">
  <tr>
    <td><strong>{{$patient->_view}} &mdash; {{$patient->_age}} ans</strong></td>
  </tr>
  {{foreach from=$patient->_ref_sejours item=curr_sejour}}
  <tr>
    <td class="text">
      <strong>
        Sejour du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}}
        au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}
      </strong>
      {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
      <br />
      <input type="radio" name="_operation_id" value="{{$curr_op->operation_id}}" />
      Intervention le {{$curr_op->_datetime|date_format:"%d/%m/%Y"}}
      avec le Dr. {{$curr_op->_ref_chir->_view}}
      {{if $curr_op->_ext_codes_ccam|@count || $curr_op->libelle}}
      <ul>
        {{if $curr_op->libelle}}
        <li><em>[{{$curr_op->libelle}}]</em></li>
        {{/if}}
        {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
        <li>{{$curr_code->libelleLong}}</li>
        {{/foreach}}
      </ul>
      {{/if}}
      {{foreachelse}}
      <br />
      <em>Aucune intervention</em>
      {{/foreach}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td><em>Aucun séjour<em></td>
  </tr>
  {{/foreach}}
  {{foreach from=$patient->_ref_consultations item=curr_consult}}
  <tr>
    <td class="text">
      Consultation le {{$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}}
      avec le Dr. {{$curr_consult->_ref_plageconsult->_ref_chir->_view}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td><em>Aucune consultation<em></td>
  </tr>
  {{/foreach}}
</table>