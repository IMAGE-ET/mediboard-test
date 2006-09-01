{{include file="inc_files_functions.tpl"}}

<script type="text/javascript">

function popSearch() {
  var f = document.FrmClass;
  var url = new Url;
  url.setModuleAction("system", "object_selector");
  url.addParam("keywords", f.keywords.value);
  url.addParam("selClass", f.selClass.value);  
  url.popup(600, 300, "-");
}

function pageMain() {
  initAccord(true);
}

</script>

<table class="main">
  <tr>
    <td>
      <form name="FrmClass" action="?m={{$m}}" method="get" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="file_id" value="{{$file->file_id}}" />
      <input type="hidden" name="cat_id" value="{{$cat_id}}" />
      <table class="form">
        <tr>
          <td  class="readonly">
            <label for="selClass" title="Veuillez Sélectionner une Class">Choix du type d'objet</label>
            <input type="text" readonly="readonly" ondblclick="popSearch()" name="selClass" value="{{$selClass}}" />
          </td>
          <td class="readonly">
            <input type="text" readonly="readonly" ondblclick="popSearch()" name="keywords" value="{{$keywords}}" />
            <button type="button" onclick="popSearch()" class="search">Rechercher</button>
            <input type="hidden" name="selKey" value="{{$selKey}}" />
            <input type="hidden" name="selView" value="{{$selView}}" />
          </td>
          {{if $selKey}}
          <td>
            <select name="typeVue" onchange="if(this.form.onsubmit()) { this.form.submit() };">
              <option value="0" {{if $typeVue == 0}}selected="selected"{{/if}}>Miniatures et aperçus</option>
              <option value="1" {{if $typeVue == 1}}selected="selected"{{/if}}>Miniatures seuls</option>
              <option value="2" {{if $typeVue == 2}}selected="selected"{{/if}}>Aperçus seuls</option>
            </select>
          </td>
          {{/if}}
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
          {{if $typeVue}}
          <td colspan="2" id="listView">
            {{if $typeVue==1}}
            {{include file="inc_list_view_colonne.tpl"}}
            {{else $typeVue==2}}
            {{include file="inc_list_view_gd_thumb.tpl"}}            
            {{/if}}
          </td>      
          {{else}}
          <td style="width: 400px;" id="listView">
            {{include file="inc_list_view.tpl"}}
          </td>
          <td id="bigView" style="text-align: center;">
            {{include file="inc_preview_file.tpl"}}
          </td>
          {{/if}}
        </tr>
      </table>
    </td>
  </tr>
  {{/if}}
</table>