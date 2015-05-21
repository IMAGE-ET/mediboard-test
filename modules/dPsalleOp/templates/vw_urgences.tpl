<script>
  Main.add(function () {
    Calendar.regField(getForm("changeDate").date, null, {noView: true});
  });

  refreshLine = function(operation_id) {
    var url = new Url("salleOp", "ajax_refresh_line_hors_plage");
    url.addParam("operation_id", operation_id);
    url.requestUpdate("hors_plage_"+operation_id);
  };

  removeLine = function(operation_id) {
    $('hors_plage_'+operation_id).remove();
  }
</script>

<table class="tbl main">
  <tr>
    <th class="title" colspan="10">
      Hors plage du {{$date|date_format:$conf.longdate}}
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
  <tr>
    <th>{{tr}}CSejour-patient_id{{/tr}}</th>
    <th>{{tr}}COperation-chir_id{{/tr}}</th>
    <th>{{tr}}COperation-anesth_id{{/tr}}</th>
    <th>{{tr}}COperation-time_operation{{/tr}}</th>
    <th>{{tr}}CSalle{{/tr}}</th>
    {{if "dPsalleOp hors_plage type_anesth"|conf:"CGroups-$g"}}
      <th>{{tr}}COperation-type_anesth{{/tr}}</th>
    {{/if}}
    {{if "dPsalleOp hors_plage heure_entree_sejour"|conf:"CGroups-$g"}}
      <th>{{tr}}CSejour-entree{{/tr}}</th>
    {{/if}}
    <th>{{tr}}COperation{{/tr}}</th>
    <th>{{tr}}COperation-cote{{/tr}}</th>
    <th>{{tr}}COperation-rques{{/tr}}</th>
  </tr>
  {{foreach from=$urgences item=_op}}
    <tr id="hors_plage_{{$_op->_id}}">
      {{mb_include module=salleOp template=inc_line_hors_plage op=$_op to_remove=false}}
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10" class="empty">{{tr}}COperation.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>