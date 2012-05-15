<script type="text/javascript">
  refreshListOperations = function(order_col, order_way) {
    var form = getForm("filterOperations");
    var url = new Url("dPbloc", "ajax_vw_departs_us");
    url.addParam("date_depart", $V(form.date_depart));
    url.addParam("bloc_id", $V(form.bloc_id));
    url.addParam("order_col", order_col);
    url.addParam("order_way", order_way);
    url.requestUpdate("list_operations"); 
  }
  
  Main.add(function() {
    Calendar.regField(getForm("filterOperations").date_depart);
  });
</script>

<form name="filterOperations" method="get">
  <table class="form">
    <tr>
      <th class="title" colspan="4">Filtre</th>
    </tr>
    <tr>
      <th>
        Date
      </th>
      <td>
        <input type="hidden" name="date_depart" class="date notNull" value="{{$date_depart}}"/>
      </td>
      <th>
        {{tr}}CBlocOperatoire{{/tr}}
      </th>
      <td>
        <select name="bloc_id">
          <option value="">&mdash; {{tr}}CBlocOperatoire.all{{/tr}}</option>
          {{foreach from=$blocs item=_bloc}}
            <option value="{{$_bloc->_id}}" {{if $bloc_id == $_bloc->_id}}selected="selected"{{/if}}>{{$_bloc}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <td colspan="4" class="button">
        <button type="button" class="tick" onclick="refreshListOperations('{{$order_col}}', '{{$order_way}}')">{{tr}}Validate{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
<br />

<table class="tbl">
  <tbody id="list_operations"></tbody>
</table>
