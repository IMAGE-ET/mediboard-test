<script type="text/javascript">
function popSearch() {
  var f = document.FrmClass;
  var url = new Url;
  url.setModuleAction("system", "object_selector");
  url.addParam("keywords", f.keywords.value);
  url.addParam("selClass", f.selClass.value);  
  url.popup(600, 300, "-");
}

function ZoomFileAjax(file_id){
  var VwFileUrl = new Url;
  VwFileUrl.setModuleAction("dPfiles", "preview_files");
  VwFileUrl.addParam("file_id", file_id);
  VwFileUrl.requestUpdate('bigView', { waitingText : null });
}

function setData(key, val){
  var f = document.FrmClass;
  if (val != '') {
    f.selKey.value = key;
    f.selView.value = val;
    f.file_id.value = "";
    f.submit();
  }
}
function ResetValue(){
   var f = document.FrmClass;
   f.selKey.value  = "";
   f.selView.value = "";
   f.file_id.value = "";
   f.submit();
}
</script>

<table class="main">
  <tr>
    <td>
      <form name="FrmClass" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="file_id" value="{{$file->file_id}}" />
      <table class="form">
        <tr>
          <td>
            <label for="selClass" title="Veuillez Sélectionner une Class">Choix du type d'objet</label>
            <select name="selClass" onchange="ResetValue()">
              <option value="">&mdash; Choisissez un type</option>
            {{foreach from=$listClass item=curr_listClass}}
              <option value="{{$curr_listClass}}"{{if $selClass==$curr_listClass}} selected="selected"{{/if}}>{{$curr_listClass}}</option>
            {{/foreach}}
            </select>
          </td>
          <td>
            {{if $selClass}}
            <input type="text" name="keywords" value="{{$keywords}}" />
            <button type="button" onclick="popSearch()" class="search">Rechercher</button>
            {{/if}}
            <input type="hidden" name="selKey" value="{{$selKey}}" />
            <input type="hidden" name="selView" value="{{$selView}}" />
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  {{if $selClass && $selKey}}
  <tr>
    <td>
      <table class="form">
        <tr>
          <th class="category" colspan="2">{{$object->_view}}</th>
        </tr>
        <tr>
          <td style="width: 400px;" id="listView">
            {{include file="inc_list_view.tpl"}}
          </td>
          <td id="bigView" style="text-align: center;">
            {{include file="inc_preview_file.tpl"}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  {{/if}}
</table>