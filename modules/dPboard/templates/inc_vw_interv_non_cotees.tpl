{{mb_script module="dPplanningOp" script="operation"}}

<script type="text/javascript">

updateActes = function() {
  var url = new Url("board", "ajax_list_interv_non_cotees");
  url.addParam("praticien_id", "{{$chirSel}}");
  url.addParam("all_prats"   , "{{$all_prats}}");
  url.addParam("debut"       , "{{$debut}}");
  url.addParam("fin"         , "{{$fin}}");
  url.requestUpdate("list_interv_non_cotees");
};

Main.add(function() {
  var form = getForm('changeDate');
  Calendar.regField(form.debut);
  Calendar.regField(form.fin);
  updateActes();
});

</script>
<form name="changeDate" method="get" action="?">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="vw_interv_non_cotees" />
  <table class="form">
    <tr>
      <th colspan="3" class="title">
        Critères de filtre
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