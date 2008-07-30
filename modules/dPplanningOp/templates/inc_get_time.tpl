{{if $javascript}}
<script type="text/javascript">
periodicalTimeUpdater.currentlyExecuting = true;
</script>
{{/if}}

{{if $temps}}
  <i>{{tr}}COperation-msg-EstimateTime{{/tr}} : {{$temps}}</i>
{{else}}
  <i>{{tr}}COperation-msg-EstimateTime{{/tr}} : -</i>
{{/if}}