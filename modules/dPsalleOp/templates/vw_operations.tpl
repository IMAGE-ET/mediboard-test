{{if $op}}
{{assign var="chir_id" value=$selOp->_ref_chir->_id}}
{{/if}}

{{mb_script module=bloc script=edit_planning}}

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
      {{if $require_check_list}}
        <table class="main layout">
          <tr>
            {{foreach from=$daily_check_lists item=check_list}}
              <td>
                <h2>{{$check_list->_ref_list_type->title}}</h2>
                {{if $check_list->_ref_list_type->description}}
                  <p>{{$check_list->_ref_list_type->description}}</p>
                {{/if}}

                {{mb_include module=salleOp template=inc_edit_check_list
                check_list=$check_list
                check_item_categories=$check_list->_ref_list_type->_ref_categories
                personnel=$listValidateurs
                list_chirs=$listChirs
                list_anesths=$listAnesths
                }}
              </td>
            {{/foreach}}
          </tr>
        </table>
      {{else}}
        {{if $selOp->_id}}
          {{mb_include module=salleOp template=inc_operation}}
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
      {{/if}}
    </td>
  </tr>
</table>