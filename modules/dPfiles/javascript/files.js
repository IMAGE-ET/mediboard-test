var file_preview = null;
var file_deleted = null;

function popFile(objectClass, objectId, elementClass, elementId, sfn){
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, sfn);
}

function ZoomAjax(objectClass, objectId, elementClass, elementId, sfn){
  file_preview = elementId;
  var url = new Url('files', 'preview_files');
  url.addParam('objectClass', objectClass);
  url.addParam('objectId', objectId);
  url.addParam('elementClass', elementClass);
  url.addParam('elementId', elementId);
  if(sfn && sfn != 0){
    url.addParam('sfn', sfn);
  }
  url.requestUpdate('bigView');
}

function setObject(oObject){
  var oForm = getForm('FrmClass');
  oForm.selKey.value = oObject.id;
  oForm.selView.value = oObject.view;
  oForm.selClass.value = oObject.objClass;
  oForm.keywords.value = oObject.keywords;
  oForm.file_id.value = '';
  if (oForm.onsubmit()) {
    oForm.submit();
  }
  
  if(window.saveObjectInfos){
    saveObjectInfos(oObject);
  }
}

function reloadListFileDossier(sAction){
  var oForm = getForm('FrmClass');
  var sSelClass = oForm.selClass.value;
  var sSelKey   = oForm.selKey.value;
  
  if($('tab-'+sSelClass+sSelKey)){
    return false;
  }
  
  var url = new Url('files', 'httpreq_vw_listfiles');
  url.addParam('selKey', sSelKey);
  url.addParam('selClass', sSelClass);  
  url.addParam('typeVue', oForm.typeVue.value);
  url.addParam('accordDossier', 1);
  url.requestUpdate('File'+sSelClass+sSelKey);
}

function reloadAfterUploadFile(category_id){
  reloadListFile('add', category_id);
}

function reloadAfterMoveFile(category_id){
  reloadListFile('move', category_id);
}

function reloadAfterDeleteFile(category_id){
  reloadListFile('delete', category_id);
}

function reloadListFile(sAction, category_id){
  if(sAction == 'delete' && file_preview == file_deleted){
    ZoomAjax('','','','', 0);
  }
  
  var oForm = getForm('FrmClass');
  var url = new Url('files', 'httpreq_vw_listfiles');
  url.addParam('selKey', oForm.selKey.value);
  url.addParam('selClass', oForm.selClass.value);  
  url.addParam('typeVue', oForm.typeVue.value);
  if (category_id != undefined) {
    if (category_id == '') {
      category_id = 0;
    }
    url.addParam('category_id', category_id);
    url.requestUpdate('Category-' + category_id);
  }
  else {
    url.requestUpdate('listView');
  }
}

function submitFileChangt(oForm){
  file_deleted = null;
  onSubmitFormAjax(oForm, reloadAfterMoveFile);
}

if (window.Document) {
  Document.refreshList = reloadAfterUploadFile;
}
