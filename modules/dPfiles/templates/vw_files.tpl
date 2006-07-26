<script type="text/javascript">
function popSearch() {
  var f = document.FrmClass;
  var url = new Url;
  url.setModuleAction("system", "object_selector");
  url.addParam("keywords", f.keywords.value);
  url.addParam("selClass", f.selClass.value);  
  url.popup(600, 300, "-");
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
      <input type="hidden" name="file_id" value="{{$file_id}}" />
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
    <td class="halfPane">
      <table class="form">
        <tr>
          <th class="category" colspan="2">{{$object->_view}}</th>
        </tr>
        <tr>
          <td>
            <div class="accordionMain" id="accordionConsult">
            {{foreach from=$listCategory item=curr_listCat}}
              <div id="{{$curr_listCat->nom}}">
                <div id="{{$curr_listCat->nom}}Header" class="accordionTabTitleBar">
                  {{$curr_listCat->nom}}
                </div>
                <div id="{{$curr_listCat->nom}}Content"  class="accordionTabContentBox">
                  <table class="tbl">
                  {{foreach from=$object->_ref_files item=curr_file}}
                    {{if $curr_file->file_category_id == $curr_listCat->file_category_id}}
                    <tr>
                      <td>
                        <a href="index.php?m={{$m}}&amp;file_id={{$curr_file->file_id}}">
                          <img src="mbfileviewer.php?file_id={{$curr_file->file_id}}&amp;phpThumb=1" alt="-" />
                        </a>
                      </td>
                      <td style="vertical-align: middle;">
                        {{$curr_file->_view}}<br />
                        {{$curr_file->file_date|date_format:"%d/%m/%Y à %Hh%M"}}
                      </td>
                    </tr>
                    {{/if}}
                  {{/foreach}}
                  </ul>
                </div>
              </div>
            {{/foreach}}            
            </div>
            <script language="Javascript" type="text/javascript">
            new Rico.Accordion( $('accordionConsult'), {panelHeight:350} );
            </script>
          </td>
        </tr>
      </table>
    </td>
    <td class="halfPane" style="text-align: center;">
      {{if $file_id}}
      <img src="mbfileviewer.php?file_id={{$file_id}}&amp;phpThumb=1&amp;hp=350&amp;wl=300" alt="-" />
      {{else}}
      Selectionnez un fichier
      {{/if}}
    </td>
  </tr>
  {{/if}}
</table>