MLLP = {
  connexion: function (exchange_source_name) {
    new Url("hl7", "ajax_connexion_mllp")
      .addParam("exchange_source_name", exchange_source_name)
      .requestModal(600);
  },

  send: function (exchange_source_name) {
    new Url("hl7", "ajax_send_mllp")
      .addParam("exchange_source_name", exchange_source_name)
      .requestModal(600);
  }
}