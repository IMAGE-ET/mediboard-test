{{if $can->admin}}
  <script>
    exportAidesCSV = function(owner, object_class, aides_ids){
      var url = new Url("compteRendu", "aides_export_csv", "raw");
      url.addParam("owner",        owner);
      url.addParam("object_class", object_class);
      url.pop(400, 300, "export_csv", null, null, {
        id: aides_ids.join("-"),
        owner: owner,
        object_class: object_class
      });
    };
  </script>
  <button class="hslip"
          onclick="exportAidesCSV('{{$owner|escape:"javascript"}}', '{{$filter_class}}', {{$aides_ids|@json}})">
    {{tr}}Export-CSV{{/tr}}
  </button>
  
  <button onclick="return popupImport('{{$owner->_guid}}');" class="hslip">{{tr}}Import-CSV{{/tr}}</button>
{{/if}}

{{mb_include module=system template=inc_pagination change_page="changePage['$type']" total=$aidesCount.$type current=$start.$type step=30}}

<table class="tbl">
<tr>
  <th>{{mb_colonne class=CAideSaisie field=class order_col=$order_col_aide order_way=$order_way function=sortBy}}</th>
  <th>{{mb_colonne class=CAideSaisie field=field order_col=$order_col_aide order_way=$order_way function=sortBy}}</th>
  <th class="narrow">{{mb_colonne class=CAideSaisie field=depend_value_1 order_col=$order_col_aide order_way=$order_way function=sortBy}}</th>
  <th class="narrow">{{mb_colonne class=CAideSaisie field=depend_value_2 order_col=$order_col_aide order_way=$order_way function=sortBy}}</th>
  <th>{{mb_colonne class=CAideSaisie field=name order_col=$order_col_aide order_way=$order_way function=sortBy}}</th>
  <th>{{mb_title class=CAideSaisie field=text}}</th>
  <th class="narrow"></th>
</tr>

{{foreach from=$aides item=_aide}}
<tr {{if $_aide->_id == $aide_id}}class="selected"{{/if}}>
  {{assign var="href" value="?m=dPcompteRendu&tab=vw_idx_aides&aide_id=`$_aide->_id`"}}
  {{assign var="class" value=$_aide->class}}
  {{assign var="field" value=$_aide->field}}
  <td class="text"><a href="{{$href}}">{{tr}}{{$class}}{{/tr}}</a></td>
  <td class="text"><a href="{{$href}}">{{tr}}{{$class}}-{{$field}}{{/tr}}</a></td>
  <td>
    {{if $_aide->_depend_field_1 && !$_aide->_is_ref_dp_1}}
      <form name="edit-CAidesSaisie-depend1-{{$_aide->_id}}" action="" method="post" onsubmit="return onSubmitFormAjax(this)">
        {{mb_class object=$_aide}}
        {{mb_key   object=$_aide}}
        
        <select 
          style="width: 10em;"
          onchange="this.form.onsubmit()" 
          name="depend_value_1" 
          onmouseover="getListDependValues(this, '{{$class}}', '{{$_aide->_depend_field_1}}')"
        >
          <option value="{{$_aide->depend_value_1}}">
            {{if $_aide->depend_value_1}}
              {{tr}}{{$class}}.{{$_aide->_depend_field_1}}.{{$_aide->depend_value_1}}{{/tr}}
            {{else}}
              &mdash; {{tr}}None{{/tr}}
            {{/if}}
          </option>
        </select>
      </form>
    {{elseif $_aide->_is_ref_dp_1}}
      {{$_aide->_vw_depend_field_1}}
    {{else}}
      &mdash;
    {{/if}}
  </td>
  <td>
    {{if $_aide->_depend_field_2 && !$_aide->_is_ref_dp_2}}
      <form name="edit-CAidesSaisie-depend2-{{$_aide->_id}}" action="" method="post" onsubmit="return onSubmitFormAjax(this)">
        {{mb_class object=$_aide}}
        {{mb_key   object=$_aide}}
        
        <select 
          style="width: 10em;"
          onchange="this.form.onsubmit()" 
          name="depend_value_2" 
          onmouseover="getListDependValues(this, '{{$class}}', '{{$_aide->_depend_field_2}}')"
        >
          <option value="{{$_aide->depend_value_2}}">
            {{if $_aide->depend_value_2}}
              {{tr}}{{$class}}.{{$_aide->_depend_field_2}}.{{$_aide->depend_value_2}}{{/tr}}
            {{else}}
              &mdash; {{tr}}None{{/tr}}
            {{/if}}
          </option>
        </select>
      </form>
    {{elseif $_aide->_is_ref_dp_2}}
      {{$_aide->_vw_depend_field_2}}
    {{else}}
      &mdash;
    {{/if}}
  </td>
  
  <td class="text"><a href="{{$href}}">{{mb_value object=$_aide field=name}}</a></td>
  <td class="text compact" title="{{$_aide->text}}">
    <div style="float: right;">
      {{mb_include module=sante400 template=inc_hypertext_links object=$_aide}}
    </div>
    {{mb_value object=$_aide field=text truncate=60}}
  </td>
    
  <td>
    <form name="delete-{{$_aide->_guid}}" action="?m={{$m}}" method="post">
      {{mb_class object=$_aide}}
      {{mb_key   object=$_aide}}
      <input type="hidden" name="del" value="1" />

      <button class="trash notext" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'aide',objName:'{{$_aide->_view|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Delete{{/tr}}
      </button>
    </form>
  </td>
</tr>
{{foreachelse}}
<tr>
  <td colspan="10" class="empty">{{tr}}CAideSaisie.none{{/tr}}</td>
</tr>
{{/foreach}}

</table>