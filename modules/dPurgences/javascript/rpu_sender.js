RPU_Sender = {
  popupImport: function(module) {
    new Url("dPurgences", "ajax_import_key")
      .addParam("module", module)
    .pop(500, 400, "Import de la clé publique");

    return false;
  },

  updateKey: function(fingerprint) {
    var message = fingerprint ? '<div class="info">Clé publique ajoutée au trousseau : '+fingerprint+'<\/div>' :
      '<div class="error">Impossible d\'importer la clé publique.<\/div>';

    $("import_key").update(message);
  },

  showEncryptKey: function() {
    var url = new Url("dPurgences", "ajax_show_encrypt_key");
    url.requestUpdate('show_encrypt_key');
  }
};