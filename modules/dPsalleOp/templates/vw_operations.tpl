<script type="text/javascript">

function pageMain() {
  regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&op=0&date=");
  PairEffect.initGroup("acteEffect");
}

</script>

<table class="main">
  <tr>
    <td style="width: 200px;">
      {{include file="inc_liste_plages.tpl"}}
    </td>
    <td class="greedyPane">
      <table class="tbl">
        {{if $selOp->operation_id}}
        <tr>
          <th class="title" colspan="2">
            {{$selOp->_ref_sejour->_ref_patient->_view}} 
            ({{$selOp->_ref_sejour->_ref_patient->_age}} ans) 
            &mdash; Dr. {{$selOp->_ref_chir->_view}}
          </th>
        </tr>
        {{include file="inc_timings_anesth.tpl"}}
        <tr>
          <th>Actes</th>
          <td class="text">
          {{if $canEdit || $modif_operation}}
          {{include file="inc_manage_codes.tpl"}}
          {{else}}
          Il n'est pas possible de modifier les actes d'une intervention antérieure.
          {{/if}}
          </td>
        </tr>
        <tr>
          <th>
            Intervention
            <br />
            Côté {{tr}}COperation.cote.{{$selOp->cote}}{{/tr}}
            <br />
            ({{$selOp->temp_operation|date_format:"%Hh%M"}})
          </th>
          <td class="text">
          {{include file="inc_codage_actes.tpl"}}
          </td>
        </tr>
        {{if $selOp->materiel}}
        <tr>
          <th>Matériel</th>
          <td><strong>{{$selOp->materiel|nl2br}}</strong></td>
        </tr>
        {{/if}}
        {{if $selOp->rques}}
        <tr>
          <th>Remarques</th>
          <td>{{$selOp->rques|nl2br}}</td>
        </tr>
        {{/if}}
        {{else}}
        <tr>
          <th class="title">
            Selectionnez une opération
          </th>
        </tr>
        {{/if}}
      </table>
    </td>
  </tr>
</table>