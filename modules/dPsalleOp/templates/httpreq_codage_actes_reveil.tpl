{{assign var="chir_id" value=$operation->chir_id}}
{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="module" value="dPsalleOp"}}
{{assign var="object" value=$operation}}
{{mb_include module=dPsalleOp template=js_codage_ccam}}

<table class="form">
  <tr>
    <th class="category" colspan="2">Actes CCAM en salle de réveil</th>
  </tr>
  <tr>
    <td>
      <div id="ccam">
        {{assign var="subject" value=$operation}}
		    {{mb_include module=dPsalleOp template=inc_codage_ccam}}
      </div>
    </td>
  </tr>
</table>