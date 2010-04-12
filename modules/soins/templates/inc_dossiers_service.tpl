<tr>
  <td>
    <button class="search notext" onclick="modalwindow = modal($('modal-{{$_sejour->_id}}'));"></button>
    {{$_sejour->_ref_patient->_view}}
  </td>
  <td>{{mb_value object=$_sejour field="_entree"}}</td>
  <td>{{mb_value object=$_sejour field="_sortie"}}</td>
  <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}</td>
  <td>{{mb_value object=$_sejour field="type"}}</td>
  <td>
    {{foreach from=$_sejour->_ref_operations item=_operation}}
      {{mb_include module=dPplanningOp template=inc_vw_operation}}
    {{/foreach}}
  </td>
</tr>