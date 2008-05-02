var File = {
  popup: function(object_class, object_id, element_class, element_id, sfn) {
    url = new Url;
    url.ViewFilePopup(object_class, object_id, element_class, element_id, sfn);
  },   
  upload: function(object_class, object_id, file_category_id){
    url = new Url();
    url.setModuleAction("dPfiles", "upload_file");
    url.addParam("object_class", object_class);
    url.addParam("object_id", object_id);
    url.addParam("file_category_id", file_category_id);
    url.popup(600, 200, "uploadfile");
  },
  remove: function(oButton, object_id, object_class){
    oOptions = {
      typeName: 'le fichier',
      objName: oButton.form._view.value,
      ajax: 1,
      target: 'systemMsg'
    }
    oAjaxOptions = {
      onComplete: function() { File.refresh(object_id, object_class) } 
    }
    confirmDeletion(oButton.form, oOptions, oAjaxOptions);
  },
  refresh: function(object_id, object_class){
    var url = new Url;
    url.setModuleAction("dPcabinet", "httpreq_widget_files");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.requestUpdate("files-"+object_id+"-"+object_class, { waitingText: null } );
  },
  register: function(object_id, object_class){
    document.write('<div id=files-'+object_id+'-'+object_class+'></div>');
    Main.add( function() {
      File.refresh(object_id,object_class);
    } );
  }
};