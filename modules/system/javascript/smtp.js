/**
 * Created with JetBrains PhpStorm.
 * User: ox-nicolas
 * Date: 25/04/13
 * Time: 14:47
 * To change this template use File | Settings | File Templates.
 */

SMTP = {
  connexion: function (exchange_source_name) {
    new Url("system", "ajax_connexion_smtp")
      .addParam("exchange_source_name", exchange_source_name)
      .addParam("type_action", "connexion")
      .requestModal(600);
  },

  envoi: function (exchange_source_name) {
    new Url("system", "ajax_connexion_smtp")
      .addParam("exchange_source_name", exchange_source_name)
      .addParam("type_action", "envoi")
      .requestModal(600);
  }
}