{{if $can->admin}}
  <button class="hslip"
          onclick="window.open('?m=dPcompteRendu&amp;a=aides_export_csv&amp;suppressHeaders=1&amp;owner={{$owner}}&amp;object_class={{$filter_class}}{{foreach from=$aides item=_aide}}&amp;id[]={{$_aide->_id}}{{/foreach}}')">
       Exporter au format CSV
  </button>
  
  <button onclick="return popupImport('{{$owner->_guid}}');" class="hslip">{{tr}}Import-CSV{{/tr}}</button>
{{/if}}

{{mb_include module=system template=inc_pagination change_page="changePage['$type']" total=$aidesCount.$type current=$start.$type step=30}}

<table class="tbl">
<tr>
  <th>{{mb_title class=CAideSaisie field=class}}</th>
  <th>{{mb_title class=CAideSaisie field=field}}</th>
  <th>{{mb_title class=CAideSaisie field=depend_value_1}}</th>
  <th>{{mb_title class=CAideSaisie field=depend_value_2}}</th>
  <th>{{mb_title class=CAideSaisie field=name}}</th>
  <th>{{mb_title class=CAideSaisie field=text}}</th>
  <th style="width: 0.1%;"></th>
</tr>

{{foreach from=$aides item=_aide}}
<tr>
  {{assign var="aide_id" value=$_aide->aide_id}}
  {{assign var="href" value="?m=dPcompteRendu&tab=vw_idx_aides&aide_id=$aide_id"}}
  {{assign var="class" value=$_aide->class}}
  {{assign var="field" value=$_aide->field}}
  <td><a href="{{$href}}">{{tr}}{{$class}}{{/tr}}</a></td>
  <td><a href="{{$href}}">{{tr}}{{$class}}-{{$field}}{{/tr}}</a></td>
  <td>
    {{if $_aide->_depend_field_1}}
      <form name="edit-CAidesSaisie-depend1" action="" method="post" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="dPcompteRendu" />
        <input type="hidden" name="dosql" value="do_aide_aed" />
        {{mb_key object=$_aide}}
        
        <select onchange="this.form.onsubmit()" name="depend_value_1" 
                onmousedown="getListDependValues(this, '{{$class}}', '{{$_aide->_depend_field_1}}')" >
          <option value="{{$_aide->depend_value_1}}">
            {{if $_aide->depend_value_1}}
              {{tr}}{{$class}}.{{$_aide->_depend_field_1}}.{{$_aide->depend_value_1}}{{/tr}}
            {{else}}
              - {{tr}}None{{/tr}}
            {{/if}}
          </option>
        </select>
      </form>
    {{else}}
      &mdash;
    {{/if}}
  </td>
  <td>
    {{if $_aide->_depend_field_2}}
      <form name="edit-CAidesSaisie-depend2" action="" method="post" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="dPcompteRendu" />
        <input type="hidden" name="dosql" value="do_aide_aed" />
        {{mb_key object=$_aide}}
        
        <select onchange="this.form.onsubmit()" name="depend_value_2" 
                onmousedown="getListDependValues(this, '{{$class}}', '{{$_aide->_depend_field_2}}')" >
          <option value="{{$_aide->depend_value_2}}">
            {{if $_aide->depend_value_2}}
              {{tr}}{{$class}}.{{$_aide->_depend_field_2}}.{{$_aide->depend_value_2}}{{/tr}}
            {{else}}
              - {{tr}}None{{/tr}}
            {{/if}}
          </option>
        </select>
      </form>
    {{else}}
      &mdash;
    {{/if}}
  </td>
  
  <td class="text"><a href="{{$href}}">{{mb_value object=$_aide field=name}}</a></td>
  <td class="text" title="{{$_aide->text}}">{{mb_value object=$_aide field=text truncate=60}}</td>
    
  <td>
    <form name="delete-{{$_aide->_guid}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="dosql" value="do_aide_aed" />
      {{mb_key object=$_aide}}
      <button class="trash notext" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'aide',objName:'{{$_aide|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Delete{{/tr}}
      </button>
    </form>
  </td>
</tr>
{{foreachelse}}
<tr>
  <td colspan="10"><em>{{tr}}CAideSaisie.none{{/tr}}</em></td>
</tr>
{{/foreach}}

</table>