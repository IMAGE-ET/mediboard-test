var file_preview = null;
var file_deleted = null;

 popFile = function(objectClass, objectId, elementClass, elementId, sfn){
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, sfn);
}

ZoomAjax = function(objectClass, objectId, elementClass, elementId, sfn){
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

setObject = function(oObject){
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

reloadListFileDossier = function(sAction){
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

reloadAfterUploadFile = function(category_id){
  reloadListFile('add', category_id);
}

reloadAfterMoveFile = function(category_id){
  reloadListFile('move', category_id);
}

reloadAfterDeleteFile = function(category_id){
  reloadListFile('delete', category_id);
}

reloadListFile = function(sAction, category_id){
  if(sAction == 'delete' && file_preview == file_deleted){
    ZoomAjax('','','','', 0);
  }
  
  var oForm = getForm('FrmClass');
  var url = new Url('files', 'httpreq_vw_listfiles');
  url.addParam('selKey', oForm.selKey.value);
  url.addParam('selClass', oForm.selClass.value);  
  url.addParam('typeVue', oForm.typeVue.value);

  var category = $('Category-'+category_id);
  if (category) {
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

submitFileChangt = function(oForm){
  file_deleted = null;
  onSubmitFormAjax(oForm, reloadAfterMoveFile);
}

if (window.Document) {
  Document.refreshList = reloadAfterUploadFile;
}

showCancelled = function(button) {
  $('listView').select('div.file_cancelled').invoke('toggle');
}

cancelFile = function(form, category_id) {
  if (confirm($T('CFile-comfirm_cancel'))) {
    onSubmitFormAjax(form, reloadAfterDeleteFile.curry(category_id));
  }
  return false;
}