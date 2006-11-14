<script language="javascript" type="text/javascript">
etatGetSiblings = function(){
{{if $textDifferent}}
if (confirm("{{$textDifferent|smarty:nodefaults|escape:"javascript"}}")) {
{{/if}}
{{if $textSiblings}}
if (confirm("{{$textSiblings|smarty:nodefaults|escape:"javascript"}}")) {
{{/if}}
  document.editFrm.submit();
{{if $textSiblings}}
}
{{/if}}
{{if $textDifferent}}
}
{{/if}}
}
etatGetSiblings();
</script>