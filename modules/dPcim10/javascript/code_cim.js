
CodeCIM = {
  show: function(code) {
    var url = new Url("cim10", "vw_full_code");
    url.addParam("code", code);
    url.modal();
  }
};