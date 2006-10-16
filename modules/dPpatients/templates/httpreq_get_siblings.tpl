<script language="javascript" type="text/javascript">
etatGetSiblings = function(){
{{if $textSiblings}}
if (confirm("{{$textSiblings|smarty:nodefaults|escape:"javascript"}}")) {
{{/if}}
  document.editFrm.submit();
{{if $textSiblings}}
}
{{/if}}
}
etatGetSiblings();
</script>