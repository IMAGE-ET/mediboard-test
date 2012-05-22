<script type="text/javascript">
  Main.add(function(){
    var form = getForm("filter_{{$type}}");
    Calendar.regField(form.date_min);
    Calendar.regField(form.date_max);
  });
</script>

<form name="filter_{{$type}}" method="get" action="?"
  onsubmit="refreshStats('{{$type}}', $V(this.date_min), $V(this.date_max), $V(this.service_id)); return false;">
  <table class="form">
    <tr>
      <th colspan="3" class="category">Critères de filtre</th>
    </tr>
    <tr>
      <td>
        A partir du <input type="hidden" name="date_min" class="date notNull" value="{{$date_min}}" onchange="this.form.onsubmit();"/>
      </td>
      <td>
        Jusqu'au <input type="hidden" name="date_max" class="date notNull" value="{{$date_max}}" onchange="this.form.onsubmit();"/>
      </td>
      <td>
        Service
        <select name="service_id" onchange="this.form.onsubmit();">
          <option value="">&mdash; Tous les services</option>
          {{foreach from=$services item=_service}}
            <option value="{{$_service->_id}}" {{if $_service->_id == $service_id}}selected="selected"{{/if}}>{{$_service}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
  </table>
</form>