/**
 * $Id$
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

Route = {

  edit : function (id) {
    new Url("eai", "ajax_edit_route")
      .addParam("route_id", id)
      .requestModal(400, 400)
      .modalObject.observe("afterClose", Route.refreshList);
  },

  refreshList : function () {
    new Url("eai", "ajax_list_route")
      .requestUpdate("list_route")
  },

  autocomplete_receiver : function () {
    var classe = "receiver";
    Route.autocomplete(classe);
  },

  autocomplete_sender : function () {
    var classe = "sender";
    Route.autocomplete(classe);
  },

  autocomplete : function (classe) {
    var form = getForm("editRoute");
    var classe_id_auto = classe+"_id_autocomplete";
    var classe_id_autocomplete = form.elements[classe_id_auto];
    var url = new Url("system", "ajax_seek_autocomplete");
    url.addParam("field", "libelle");
    url.addParam("input_field", classe_id_autocomplete.name);
    url.addParam("whereComplex[libelle]", "IS NOT NULL");

    var autocompleter = url.autoComplete(classe_id_autocomplete, null, {
      minChars: 2,
      width: "250px",
      method: "get",
      dropdown: true,
      callback: function(input, queryString){
        var _class = classe+"_class";
        return queryString+"&object_class="+$V(form.elements[_class]);
      },
      updateElement: function(selected) {
        var classe_id = classe+"_id";
        $V(form[classe_id], selected.get('id'), false);
        $V(classe_id_autocomplete, selected.getText().trim(), false);
      }
    });
  }
};