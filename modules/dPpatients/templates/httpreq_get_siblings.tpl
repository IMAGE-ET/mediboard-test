<script type="text/javascript">
SiblingsChecker.textSiblings = "{{$textSiblings|smarty:nodefaults|escape:'javascript'}}";
SiblingsChecker.textMatching  = "{{$textMatching|smarty:nodefaults|escape:'javascript'}}";
{{if $dPconfig.dPpatients.CPatient.identitovigilence == "doublons"}}
SiblingsChecker.alert();
{{else}}
SiblingsChecker.confirm();
{{/if}}
</script>