{{mb_default var=canFile value=0}}
{{mb_default var=canDoc  value=0}}

{{assign var=object_guid value="$object_class-$object_id"}}
{{if $canFile && !$accordDossier}}
  <button style="float: left" class="new" type="button" onclick="uploadFile('{{$object_guid}}')">
   {{tr}}CFile-title-create{{/tr}}
  </button>

  <button style="float:left;" class="new" type="button" onclick="File.createMozaic('{{$object_guid}}', '', reloadAfterUploadFile);">{{tr}}CFile-create-mozaic{{/tr}}</button>
  
  {{if $app->user_prefs.directory_to_watch}}
    <button class="new yopletbutton" style="float: left" type="button" disabled="disabled"
      onclick="File.applet.modalOpen('{{$object_guid}}')">
      {{tr}}Upload{{/tr}}
    </button>
  {{/if}}
  {{if $object && $object->_nb_cancelled_files}}
    <button class="hslip" onclick="showCancelled(this)">Voir / Masquer {{$object->_nb_cancelled_files}} fichier(s) annulé(s)</button>
  {{/if}}
{{/if}}
{{if $canDoc}}
  <div style="float: left" id="document-add-{{$object_guid}}"></div>
  <script type="text/javascript">
  Main.add(function () {
    Document.register('{{$object_id}}', '{{$object_class}}', '{{$praticienId}}', "document-add-{{$object_guid}}", "hide");
  });
  </script>
{{/if}}