/**
 * Created with JetBrains PhpStorm.
 * User: ox-nicolas
 * Date: 25/04/13
 * Time: 14:47
 * To change this template use File | Settings | File Templates.
 */

FTP = {
  connexion: function (exchange_source_name) {
    var url = new Url("ftp", "ajax_connexion_ftp");
    url.addParam("exchange_source_name", exchange_source_name);
    url.requestUpdate("utilities-source-ftp-connexion-" + exchange_source_name);
  },

  getFiles: function (exchange_source_name) {
    var url = new Url("ftp", "ajax_getFiles_ftp");
    url.addParam("exchange_source_name", exchange_source_name);
    url.requestUpdate("utilities-source-ftp-getFiles-" + exchange_source_name);
  }
};