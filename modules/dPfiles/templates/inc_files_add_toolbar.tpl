{{if $canFile->edit && !$accordDossier}}
<button class="new" type="button" onclick="uploadFile('{{$selClass}}', '{{$selKey}}')">
 {{tr}}CFile-title-create{{/tr}}
</button>

{{if $praticienId}}
<span id="document-add-{{$selClass}}-{{$selKey}}"></span>
<script type="text/javascript">
Main.add(function () {
  Document.register('{{$selKey}}', '{{$selClass}}', '{{$praticienId}}', "document-add-{{$selClass}}-{{$selKey}}", "hide");
});
</script>
{{/if}}
{{/if}}