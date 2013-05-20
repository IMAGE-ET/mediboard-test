// $Id: $

CodeCCAM = {
  show: function(code, object_class) {
    var url = new Url("dPccam", "vw_full_code");
    url.addParam("_codes_ccam", code);
    url.addParam("object_class", object_class);
    url.addParam("hideSelect", "1");
    url.modal();
  }
};
