<script type="text/javascript">
  checkMaxPeriod = function(elt) {
    var form = elt.form;
    var date_min_elt = form._date_min;
    var date_max_elt = form._date_max;
    var date_min = new Date($V(date_min_elt));
    var date_max = new Date($V(date_max_elt));
    
    if (date_min.format("yyyy-MM-dd") > date_max.format("yyyy-MM-dd")) {
      return;
    }
    
    if (elt.name == "_date_min" ) {
      if (date_max.format("yy-MM-dd") > date_min.addDays(31).format("yy-MM-dd")) {
        $V(date_max_elt, date_min.format("yyyy-MM-dd"), false);
        $V(form._date_max_da, date_min.format("dd/MM/yyyy"), false);
      }
    }
    else if (date_min.format("yy-MM-dd") < date_max.addDays(-31).format("yy-MM-dd")) {
      $V(date_min_elt, date_max.format("yyyy-MM-dd"), false);
      $V(form._date_min_da, date_max.format("dd/MM/yyyy"), false);
    }
  }
</script>
<form name="Filter" action="?" method="get" onsubmit="return checkForm(this);">
  
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />

<table class="form">
  <tr>
    <th class="title" colspan="4">
      Filtre de statistiques
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field=_function_id}}</th>
    <td>
      <select name="_function_id">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_function list=$functions selected=$filter->_function_id}}
      </select>
    </td>

    <th>{{mb_label object=$filter field=_date_min}}</th>
    <td>{{mb_field object=$filter field=_date_min form=Filter register=true canNull=false onchange="checkMaxPeriod(this)"}}</td>
  </tr>
  
  <tr>
    <th>
      {{mb_label object=$filter field=_other_function_id}}
    </th>
    <td>
      <select name="_other_function_id" class="{{$filter->_props._other_function_id}}">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_function list=$functions selected=$filter->_other_function_id}}
      </select>
    </td>

    <th>{{mb_label object=$filter field=_date_max}}</th>
    <td>{{mb_field object=$filter field=_date_max form=Filter register=true canNull=false onchange="checkMaxPeriod(this)"}}</td>
  </tr>
  <tr>
    <th>
      {{mb_label object=$filter field=_user_id}}
    </th>
    <td colspan="3">
      <select name="_user_id">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$users selected=$filter->_user_id}}
      </select>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="4">
      <button type="submit" class="change">
        {{tr}}Compute{{/tr}}
      </button>
    </td>
  </tr>
</table> 

</form>