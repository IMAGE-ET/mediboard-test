{{mb_default var=canFile value=0}}
{{mb_default var=canDoc  value=0}}
{{mb_default var=mozaic  value=0}}

<div id="button_toolbar">
  {{assign var=object_guid value="$object_class-$object_id"}}
  {{if $canFile && !$accordDossier}}
    <button style="float: left" class="new" type="button" onclick="uploadFile('{{$object_guid}}')">
     {{tr}}CFile-title-create{{/tr}}
    </button>

    {{if $mozaic}}
      <button style="float:left;" class="new" type="button" onclick="File.createMozaic('{{$object_guid}}', '', reloadAfterUploadFile);">{{tr}}CFile-create-mozaic{{/tr}}</button>
    {{/if}}

    {{if "drawing"|module_active}}
      <button style="float:left;" class="drawing" type="button" onclick="editDrawing(null, null, '{{$object_guid}}', reloadAfterUploadFile);">{{tr}}CDrawingItem.new{{/tr}}</button>
    {{/if}}

    {{if "mbHost"|module_active && $app->user_prefs.upload_mbhost}}
      {{mb_script module=mbHost script=mbHost ajax=true}}
      {{mb_script module=files  script=filembhost ajax=true}}

      <button class="new mbhostbutton" style="float: left" type="button" disabled
              onclick="FileMbHost.modalUpload('{{$object_guid}}')">
        {{tr}}Upload{{/tr}}
      </button>

      <form name="sendFile{{$object_guid}}" method="post" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="files" />
        <input type="hidden" name="dosql" value="do_send_file" />
        <input type="hidden" name="content" />
        <input type="hidden" name="file_name" />
        <input type="hidden" name="object_class" value="{{$object_class}}" />
        <input type="hidden" name="object_id" value="{{$object_id}}" />
      </form>

      <div style="display: none; margin: 5px; width: 880px; height: 700px;">
        <div id="mbhost_file_{{$object_guid}}"></div>
        <div style="text-align: center; margin-top: 5px;">
          <label style="float: left">
            <input type="checkbox" id="_del_file_{{$object_guid}}" checked /> Supprimer après envoi
          </label>
          <button class="submit singleclick" type="button" onclick="FileMbHost.sendFiles('{{$object_guid}}');">{{tr}}Send{{/tr}}</button>
          <button class="close" type="button" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
        </div>
      </div>
      <script>
        Main.add(function() {
          FileMbHost.extensions = '{{" "|str_replace:",":$conf.dPfiles.extensions_yoplet}}';
          FileMbHost.extensions_thumb = 'gif,jpeg,jpg,png';
          FileMbHost.periodicalUpdateCount();
        });
      </script>
    {{elseif $app->user_prefs.directory_to_watch}}
      <button class="new yopletbutton" style="float: left" type="button" disabled
        onclick="File.applet.modalOpen('{{$object_guid}}')">
        {{tr}}Upload{{/tr}}
      </button>
    {{/if}}
    {{if $object && ($object->_nb_cancelled_files || $object->_nb_cancelled_docs)}}
      <button class="hslip" onclick="showCancelled(this)">Voir / Masquer {{math equation=x+y x=$object->_nb_cancelled_files y=$object->_nb_cancelled_docs}} fichier(s) annulé(s)</button>
    {{/if}}
  {{/if}}
  {{if $canDoc}}
    <div style="float: left" id="document-add-{{$object_guid}}"></div>
    <script>
      Main.add(function() {
        Document.register('{{$object_id}}', '{{$object_class}}', '{{$praticienId}}', "document-add-{{$object_guid}}", "hide");
      });
    </script>
  {{/if}}
</div>