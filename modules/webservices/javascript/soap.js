SOAP = {
  connexion: function (exchange_source_name) {
    new Url("webservices", "ajax_connexion_soap")
      .addParam("exchange_source_name", exchange_source_name)
      .requestModal(600);
  },

  getFunctions: function (exchange_source_name, form) {
    new Url("webservices", "ajax_getFunctions_soap")
      .addParam("form_name", form.getAttribute("name"))
      .addParam("exchange_source_name", exchange_source_name)
      .requestModal(600);
  }
}