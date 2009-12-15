
{{foreach from=$stats item=_stat}}
<tr>
  <td style="text-align: right;">{{$_stat.files_count}}</td>
  <td style="text-align: right;">{{$_stat._files_count_percent|percent}}</td>
  <td style="text-align: right;">{{$_stat._files_weight}}</td>
  <td style="text-align: right;">{{$_stat._files_weight_percent|percent}}</td>
  <td style="text-align: right;">{{$_stat._file_average_weight}}</td>
  <td>
    {{assign var=owner value=$_stat._ref_owner}}
    {{if $owner instanceof CMediusers}}
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$owner}}
    {{elseif $owner instanceof CFunctions}}
      {{mb_include module=mediusers template=inc_vw_function function=$owner}}
    {{else}}
      {{$_stat.user_first_name}} {{$_stat.user_last_name}}
    {{/if}}     
  </td>
</tr>

{{foreachelse}}
<tr>
  <td><em>{{tr}}CFile.none{{/tr}}</em></td>
</tr>
{{/foreach}}
