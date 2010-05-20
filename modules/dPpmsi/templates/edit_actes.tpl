{{assign var="chir_id" value=$selOp->_ref_chir->_id}}
{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="module" value="dPpsmi"}}
{{assign var="object" value=$selOp}}
{{mb_include module=dPsalleOp template=js_codage_ccam}}
<script type="text/javascript">
Main.add (function () {
  Control.Tabs.create('tab-actes', false);
} );
</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="2">
      {{$selOp->_ref_sejour->_ref_patient->_view}} 
      &mdash; {{$selOp->_datetime|date_format:$dPconfig.longdate}}
      <br /> Chirurgien : Dr {{$selOp->_ref_chir->_view}}
      {{if $selOp->_ref_anesth->_id}}
        <br /> Anesthésiste probable : Dr {{$selOp->_ref_anesth->_view}}
      {{/if}} 
    </th>
  </tr>

  <tr>
    <th>Patient</th>
    <td>{{$selOp->_ref_sejour->_ref_patient->_view}} &mdash; {{$selOp->_ref_sejour->_ref_patient->_age}} ans</td>
  </tr>
  
  <tr>
    <td colspan="2">
      <ul id="tab-actes" class="control_tabs">
        <li><a href="#one">CCAM</a></li>
        <li><a href="#two">NGAP</a></li>
      </ul>
    </td>
  </tr>       
  <tr id="one" style="display: none;">
    <th>Actes<br /><br />
      {{tr}}{{$selOp->_class_name}}{{/tr}}
      {{if ($module=="dPplanningOp") || ($module=="dPsalleOp")}}
      <br />
      Côté {{tr}}COperation.cote.{{$selOp->cote}}{{/tr}}
      <br />
      ({{$selOp->temp_operation|date_format:$dPconfig.time}})
      {{/if}}
    </th>
    <td>
      <div id="ccam">
        {{assign var="module" value="dPpmsi"}}
        {{assign var="subject" value=$selOp}}
		    {{mb_include module=dPsalleOp template=inc_codage_ccam}}
      </div>
    </td> 
  </tr>
  <tr id="two" style="display: none;">
    <th class="category" style="vertical-align: middle">
      Actes <br />NGAP
    </th>
    <td id="listActesNGAP">
      {{assign var="object" value=$selOp}}
	    {{mb_include module=dPcabinet template=inc_codage_ngap}}
    </td>
  </tr>
</table>
