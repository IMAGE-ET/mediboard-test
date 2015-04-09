<script type="text/javascript">
  reloadCallback = function(id, object) {
    if (window.parent.reloadAfterUploadFile) {
      window.parent.reloadAfterUploadFile(object.file_category_id);
    }
    
    if (window.parent.File && window.parent.File.refresh) {
      window.parent.File.refresh(object.object_id, object.object_class);
    }
  
    if (window.parent.reloadListFileEditPatient) {
      window.parent.reloadListFileEditPatient("load", object.file_category_id);
    }

    if (window.parent.reloadAfterUpload) {
      window.parent.reloadAfterUpload();
    }

    var systemMsg=window.parent.$("systemMsg").update('{{$messages|smarty:nodefaults}}');
  
    systemMsg.show();
    window.parent.Control.Modal.close();
  }
</script>