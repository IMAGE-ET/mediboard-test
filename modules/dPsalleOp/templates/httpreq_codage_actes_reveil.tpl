<script type="text/javascript">
function pageMain(){
  {{if !$dPconfig.dPsalleOp.CActeCCAM.openline}}
  PairEffect.initGroup("acteEffect");
  {{/if}}
}
</script>

{{assign var="chir_id" value=$operation->chir_id}}
{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="module" value="dPsalleOp"}}
{{assign var="object" value=$operation}}
{{include file="../../dPsalleOp/templates/js_gestion_ccam.tpl"}}

<table class="form">
  <tr>
    <th class="category" colspan="2">Actes CCAM en salle de réveil</th>
  </tr>
  <tr>
    <td>
      <div id="ccam">
        {{assign var="subject" value=$operation}}
        {{include file="inc_gestion_ccam.tpl"}}
      </div>
    </td>
  </tr>
</table>