<table class="tbl" style="text-align: center;">
<tr>
  <th rowspan="2" class="title" style="width: 8em;">{{mb_label class=CFile field=file_category_id}}</th>
  {{foreach from=$classes item=_class}}
  <th colspan="2" class="title" style="width: 8em;">{{tr}}{{$_class}}{{/tr}}</th>
  {{/foreach}}
  <th colspan="2" class="narrow title" style="width: 8em;">{{tr}}Total{{/tr}}</th>
</tr>
<tr>
  {{foreach from=$class_totals key=_class item=_totals}}
  <th style="width: 4em;">{{tr}}CFile-_count-court{{/tr}}</th>
  <th style="width: 4em;">{{tr}}CFile-_total_weight-court{{/tr}}</th>
  {{/foreach}}
  <th style="width: 4em;">{{tr}}CFile-_count-court{{/tr}}</th>
  <th style="width: 4em;">{{tr}}CFile-_total_weight-court{{/tr}}</th>
</tr>

{{foreach from=$category_totals key=_category_id item=_totals}}
<tr>
  <th>
    {{if $_category_id}}
      {{assign var=category value=$categories.$_category_id}}
      {{$category}}
    {{else}}
      <em>{{tr}}None{{/tr}}e</em>
    {{/if}}
  </th>
  
  {{foreach from=$classes item=_class}}
    {{if $_category_id && $category->class && $category->class != $_class}}
    <td colspan="2" class="arretee"></td>
    {{elseif !@$details.$_category_id.$_class}}
    <td colspan="2"></td>
    {{else}}
    {{assign var=detail value=$details.$_category_id.$_class}}
    <td>{{$detail.count}}</td>
    <td>{{$detail.weight|decabinary}}</td>
    {{/if}}
  {{/foreach}}
  
  {{assign var=totals value=$category_totals.$_category_id}}
  <td><strong>{{$totals.count}}</strong></td>
  <td><strong>{{$totals.weight|decabinary}}</strong></td>
</tr>
{{/foreach}}
<tr>
  <th>{{tr}}Total{{/tr}}</th>
  {{foreach from=$classes item=_class}}
  {{assign var=totals value=$class_totals.$_class}}
  <td><strong>{{$totals.count}}</strong></td>
  <td><strong>{{$totals.weight|decabinary}}</strong></td>
  {{/foreach}}
  <td class="ok"><strong>{{$big_totals.count}}</strong></td>
  <td class="ok"><strong>{{$big_totals.weight|decabinary}}</strong></td>
</tr>

</table>