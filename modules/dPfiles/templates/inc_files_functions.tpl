<script type="text/javascript">

var oCookie = new CJL_CookieUtil("fileAccordion");
var fHeight = 380;
var file_preview = null;
var file_deleted = null;
if(oCookie.getSubValue("height")){
  var fHeight = oCookie.getSubValue("height");
}

function initAccord(init_resize){
  var oAccordionDiv = $("accordionConsult");
  if (!oAccordionDiv) {
    return;
  }
  var fHeightDivTitle = 0;
  var fhauteur_div = 0;
  fHeightDivTitle = Element.getOffsetHeightByClassName("accordionTabTitleBar");
  fhauteur_div = window.getInnerDimensions().x - Position.cumulativeOffset($('accordionConsult'))[1] - fHeightDivTitle;
  aAccordBorderTop = Element.getStyle("accordionConsult","border-top-width").split("px");
  fHeight = fhauteur_div - parseFloat(aAccordBorderTop[0]) - 14; //-14 pour les marges et bordures en bas des tableaux  
  if(fHeight<=120){
    fHeight = 120;
  }
  oCookie.setSubValue("height", fHeight);
  if(init_resize){
    oAccord.lastExpandedTab.content.style.height = fHeight + "px";
    oAccord.setOptions( { panelHeight: fHeight } );
  }
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
  file_preview = file_id;
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

function reloadListFile(){
  if(file_deleted && file_preview == file_deleted){
    ZoomFileAjax("", 0);
  }
  var url = new Url;
  initAccord(false);
  url.setModuleAction("dPfiles", "httpreq_vw_listfiles");
  url.addParam("selKey", document.FrmClass.selKey.value);
  url.addParam("selClass", document.FrmClass.selClass.value);  
  url.addParam("typeVue", document.FrmClass.typeVue.value);
  url.addParam("cat_id", document.FrmClass.cat_id.value);
  url.requestUpdate('listView', { waitingText : null });
}

function submitFileChangt(oForm){
  file_deleted = null;
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadListFile });
}

</script>