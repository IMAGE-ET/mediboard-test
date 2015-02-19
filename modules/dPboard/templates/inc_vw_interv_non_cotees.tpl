{{mb_script module="dPplanningOp" script="operation"}}

<script>
updateActes = function() {
  var url = new Url("board", "ajax_list_interv_non_cotees");
  url.addParam("praticien_id", "{{$chirSel}}");
  url.addParam("all_prats"   , "{{$all_prats}}");
  url.addParam("debut"       , "{{$debut}}");
  url.addParam("fin"         , "{{$fin}}");
  url.requestUpdate("list_interv_non_cotees");
};

popupExport = function() {
  var formFrom = getForm('changeDate');
  var formTo = getForm('exportCotationSalleOp');
  $V(formTo.debut, $V(formFrom.debut));
  $V(formTo.fin, $V(formFrom.fin));
  formTo.submit();
};

Main.add(function() {
  var form = getForm('changeDate');
  Calendar.regField(form.debut);
  Calendar.regField(form.fin);
  updateActes();
});
</script>

<form name="exportCotationSalleOp" method="get" target="_blank">
  <input type="hidden" name="m" value="board" />
  <input type="hidden" name="a" value="ajax_list_interv_non_cotees" />
  <input type="hidden" name="dialog" value="1" />
  <input type="hidden" name="suppressHeaders" value="1" />
  <input type="hidden" name="debut" />
  <input type="hidden" name="fin" />
  <input type="hidden" name="all_prats" value="{{$all_prats}}"/>
  <input type="hidden" name="chirSel" value="{{$chirSel}}"/>
  <input type="hidden" name="export" value="1"/>
</form>

<form name="changeDate" method="get" action="?">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="vw_interv_non_cotees" />
  <table class="form">
    <tr>
      <th colspan="3" class="title">
        Critères de filtre
        <button type="button" class="hslip" onclick="popupExport();" style="float: right;">{{tr}}Export-CSV{{/tr}}</button>
      </th>
    </tr>
    <tr>
      <td>
        A partir du
        <input type="hidden" name="debut" value="{{$debut}}" class="date notNull" onchange="this.form.submit()"/>
      </td>
      <td>
        jusqu'au
        <input type="hidden" name="fin" value="{{$fin}}" class="date notNull" onchange="this.form.submit()"/>
      </td>
    </tr>
  </table>
</form>

<div id="list_interv_non_cotees">
</div>