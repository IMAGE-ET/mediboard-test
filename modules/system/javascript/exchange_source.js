/**
 * JS function Exchange Source
 *  
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

ExchangeSource = {
  status_images : ["images/icons/status_red.png", "images/icons/status_orange.png", "images/icons/status_green.png"],

  resfreshImageStatus : function(element){
    if (!element.get('id')) {
      return;
    }

    var url = new Url("system", "ajax_get_source_status");

    element.title = "";
    element.src   = "style/mediboard/images/icons/loading.gif";

    url.addParam("source_guid", element.get('guid'));
    url.requestJSON(function(status) {
      element.src = ExchangeSource.status_images[status.reachable];
      element.onmouseover = function() { 
        ObjectTooltip.createDOM(element, 
          DOM.div(null, 
            DOM.table({className:"main tbl", style:"max-width:350px"}, 
              DOM.tr(null,
                DOM.th(null, status.name)
              ), 
              DOM.tr(null,
                DOM.td({className:"text"}, 
                  DOM.strong(null, "Message : "), status.message)
             ), 
             DOM.tr(null,
             DOM.td({className:"text"},
               DOM.strong(null, "Temps de réponse : "), status.response_time, " ms")
           )
           )
         ).hide()) 
      };
    });
  },

  manageFiles: function (source_guid) {
    new Url("system", "ajax_manage_files")
      .addParam("source_guid", source_guid)
      .requestModal(1000, 500);
  },

  showDirectory: function (source_guid) {
    new Url("system", "ajax_manage_directory")
      .addParam("source_guid", source_guid)
      .requestUpdate("listDirectory");

    ExchangeSource.showFiles(source_guid);
  },

  changeDirectory: function (source_guid, directory) {
    new Url("system", "ajax_manage_directory")
      .addParam("source_guid", source_guid)
      .addParam("new_directory", directory)
      .requestUpdate("listDirectory");

    ExchangeSource.showFiles(source_guid, directory);
  },

  showFiles: function (source_guid, current_directory) {
    new Url("system", "ajax_manage_file")
      .addParam("source_guid", source_guid)
      .addParam("current_directory", current_directory)
      .requestUpdate("listFiles");
  },

  deleteFile: function (source_guid, file,current_directory) {
    new Url("system", "ajax_manage_file")
      .addParam("source_guid", source_guid)
      .addParam("current_directory", current_directory)
      .addParam("delete", true)
      .addParam("file", file)
      .requestUpdate("listFiles");
  },

  renameFile: function (source_guid, file, current_directory) {
    var new_name = prompt("Etes-vous sûr de vouloir renommer le fichier '"+file+"'\nEntrez le nouveau nom du fichier", "");
    if (new_name === null || new_name === "") {
      return false;
    }

    new Url("system", "ajax_manage_file")
      .addParam("source_guid", source_guid)
      .addParam("current_directory", current_directory)
      .addParam("file", file)
      .addParam("new_name", new_name)
      .addParam("rename", true)
      .requestUpdate("listFiles");
    return true;
  },

  addFileForm: function (source_guid, current_directory) {
    new Url('system', 'ajax_add_file')
      .addParam("source_guid", source_guid)
      .addParam("current_directory", current_directory)
      .requestModal(700, 300)
      .modalObject.observe("afterClose", function () {ExchangeSource.showFiles(source_guid, current_directory)});
  },

  closeAfterSubmit : function(message) {
    window.parent.$("systemMsg").update("");
    if (message["resultNumber"] != '0'  ) {
      window.parent.SystemMessage.notify(DOM.div({class:"info"}, message["result"]+" x"+message["resultNumber"]+"<br/>"), true);
    }
    var length = message["error"].length;
    if (length !==0) {
      for (var i =0; i<length; i++) {
        window.parent.SystemMessage.notify(DOM.div({class:"error"}, message["error"][i]+"<br/>"), true);
      }
    }
    window.parent.Control.Modal.close();
  },

  addInputFile : function(elt) {
    var name = elt.name;
    var number_file = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
    number_file = parseInt(number_file);
    number_file += 1;
    var form = elt.up();
    var br = form.insertBefore(DOM.br(), elt.nextSibling);
    form.insertBefore(DOM.input({type: "file", name: "import["+number_file +"]", size: 0, onchange: "ExchangeSource.addInputFile(this); this.onchange=''"})
      , br.nextSibling);
  }
};