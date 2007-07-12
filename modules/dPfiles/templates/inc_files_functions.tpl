<script type="text/javascript">
var oCookie = new CJL_CookieUtil("fileAccordion");

var fHeight = 380;
var file_preview = null;
var file_deleted = null;
var showTabAcc = 0;

if(oCookie.getSubValue("showTab")){
  showTabAcc = oCookie.getSubValue("showTab");
}
if(oCookie.getSubValue("height")){
  fHeight = oCookie.getSubValue("height");
}

function storeKeyCat(objAcc){
  var aArray = oAccord.accordionTabs;
  for ( var i=0 ; i < aArray.length ; i++ ){
    if(objAcc == aArray[i]){
      oCookie.setSubValue("showTab", i.toString());
      showTabAcc = i.toString();
    }
  }
}

function popFile(objectClass, objectId, elementClass, elementId, sfn){
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, sfn);
}

function ZoomAjax(objectClass, objectId, elementClass, elementId, sfn){
  file_preview = elementId;
  var url = new Url;
  url.setModuleAction("dPfiles", "preview_files");
  url.addParam("objectClass", objectClass);
  url.addParam("objectId", objectId);
  url.addParam("elementClass", elementClass);
  url.addParam("elementId", elementId);
  if(sfn && sfn!=0){
    url.addParam("sfn", sfn);
  }
  url.requestUpdate('bigView', { waitingText : "Chargement du miniature" });
}

function setObject(oObject){
  //alert("toto"+"id"+oObject.id+"view"+oObject.view+"class"+oObject.objClass+"key"+oObject.keyword);
  var oForm = document.FrmClass;
  oForm.selKey.value = oObject.id;
  oForm.selView.value = oObject.view;
  oForm.selClass.value = oObject.objClass;
  oForm.keywords.value = oObject.keywords;
  oForm.file_id.value = "";
  if (oForm.onsubmit()) {
    oForm.submit();
  }
  
  if(window.saveObjectInfos){
    saveObjectInfos(oObject);
  }
}

function reloadListFileDossier(sAction){
  var sSelClass = document.FrmClass.selClass.value;
  var sSelKey   = document.FrmClass.selKey.value;
  if(eval($('accordion'+sSelClass+sSelKey))){
    return false;
  }
  var url = new Url;
 
  url.setModuleAction("dPfiles", "httpreq_vw_listfiles");
  url.addParam("selKey", sSelKey);
  url.addParam("selClass", sSelClass);  
  url.addParam("typeVue", document.FrmClass.typeVue.value);
  url.addParam("accordDossier", 1);
  url.requestUpdate('File'+sSelClass+sSelKey);
}

function reloadAfterUploadFile(){
  reloadListFile('add');
}

function reloadAfterMoveFile(){
  reloadListFile('move');
}

function reloadAfterDeleteFile(){
  reloadListFile('delete');
}

function reloadListFile(sAction){
  if(sAction == "delete" && file_preview == file_deleted){
    ZoomAjax("","","","", 0);
  }
  var url = new Url;

  url.setModuleAction("{{$m}}", "httpreq_vw_listfiles");
  url.addParam("selKey", document.FrmClass.selKey.value);
  url.addParam("selClass", document.FrmClass.selClass.value);  
  url.addParam("typeVue", document.FrmClass.typeVue.value);
  url.requestUpdate('listView', { waitingText : null });
}

function submitFileChangt(oForm){
  file_deleted = null;
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAfterMoveFile });
}

function reloadAfterSaveDoc() { 
  reloadAfterUploadFile(); 
}

</script>