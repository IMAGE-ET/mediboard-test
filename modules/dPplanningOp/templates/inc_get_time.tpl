{{if $javascript}}
<script type="text/javascript">
periodicalTimeUpdater.currentlyExecuting = true;
</script>
{{/if}}

{{if $temps}}
  <i>{{tr}}msg-COperation-EstimateTime{{/tr}} : {{$temps}}</i>
{{else}}
  <i>{{tr}}msg-COperation-EstimateTime{{/tr}} : -</i>
{{/if}}