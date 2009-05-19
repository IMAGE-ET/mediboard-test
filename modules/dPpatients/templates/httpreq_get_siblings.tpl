<script type="text/javascript">
SiblingsChecker.textDifferent = "{{$textDifferent|smarty:nodefaults|escape:'javascript'}}";
SiblingsChecker.textMatching  = "{{$textMatching|smarty:nodefaults|escape:'javascript'}}";
{{if $dPconfig.dPpatients.CPatient.identitovigilence == "doublons"}}
SiblingsChecker.alert();
{{else}}
SiblingsChecker.confirm();
{{/if}}
</script>