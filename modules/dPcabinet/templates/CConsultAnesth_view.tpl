<table class="tbl tooltip">
  <tr>
    <th>
      {{$object->_ref_consultation->_view}}
    </th>
  </tr>

  {{if $object->operation_id}}
  <tr>
    <td>
      <strong>Intervention le :</Strong>
      <i>{{$object->_date_op|date_format:"%a %d %b %Y"}}</i><br />
      <strong>Par :</strong>
      <i>Dr {{$object->_ref_operation->_ref_chir->_view}}</i><br />
      Coté {{tr}}COperation.cote.{{$object->_ref_operation->cote}}{{/tr}} &mdash; 
      {{tr}}CSejour.type.{{$object->_ref_operation->_ref_sejour->type}}{{/tr}}
      {{if $object->_ref_operation->_ref_sejour->type!="ambu" && $object->_ref_operation->_ref_sejour->type!="exte"}}
        &mdash; {{$object->_ref_operation->_ref_sejour->_duree_prevue}} jour(s)
      {{/if}}
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <td>
      <strong>Date:</strong>
      <i>le {{$object->_ref_plageconsult->date|date_format:"%d %B %Y"}}</i>
      <br />
      <strong>Praticien:</strong>
      <i>Dr {{$object->_ref_plageconsult->_ref_chir->_view}}</i>
      {{if $object->_ref_consultation->motif}}
        <br />
        <strong>Motif:</strong>
        <i>{{$object->_ref_consultation->motif|nl2br|truncate}}</i>
      {{/if}}      
      {{if $object->_ref_consultation->rques}}
        <br />
        <strong>Remarques:</strong>
        <i>{{$object->_ref_consultation->rques|nl2br|truncate}}</i>
      {{/if}}
      
			{{mb_include module=dPcabinet template=inc_list_actes_ccam subject=$object->_ref_consultation vue=view}}
    </td>
  
  </tr>
</table>