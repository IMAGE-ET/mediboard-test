function refreshValue(element, class, id, field) {
  url = new Url;
  url.setModuleAction("dPstock", "httpreq_vw_object_value");
  url.addParam("class", class);
  url.addParam("id",    id);
  url.addParam("field", field);
  url.requestUpdate(element, { waitingText: null } );
}