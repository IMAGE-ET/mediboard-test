/**
 * $Id$
 *
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

Search = window.Search || {
  /**
   * make the search with words
   * @param words
   * @param mistakes
   * @param search
   */

  displayResults: function (form){
    var url = new Url('search',  'ajax_result_search');
    url.addFormData(form);
    url.requestUpdate('list_result');
    return false;
  },

  showdiff: function (before, after) {
    var url = new Url('search',  'ajax_show_diff_mapping');
    url.addParam("before" , before);
    url.addParam("after" , after);
    url.requestModal("85%","50%");
  },

  saveModifyMapping : function (modify) {
    var url = new Url('search',  'vw_cartographie_mapping');
    url.addParam("modify" , modify);
    url.requestUpdate('table-mapping');
  },

  configServeur : function () {
    var url    = new Url('search', 'ajax_configure_serveur');
    url.requestUpdate("CConfigServeur");
  },

  toggleElement : function (elt) {
    if (elt.hasClassName('down') || elt.hasClassName('up')){
      elt.toggleClassName('down');
      elt.toggleClassName('up');
    }
    else {
      elt.toggle();
    }
  },

  assignFieldText : function (elt, text) {
    elt.value += text;
    elt.focus();
  },

  popupExample : function () {
    var url = new Url('search',  'ajax_show_query_examples');
    url.requestModal("85%","50%","Exemples de transcriptions d'une requête");
  },

  selectPraticien : function (element2, element) {
    // Autocomplete des users
    var url = new Url("mediusers", "ajax_users_autocomplete");
    url.addParam("praticiens", '1');
    url.addParam("input_field", element.name);
    url.autoComplete(element, null, {
      minChars: 0,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected) {
        if ($V(element) == "") {
          $V(element, selected.down('.view').innerHTML);
        }
        var id = selected.getAttribute("id").split("-")[2];
        $V(element2, id);
      }
    });
  }


};