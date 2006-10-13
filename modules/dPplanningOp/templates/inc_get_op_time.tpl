<script type="text/javascript">

periodicalTimeUpdater.currentlyExecuting = true;

</script>

{{if $temps}}
  <i>Temps estimé : {{$temps|date_format:"%Hh%M"}}</i>
{{else}}
  <i>Temps estimé : -</i>
{{/if}}