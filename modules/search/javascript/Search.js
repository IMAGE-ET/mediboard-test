/**
 * $Id$
 *
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

Search = window.Search || {
  words_request : null,
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
  },

  firstIndexing: function (table, mapping, input) {
    var values = [];
    input.each(function(item) {
      values.push($F(item));
    });
    if (table) {
      Modal.confirm(" Voulez-vous remplir la table ? Si cette action a déjà été effectuée, cela entraînera une nouvelle indexation des données. ATTENTION : l'opération sera irréversible. ",
        {onOK: function() {
          var url = new Url('search',  'first_indexing');
          url.addParam("table" , table);
          url.addParam("mapping" , mapping);
          url.addParam("names_types" , Object.toJSON(values));
          url.requestUpdate("table_main");
        }
      });
    }
    else {
      var url = new Url('search',  'first_indexing');
      url.addParam("table" , table);
      url.addParam("mapping" , mapping);
      url.addParam("names_types" , Object.toJSON(values));
      url.requestUpdate("table_main");
    }
  },

  routineIndexing: function () {
    var url = new Url('search',  'routine_indexing');
    url.requestUpdate("table_main");
  },

  updateListStats: function () {
    var url = new Url('search', 'vw_cartographie_mapping');
    setInterval(function() {
      url.requestUpdate("cartographie_systeme");
    }, 300000);
  },

  checkAllCheckboxes: function (form, name) {
    var oform = form;

    while (oform.parentNode && oform.nodeName.toLowerCase() != 'form'){
      oform = oform.parentNode;
    }
    var elements = oform.getElementsByTagName('input');

    for (var i = 0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox' && elements[i].name == name) {
        elements[i].checked = form.checked;
      }
    }
  },

  searchMoreDetails: function (object_id, object_ref, type) {
    var id = "details-"+type+"-"+object_id;
    new Url('search',  'ajax_result_search_details')
      .addParam("object_ref_id", object_id)
      .addParam("object_ref_class",object_ref)
      .addParam("type", type)
      .addParam("words", this.words_request)
      .requestUpdate(id);
  },

  filter: function (input, classe, table) {
    table = $(table);
    table.select("tr").invoke("show");
    var nameClass = "." + classe;
    var terms = $V(input);
    if (!terms) return;
    terms= terms.split(" ");
    table.select(nameClass).each(function(e) {
      terms.each(function(term){
        if (!e.innerHTML.like(term)) {
          e.up("tr").hide();
        }
      });
    });
  }
};