
{{foreach from=$stats item=_stat}}
<tr>
  <td style="text-align: right;">{{$_stat.docs_count|integer}}</td>
  <td style="text-align: right;">{{$_stat._docs_count_percent|percent}}</td>
  <td style="text-align: right;">{{$_stat.docs_weight|decabinary}}</td>
  <td style="text-align: right;">{{$_stat._docs_weight_percent|percent}}</td>
  <td style="text-align: right;">{{$_stat._docs_average_weight|decabinary}}</td>
  {{assign var=owner value=$_stat._ref_owner}}
  {{if !$owner->_id}}
  <td class="empty" colspan="2">{{tr}}None{{/tr}}</td>
  {{else}}
  <td>
    {{if $owner instanceof CMediusers}}
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$owner}}
    {{elseif $owner instanceof CFunctions}}
      {{mb_include module=mediusers template=inc_vw_function function=$owner}}
    {{elseif $owner instanceof CGroups}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$owner->_guid}}');">{{$owner}}</span>
    {{else}}
      {{$_stat.user_first_name}} {{$_stat.user_last_name}}
    {{/if}}     
  </td>
  <td>
    <button class="search notext compact" type="button" onclick="Details.statOwner('{{$doc_class}}', '{{$owner->_guid}}');">
      {{tr}}Details{{/tr}}
    </button>
  </td>
  {{/if}}
</tr>

{{foreachelse}}
<tr>
  <td class="empty">{{tr}}CFile.none{{/tr}}</td>
</tr>
{{/foreach}}
