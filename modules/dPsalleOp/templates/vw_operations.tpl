{{mb_script module=bloc   script=edit_planning}}
{{mb_script module=system script=alert}}

<script>
  loadOperation = function(operation_id, tr, load_checklist) {
    var url = new Url("salleOp", "ajax_vw_operation");
    url.addParam("operation_id", operation_id);
    url.addParam("date", "{{$date}}");
    url.addParam("salle_id", "{{$salle}}");
    url.addNotNullParam("load_checklist", load_checklist);
    url.requestUpdate("operation_area");

    if (tr) {
      $("listplages").select("tr").invoke("removeClassName", "selected");
      tr.addClassName("selected");
    }
  }

  Main.add(function() {
    {{if $conf.dPsalleOp.COperation.mode || ($currUser->_is_praticien && !$currUser->_is_anesth)}}
      var url = new Url("dPsalleOp", "httpreq_liste_op_prat");
    {{else}}
      var url = new Url("dPsalleOp", "httpreq_liste_plages");
    {{/if}}
    url.addParam("date"         , "{{$date}}");
    url.addParam("hide_finished", "{{$hide_finished}}");

    url.periodicalUpdate('listplages', { frequency: 90 });

    loadOperation('{{$operation_id}}', null);
  });
</script>

<table class="main">
  <tr>
    <td style="width: 220px;" id="listplages"></td>
    <td id="operation_area">&nbsp;</td>
  </tr>
</table>