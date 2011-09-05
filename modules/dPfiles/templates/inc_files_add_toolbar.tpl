{{if $canFile->edit && !$accordDossier}}

  <button style="float: left" class="new" type="button" onclick="uploadFile('{{$object_class}}', '{{$object_id}}')">
   {{tr}}CFile-title-create{{/tr}}
  </button>
  
  {{if $app->user_prefs.directory_to_watch}}
    <button class="new yopletbutton" style="float: left" type="button" disabled="disabled"
      onclick="File.applet.modalOpen('{{$object_class}}-{{$object_id}}')">
      {{tr}}Upload{{/tr}}
    </button>
  {{/if}}
  <div style="float: left" id="document-add-{{$object_class}}-{{$object_id}}"></div>
  
  <script type="text/javascript">
  Main.add(function () {
    Document.register('{{$object_id}}', '{{$object_class}}', '{{$praticienId}}', "document-add-{{$object_class}}-{{$object_id}}", "hide");
  });
  </script>

{{/if}}