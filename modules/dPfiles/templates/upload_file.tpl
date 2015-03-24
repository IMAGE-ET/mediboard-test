<script>
  showLoading = function(){
    var systemMsg = $('systemMsg');
    systemMsg.update(DOM.div({className: "loading"}, "{{tr}}Loading in progress{{/tr}}"));
    systemMsg.show();
  };

  var count_files = 0;
  var count_datauri = 0;

  // Les images ne sont pas converties en PDF,
  // donc le bouton de fusion reste grisé tant que l'on rajoute une image
  var extensions = ["bmp", "gif", "jpeg", "jpg", "png"];

  addFile = function(elt) {
    {{if $conf.dPfiles.CFile.merge_to_pdf}}
      var add_and_merge = $("add_and_merge");

      var oForm = getForm("uploadFrm");

      if (add_and_merge.disabled) {
        var name_file = oForm.elements["formfile["+count_files+"]"].value;
        var extension = name_file.substring(name_file.lastIndexOf(".")+1);

        if (extensions.indexOf(extension) == -1) {
          add_and_merge.disabled = "";
        }
      }
    {{/if}}

    count_files++;

    // Ajout d'un input pour le fichier suivant
    // <input type="file" name="formfile[0]" size="0" onchange="addFile(this); this.onchange=''"/>
    var tr = DOM.tr({},
      DOM.td({colSpan: "2"},
        DOM.input({type: "file", name: "formfile["+count_files+"]", size: 0, onchange: "addFile(this); this.onchange=''"})
      )
    );
    $("files-list").insert(tr);
  };

  Main.add(function(){
    if (window.clipboardData || Prototype.Browser.Gecko) {
      return;
    }

    $("image-paster").show();

    var form = getForm("uploadFrm");
    var matchType = /image\/(.*)/i;
    form.pastearea.observe("paste", function(e){
      var items = [];

      if (e.clipboardData) {
        items = e.clipboardData.items || e.clipboardData.files || [];
      }

      var found = false;
      for (var i = 0, l = items.length; i < l; i++) {
        var item = items[i];
        var matches = item.type.match(matchType);
        if (matches) {
          found = true;

          var file = item.getAsFile();
          var reader = new FileReader();

          reader.onload = function(evt) {
            var img, dataInput, nameInput;
            var id = form.name+"_formdatauri_name["+count_datauri+"]";

            var tr = DOM.tr({},
              DOM.td({className: "narrow"},
                DOM.label(
                  {
                    htmlFor: "formdatauri_name["+count_datauri+"]",
                    id: "labelFor_"+id
                  },
                  "Nom du fichier "
                ),
                DOM.br({}),
                nameInput = DOM.input({
                  type: "text",
                  name: "formdatauri_name["+count_datauri+"]",
                  id: id,
                  className: "str notNull",
                  value: "Image "+(count_datauri+1)+"."+matches[1]
                })
              ),
              DOM.td({},
                dataInput = DOM.input({type: "hidden", name: "formdatauri["+count_datauri+"]"}),
                img       = DOM.img({
                  src: "",
                  style: "max-height: 120px; max-width: 500px; border: 1px solid #333; background: white;"
                })
              )
            );

            img.src = evt.target.result;
            $V(dataInput, evt.target.result);

            $("images-list").insert(tr);
            count_datauri++;

            nameInput.tryFocus();
          };

          reader.readAsDataURL(file);
          break;
        }
      }

      var message = $("paste-message");

      if (!found) {
        Event.stop(e);
        message.show();
      }
      else {
        message.hide();
      }
    });
  });
</script>

<iframe name="upload-{{$object->_guid}}" id="upload-{{$object->_guid}}" style="width: 1px; height: 1px;"></iframe>

<form name="uploadFrm" action="?" enctype="multipart/form-data" method="post"
  onsubmit="return checkForm(this)" target="upload-{{$object->_guid}}">
  <input type="hidden" name="m" value="files" />
  <input type="hidden" name="a" value="upload_file" />
  <input type="hidden" name="dosql" value="do_file_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="ajax" value="1" />
  <input type="hidden" name="suppressHeaders" value="1" />
  <input type="hidden" name="callback" value="reloadCallback" />
  <input type="hidden" name="object_class" value="{{$object->_class}}" />
  <input type="hidden" name="object_id" value="{{$object->_id}}" />
  <input type="hidden" name="named" value="{{$named}}" />
  <input type="hidden" name="_merge_files" value="0" />

  {{if $named}}
  <input type="hidden" name="_rename" value="{{$_rename}}" />
  {{/if}}

  <table class="form">
    <tr>
      <th class="title" colspan="6">
        Ajouter un fichier {{if $named}}'{{$_rename}}'{{/if}} pour
        <br/>'{{$object->_view}}'
      </th>
    </tr>

    <tr>
      <td class="button" colspan="6">
        <div class="small-info">
          <div>{{tr}}config-dPfiles-upload_max_filesize{{/tr}} : <strong>{{$conf.dPfiles.upload_max_filesize}}</strong></div>
          <div>{{tr}}config-dPfiles-extensions_yoplet  {{/tr}} : <strong>{{$conf.dPfiles.extensions_yoplet  }}</strong></div>
        </div>
      </td>
    </tr>

    {{if !$named}}
      <tr>
        <th style="width: 120px;">
          {{mb_label object=$file field="file_category_id" typeEnum=checkbox}}
        </th>
        <td>
          <select name="_file_category_id" style="width: 15em;">
            <option value="" {{if !$file_category_id}}selected="selected"{{/if}}>&mdash; Aucune</option>
            {{foreach from=$listCategory item=curr_cat}}
            <option value="{{$curr_cat->file_category_id}}" {{if $curr_cat->file_category_id == $file_category_id}}selected="selected"{{/if}} >
              {{$curr_cat->nom}}
            </option>
            {{/foreach}}
          </select>
        </td>
        <th>
          <label title="{{tr}}CFile-_rename-desc{{/tr}}">{{tr}}CFile-_rename{{/tr}}</label>
        </th>
        <td>
          <input type="text" name="_rename" value="{{$_rename}}"/>
        </td>
        <th>
          {{mb_label object=$file field="language"}}
        </th>
        <td>
          {{mb_field object=$file field="language"}}
        </td>
      </tr>
      <tr>
        {{if "cda"|module_active}}
          <th>
            {{mb_label object=$file field="type_doc"}}
          </th>
          <td>
            {{mb_field object=$file field="type_doc" emptyLabel="Choose" style="width: 15em;"}}
          </td>
        {{else}}
          <td colspan="2"></td>
        {{/if}}

        <th>
          {{mb_label object=$file field="private" typeEnum=checkbox}}
        </th>
        <td>
          {{mb_field object=$file field="private" typeEnum=checkbox}}
        </td>

        <td colspan="2"></td>
      </tr>
    {{/if}}

    <tr>
      <th colspan="6" class="category">{{tr}}CFile{{/tr}}</th>
    </tr>
    <tr>
      <td colspan="6">
        <div style="max-height: 220px; overflow: auto; width: 100%;">
          <table class="main tbl">
            <tbody id="files-list">
              <tr>
                <td colspan="2">
                  <input type="file" name="formfile[0]" size="30" {{if !$named}} onchange="addFile(this); this.onchange=''" {{/if}} />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </td>
    </tr>

    <tbody id="image-paster" style="display: none;">
      <tr>
        <th colspan="6" class="category">{{tr}}common-msg-Image paste{{/tr}}</th>
      </tr>
      <tr>
        <td colspan="6">
          <input type="text" name="pastearea" style="width: 200px;" onkeypress="return false"
                 placeholder="{{tr}}common-msg-Paste your image here{{/tr}}" />
          <span id="paste-message" class="warning" style="display: none;">{{tr}}common-msg-Please paste a valid image{{/tr}}</span>
  
          <div style="max-height: 220px; overflow: auto; width: 100%;">
            <table class="main tbl">
              <tbody id="images-list"></tbody>
            </table>
          </div>
        </td>
      </tr>
    </tbody>

    <tr>
      <td class="button" colspan="9">
        <button class="submit" type="submit" onclick="showLoading();">{{tr}}Add{{/tr}}</button>
        {{if $conf.dPfiles.CFile.merge_to_pdf}}
          <button class="hslip" id="add_and_merge" disabled="disabled" onclick="$V(this.form._merge_files, 1); this.form.submit(); showLoading();">
            {{tr}}CFile-_add_and_merge{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
