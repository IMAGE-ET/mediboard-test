<table class="tbl" style="text-align: center;">
<tr>
  <th rowspan="2" class="title text" style="width: 8em;">{{mb_label class=CFile field=file_category_id}}</th>
  {{foreach from=$classes item=_class}}
  <th class="title text" style="width: 8em;">{{tr}}{{$_class}}{{/tr}}</th>
  {{/foreach}}
  <th  class="narrow title" style="width: 8em;">{{tr}}Total{{/tr}}</th>
</tr>
<tr>
  {{foreach from=$class_totals key=_class item=_totals}}
  <th style="width: 4em;">{{tr}}CFile-_count-court{{/tr}} / {{tr}}CFile-_total_weight-court{{/tr}}</th>
  {{/foreach}}
  <th style="width: 4em;">{{tr}}CFile-_count-court{{/tr}} / {{tr}}CFile-_total_weight-court{{/tr}}</th>
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
    <td class="arretee"></td>
    {{elseif !@$details.$_category_id.$_class}}
    <td></td>
    {{else}}
    {{assign var=detail value=$details.$_category_id.$_class}}
    <td>{{$detail.count|integer}}<br/>{{$detail.weight|decabinary}}</td>
    {{/if}}
  {{/foreach}}
  
  {{assign var=totals value=$category_totals.$_category_id}}
  <td><strong>{{$totals.count|integer}}</strong><br /><strong>{{$totals.weight|decabinary}}</strong></td>
</tr>
{{/foreach}}
<tr>
  <th>{{tr}}Total{{/tr}}</th>
  {{foreach from=$classes item=_class}}
  {{assign var=totals value=$class_totals.$_class}}
  <td><strong>{{$totals.count|integer}}</strong><br /><strong>{{$totals.weight|decabinary}}</strong></td>
  {{/foreach}}
  <td class="ok"><strong>{{$big_totals.count|integer}}</strong><br /><strong>{{$big_totals.weight|decabinary}}</strong></td>
</tr>

</table>

<hr />

<table class="tbl">
  <tr>
    <th class="title narrow">Types de périodes</th>
    <th class="title" colspan="{{$periodical_details.yearly|@count}}">Période</th>
  </tr>
  {{foreach from=$periodical_details key=_period_type item=_details}}
    <tr>
      <th rowspan="2">{{tr}}{{$_period_type}}{{/tr}}</th>
      {{foreach from=$_details key=_period item=_detail}}
      <th>{{$_period}}</th>
      {{/foreach}}
    </tr>
    <tr style="text-align: center;">
      {{foreach from=$_details key=_period item=_detail name=details}}
        {{assign var=opacity value=$smarty.foreach.details.last|ternary:'opacity-50':0}}
        {{if !$_detail.count}}
          <td class="arretee {{$opacity}} empty">{{tr}}None{{/tr}}</td>
        {{else}}
          <td class="{{$opacity}}">{{$_detail.count|integer}}<br/>{{$_detail.weight|decabinary}}</td>
        {{/if}}
      {{/foreach}}
    <tr>
  {{/foreach}}
</table>