{{if $canFile->edit && !$accordDossier}}

<button style="float: left" class="new" type="button" onclick="uploadFile('{{$selClass}}', '{{$selKey}}')">
 {{tr}}CFile-title-create{{/tr}}
</button>

<div style="float: left" id="document-add-{{$selClass}}-{{$selKey}}"></div>

<script type="text/javascript">
Main.add(function () {
  Document.register('{{$selKey}}', '{{$selClass}}', '{{$praticienId}}', "document-add-{{$selClass}}-{{$selKey}}", "hide");
});
</script>

{{/if}}