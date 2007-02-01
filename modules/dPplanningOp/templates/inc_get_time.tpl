{{if $javascript}}
<script type="text/javascript">
periodicalTimeUpdater.currentlyExecuting = true;
</script>
{{/if}}

{{if $temps}}
  <i>Temps estimé : {{$temps}}</i>
{{else}}
  <i>Temps estimé : -</i>
{{/if}}