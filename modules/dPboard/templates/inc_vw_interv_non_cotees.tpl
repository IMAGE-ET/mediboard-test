{{mb_script module="dPplanningOp" script="operation"}}

<script>
updateActes = function() {
  var url = new Url("board", "ajax_list_interv_non_cotees");
  url.addParam("praticien_id"        , "{{$chirSel}}");
  url.addParam("all_prats"           , "{{$all_prats}}");
  url.addParam("debut"               , "{{$debut}}");
  url.addParam("fin"                 , "{{$fin}}");
  url.addParam('interv_with_no_codes', '{{$interv_with_no_codes}}');
  url.addParam('display_not_exported', '{{$display_not_exported}}');
  url.requestUpdate("list_interv_non_cotees");
};

popupExport = function() {
  var formFrom = getForm('changeDate');
  var formTo = getForm('exportCotationSalleOp');
  $V(formTo.debut, $V(formFrom.debut));
  $V(formTo.fin, $V(formFrom.fin));
  formTo.submit();
};

toggleValueCheckbox = function(checkbox, element) {
  $V(element, checkbox.checked ? 1 : 0);
}

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
      <th colspan="4" class="title">
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
      <td>
        Afficher les interventions/consultations sans codes CCAM
        <input type="checkbox" name="_cb_interv_with_no_codes"{{if $interv_with_no_codes}} checked="checked"{{/if}} onchange="toggleValueCheckbox(this, this.form.interv_with_no_codes);"/>
        <input type="hidden" name="interv_with_no_codes" value="{{$interv_with_no_codes}}" onchange="this.form.submit();"/>
      </td>
      <td>
        Afficher les actes non exportés
        <input type="checkbox" name="_cb_display_not_exported"{{if $display_not_exported}} checked="checked"{{/if}} onchange="toggleValueCheckbox(this, this.form.display_not_exported);"/>
        <input type="hidden" name="display_not_exported" value="{{$display_not_exported}}" onchange="this.form.submit();"/>
      </td>
    </tr>
  </table>
</form>

<div id="list_interv_non_cotees">
</div>