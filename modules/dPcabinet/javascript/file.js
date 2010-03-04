var File = {
  popup: function(object_class, object_id, element_class, element_id, sfn) {
    var url = new Url;
    url.ViewFilePopup(object_class, object_id, element_class, element_id, sfn);
  },
    
  upload: function(object_class, object_id, file_category_id){
    var url = new Url("dPfiles", "upload_file");
    url.addParam("object_class", object_class);
    url.addParam("object_id", object_id);
    url.addParam("file_category_id", file_category_id);
    url.popup(600, 200, "uploadfile");
  },
  
  remove: function(oButton, object_id, object_class){
    var oOptions = {
      typeName: 'le fichier',
      objName: oButton.form._view.value,
      ajax: 1,
      target: 'systemMsg'
    };
    var oAjaxOptions = {
      onComplete: function() { File.refresh(object_id, object_class); } 
    };
    confirmDeletion(oButton.form, oOptions, oAjaxOptions);
  },
  
  refresh: function(object_id, object_class) {
  	var div_id = printf("files-%s-%s", object_id, object_class);
  	
    var url = new Url("dPcabinet", "httpreq_widget_files");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.requestUpdate("files-"+object_id+"-"+object_class);
  },
  
  register: function(object_id, object_class, container) {
    var div = document.createElement("div");
    div.style.minWidth = "200px";
    div.style.minHeight = "50px";
    div.id = printf("files-%s-%s", object_id, object_class);
    $(container).insert(div);
    
    Main.add( function() {
      File.refresh(object_id,object_class);
    } );
  }
};