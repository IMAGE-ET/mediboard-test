
<script type="text/javascript">
Main.add(function(){
  var form = getForm("filter-ex_object");
  Calendar.regField(form.date_min);
  Calendar.regField(form.date_max);
});

selectConcept = function(field) {
  var id = field.value;
  
  var url = new Url("forms","ajax_concept_value_choser");
  url.addParam("concept_id", id);
  url.requestUpdate("concept-value-chose");
}
</script>

<table class="main layout">
  <tr>
    <td class="narrow">
      <form name="filter-ex_object" method="get" onsubmit="return Url.update(this, 'list-ex_object-counts')">
        <input type="hidden" name="m" value="forms" />
        <input type="hidden" name="a" value="ajax_list_ex_object_counts" />
        
        <table class="main form">
          <tr>
            <th class="narrow">Date min</th>
            <td class="narrow"><input type="hidden" name="date_min" value="{{$date_min}}" class="date" /></td>
          </tr>
          <tr>
            <th>Date max</th>
            <td><input type="hidden" name="date_max" value="{{$date_max}}" class="date" /></td>
          </tr>
          <tr>
            <th class="narrow">{{tr}}CGroups{{/tr}}</th>
            <td>
              <select name="group_id">
                {{foreach from=$groups item=_group}}
                  <option value="{{$_group->_id}}" {{if $_group->_id == $g}} selected="selected" {{/if}}>{{$_group}}</option>
                {{/foreach}}
              </select>
            </td>
          </tr>
          <tr>
            <td></td>
            <td><button type="submit" class="search">Filtrer</button></td>
          </tr>
        </table>
      </form>
    </td>
    <td>
      <form name="filter-concept-value" method="post" style="display: none;">
        <fieldset>
          <legend>Valeurs</legend>
          <table class="main layout">
            <tr>
              <td class="narrow">
                {{mb_label object=$field field=concept_id}}
                {{mb_field object=$field field=concept_id form="filter-concept-value" autocomplete="true,1,50,true,true" onchange="selectConcept(this)"}}
                <div id="concept-value-chose"></div>
              </td>
              <td id="concept-value-"></td>
            </tr>
          </table>
        </fieldset>
      </form>
    </td>
  </tr>
</table>

<table class="main layout">
  <tr>
    <td id="list-ex_object-counts" style="width: 20%;"></td>
    <td id="list-ex_object">&nbsp;</td>
  </tr>
</table>
