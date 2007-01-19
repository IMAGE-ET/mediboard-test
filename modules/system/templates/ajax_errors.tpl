{{if !$app->user_id}}

<div class="error">{{tr}}Disconnected{{/tr}}</div>
<script language="Javascript" type="text/javascript">
AjaxResponse.onDisconnected();
</script>

{{else}}

<script language="Javascript" type="text/javascript">
AjaxResponse.onPerformances({{$performance|@json}});
</script>

{{/if}}}
  