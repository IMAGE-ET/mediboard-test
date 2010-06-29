var fHeight = 380;
var file_preview = null;
var file_deleted = null;

function popFile(objectClass, objectId, elementClass, elementId, sfn){
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, sfn);
}

function ZoomAjax(objectClass, objectId, elementClass, elementId, sfn){
  file_preview = elementId;
  var url = new Url("dPfiles", "preview_files");
  url.addParam("objectClass", objectClass);
  url.addParam("objectId", objectId);
  url.addParam("elementClass", elementClass);
  url.addParam("elementId", elementId);
  if(sfn && sfn != 0){
    url.addParam("sfn", sfn);
  }
  url.requestUpdate('bigView');
}

function setObject(oObject){
  var oForm = getForm("FrmClass");
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
  var oForm = getForm("FrmClass");
  var sSelClass = oForm.selClass.value;
  var sSelKey   = oForm.selKey.value;
  
  if($('tab-'+sSelClass+sSelKey)){
    return false;
  }
  
  var url = new Url("dPfiles", "httpreq_vw_listfiles");
  url.addParam("selKey", sSelKey);
  url.addParam("selClass", sSelClass);  
  url.addParam("typeVue", oForm.typeVue.value);
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
  
  var oForm = getForm("FrmClass");
  var url = new Url("dPfiles", "httpreq_vw_listfiles");
  url.addParam("selKey", oForm.selKey.value);
  url.addParam("selClass", oForm.selClass.value);  
  url.addParam("typeVue", oForm.typeVue.value);
  url.requestUpdate('listView');
}

function submitFileChangt(oForm){
  file_deleted = null;
  onSubmitFormAjax(oForm, { onComplete : reloadAfterMoveFile });
}

Document.refreshList = reloadAfterUploadFile;
