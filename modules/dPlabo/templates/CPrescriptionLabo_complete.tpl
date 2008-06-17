<table class="tbl">
  <tr>
    <th class="title">Dr {{$object->_ref_praticien->_view}}</th>
    <th class="title">{{$object->_ref_patient->_view}}</th>
  </tr>
</table>

{{assign var="prescription" value=$object}}
{{include file="../../dPlabo/templates/inc_vw_examens_prescriptions.tpl}}