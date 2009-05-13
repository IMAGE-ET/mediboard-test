
<table class="main">
  <tr>
    <td style="width: 50%">
      {{if $can->edit}}
      <a class="button new" href="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}&amp;mark_id=0">
				{{tr}}CTriggerMark-create{{/tr}}
      </a>
      {{/if}}
      {{include file="inc_filter_marks.tpl"}}
      {{include file="inc_list_marks.tpl"}}
    </td>
    {{if $can->edit}}
    <td style="width: 50%">
      {{include file="inc_edit_mark.tpl"}}
    </td>
    {{/if}}
  </tr>
</table>