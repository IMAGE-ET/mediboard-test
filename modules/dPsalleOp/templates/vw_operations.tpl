{{if $op}}
{{assign var="chir_id" value=$selOp->_ref_chir->_id}}
{{/if}}

<script type="text/javascript">

Main.add(function () {
  var url = new Url;
  {{if $conf.dPsalleOp.COperation.mode || ($currUser->_is_praticien && !$currUser->_is_anesth)}}
  url.setModuleAction("dPsalleOp", "httpreq_liste_op_prat");
  {{else}}
  url.setModuleAction("dPsalleOp", "httpreq_liste_plages");
  {{/if}}
  url.addParam("date", "{{$date}}");
  url.addParam("operation_id", "{{$selOp->_id}}");
  url.addParam("hide_finished", "{{$hide_finished}}");
  
  url.periodicalUpdate('listplages', { frequency: 90 });

  // Effet sur le programme
  if ($('listplages') && $('listplages-trigger')){
    new PairEffect("listplages", { sEffect : "appear", bStartVisible : true });
  }
});

</script>

<table class="main">
  <tr>
    <td style="width: 220px;" id="listplages"></td>
    <td>
    {{if $selOp->_id}}
      {{if $conf.dPsalleOp.CDailyCheckList.active != '1' || 
           $date < $smarty.now|date_format:'%Y-%m-%d' || 
           $daily_check_list->_id && $daily_check_list->validator_id || 
           $currUser->_is_praticien}}
        {{mb_include module=salleOp template=inc_operation}}
      {{else}}
        {{mb_include module=salleOp template=inc_edit_check_list
                  check_list=$daily_check_list 
                  check_item_categories=$daily_check_item_categories
                  personnel=$listValidateurs}}
      {{/if}}
    {{else}}
      <div class="big-info">
        Veuillez sélectionner une intervention dans la liste pour pouvoir :
        <ul>
          <li>sélectionner le personnel en salle</li>
          <li>effectuer l'horodatage</li>
          <li>coder les diagnostics</li>
          <li>coder les actes</li>
          <li>consulter le dossier</li>
        </ul>
      </div>
    {{/if}}
    </td>
  </tr>
</table>