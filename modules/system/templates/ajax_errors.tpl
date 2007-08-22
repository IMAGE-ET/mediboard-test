{{if !$app->user_id}}

<div class="error">{{tr}}Veuillez vous reconnecter{{/tr}}</div>
<script type="text/javascript">
AjaxResponse.onDisconnected();
</script>

{{else}}

<script type="text/javascript">
AjaxResponse.onPerformances({{$performance|@json}});
</script>

{{/if}}
  