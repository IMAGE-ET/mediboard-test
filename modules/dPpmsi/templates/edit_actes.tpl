{{assign var="chir_id" value=$selOp->_ref_chir->_id}}
{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="module" value="dPpsmi"}}
{{assign var="object" value=$selOp}}
{{include file="../../dPsalleOp/templates/js_gestion_ccam.tpl"}}

<script type="text/javascript">
function pageMain() {
  PairEffect.initGroup("acteEffect");
}
</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="2">
      {{$selOp->_ref_sejour->_ref_patient->_view}} 
      &mdash; {{$selOp->_datetime|date_format:"%A %d %B %Y"}}
      <br /> Chirurgien : Dr. {{$selOp->_ref_chir->_view}}
      <br /> Anesthésiste probable : Dr. {{$selOp->_ref_anesth->_view}} 
    </th>
  </tr>

  <tr>

  <th>Patient</th>
  <td>{{$selOp->_ref_sejour->_ref_patient->_view}} &mdash; {{$selOp->_ref_sejour->_ref_patient->_age}} ans</td>
  </tr>
  <tr>
    <th>Actes<br /><br />
      {{tr}}{{$selOp->_class_name}}{{/tr}}
      {{if ($module=="dPplanningOp") || ($module=="dPsalleOp")}}
      <br />
      Côté {{tr}}COperation.cote.{{$selOp->cote}}{{/tr}}
      <br />
      ({{$selOp->temp_operation|date_format:"%Hh%M"}})
      {{/if}}
    </th>
    <td>
      <div id="ccam">
        {{assign var="module" value="dPpmsi"}}
        {{assign var="subject" value=$selOp}}
        {{include file="../../dPsalleOp/templates/inc_gestion_ccam.tpl"}}
      </div>
    </td> 
  </tr>
</table>