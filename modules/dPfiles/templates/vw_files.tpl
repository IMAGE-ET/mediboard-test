<script type="text/javascript">

function calcHeightParentBottom(oDiv){
  var oParent = oDiv;
  var fHeightBot = 0;
  while(oParent.nodeName!="HTML"){
    aBorderBot = Element.getStyle(oParent,"border-bottom-width").split("px");
    aMarginBot = Element.getStyle(oParent,"margin-bottom").split("px");
    aPaddingBot= Element.getStyle(oParent,"padding-bottom").split("px");
    fHeightBot += parseFloat(aBorderBot[0]) + parseFloat(aMarginBot[0]) + parseFloat(aPaddingBot[0]);
    oParent = oParent.parentNode;
  }
  return fHeightBot;
}

function initAccord(OpenTab){
  var fHeightDivTitle = 0;
  var fhauteur_div = 0;
  fHeightDivTitle = Element.getOffsetHeightByClassName("accordionTabTitleBar");
  fhauteur_div = window.getInnerDimensions().x - Position.cumulativeOffset($('accordionConsult'))[1] - fHeightDivTitle;
  aAccordBorderTop = Element.getStyle("accordionConsult","border-top-width").split("px");
  fhauteur_div = fhauteur_div - parseFloat(aAccordBorderTop[0]);
  fHeightBot = calcHeightParentBottom($('accordionConsult'));
  fhauteur_div = fhauteur_div - fHeightBot;
  new Rico.Accordion( $('accordionConsult'), {panelHeight:fhauteur_div,onShowTab: storeKeyCat,onLoadShowTab : OpenTab} );
}

function pageMain() {
  initAccord(0);
}

function popSearch() {
  var f = document.FrmClass;
  var url = new Url;
  url.setModuleAction("system", "object_selector");
  url.addParam("keywords", f.keywords.value);
  url.addParam("selClass", f.selClass.value);  
  url.popup(600, 300, "-");
}

function storeKeyCat(objAcc){
  var cat_id = objAcc.titleBar.id;
  cat_id = cat_id.substring(3, cat_id.indexOf("Header"));
  document.FrmClass.cat_id.value = cat_id;
}

function popFile(file_id, sfn){
  var url = new Url;
  url.ViewFilePopup(file_id, sfn);
}

function ZoomFileAjax(file_id, sfn){
  var url = new Url;
  url.setModuleAction("dPfiles", "preview_files");
  url.addParam("file_id", file_id);
  if(sfn!=0){
    url.addParam("sfn", sfn);
  }
  url.requestUpdate('bigView', { waitingText : "Chargement du miniature" });
}

function setData(selClass,keywords,key,val){
  var f = document.FrmClass;
  if (val != '') {
    f.selKey.value = key;
    f.selView.value = val;
    f.selClass.value = selClass;
    f.keywords.value = keywords;
    f.file_id.value = "";
    f.submit();
  }
}
function saveCatId(key){
  document.FrmClass.cat_id.value = key;
}

function reloadListFile(){
  var url = new Url;
  url.setModuleAction("dPfiles", "httpreq_vw_listfiles");
  url.addParam("selKey", document.FrmClass.selKey.value);
  url.addParam("selClass", document.FrmClass.selClass.value);  
  url.addParam("typeVue", document.FrmClass.typeVue.value);
  url.addParam("cat_id", document.FrmClass.cat_id.value);
  url.requestUpdate('listView', { waitingText : null });
}

function submitFileChangt(oForm){
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadListFile });
}
</script>

<table class="main">
  <tr>
    <td>
      <form name="FrmClass" action="?m={{$m}}" method="get">
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
            <select name="typeVue" onchange="submit()">
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