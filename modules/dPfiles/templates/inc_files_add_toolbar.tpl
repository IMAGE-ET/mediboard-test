{{if $canFile->edit && !$accordDossier}}

  <button style="float: left" class="new" type="button" onclick="uploadFile('{{$selClass}}', '{{$selKey}}')">
   {{tr}}CFile-title-create{{/tr}}
  </button>
  
  {{if $app->user_prefs.directory_to_watch}}
    <button class="new yopletbutton" style="float: left" type="button" disabled="disabled"
      onclick="File.applet.modalOpen('{{$selClass}}-{{$selKey}}')">
      {{tr}}Upload{{/tr}}
    </button>
  {{/if}}
  <div style="float: left" id="document-add-{{$selClass}}-{{$selKey}}"></div>
  
  <script type="text/javascript">
  Main.add(function () {
    Document.register('{{$selKey}}', '{{$selClass}}', '{{$praticienId}}', "document-add-{{$selClass}}-{{$selKey}}", "hide");
  });
  </script>

{{/if}}