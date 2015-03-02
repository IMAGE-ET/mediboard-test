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
  export_csv : null,

  /**
   * Method to display result from search
   *
   * @param {Element} form The form
   *
   * @return bool
   */
  displayResults: function (form) {
    var url = new Url('search',  'ajax_result_search');
    url.addFormData(form);
    url.requestUpdate('list_result');
    return false;
  },

  /**
   * Method to show the différence between two mapping
   *
   * @param {Element} before the mapping before
   * @param {Element} after  the mapping after
   *
   */
  showdiff: function (before, after) {
    var url = new Url('search',  'ajax_show_diff_mapping');
    url.addParam("before" , before);
    url.addParam("after" , after);
    url.requestModal("85%","50%");
  },

  /**
   * Method to configure the serveur
   */
  configServeur : function () {
    var url    = new Url('search', 'ajax_configure_serveur');
    url.requestUpdate("CConfigServeur");
  },

  /**
   * Method to configure the serveur
   */
  configES : function () {
    var url    = new Url('search', 'ajax_configure_es');
    url.requestUpdate("CConfigES");
    return false;
  },

  /**
   * Method to toggle élément
   *
   * @param {Element} elt
   */
  toggleElement : function (elt) {
    if (elt.hasClassName('down') || elt.hasClassName('up')){
      elt.toggleClassName('down');
      elt.toggleClassName('up');
    }
    else {
      elt.toggle();
    }
  },

  /**
   * Method to select a praticien
   *
   * @param {Element} element2
   * @param {Element} element
   */
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

  /**
   * Method use in first indexing
   *
   * @param {Element} table
   * @param {Element} mapping
   * @param {Element} types
   */
  firstIndexing: function (table, mapping, types) {

    if (table) {
      Modal.confirm(" Voulez-vous remplir la table ? Si cette action a déjà été effectuée, cela entraînera une nouvelle indexation des données. ATTENTION : l'opération sera irréversible. ",
        {onOK: function() {
          var url = new Url('search',  'first_indexing');
          url.addParam("table" , table);
          url.addParam("mapping" , mapping);
          url.addParam("names_types[]" , types, true);
          url.requestUpdate("tab_config_es");
        }
      });
    }
    else {
      var url = new Url('search',  'first_indexing');
      url.addParam("table" , table);
      url.addParam("mapping" , mapping);
      url.addParam("names_types[]" ,types, true);
      url.requestUpdate("tab_config_es");
    }
  },

  /**
   * Method to index in mode routine
   */
  routineIndexing: function () {
    var url = new Url('search',  'routine_indexing');
    url.requestUpdate("tab_config_es");
  },

  /**
   * Method to update stats in cartographie mapping
   */
  updateListStats: function () {
    var url = new Url('search', 'vw_cartographie_mapping');
    setInterval(function() {
      url.requestUpdate("cartographie_systeme");
    }, 300000);
  },

  /**
   * Method to check checkboxes
   *
   * @param {Element} input
   * @param {string} name
   */
  checkAllCheckboxes: function (input, name) {
    var oform = input.form;
    var elements = oform.select('input[name="'+name+'"]');

    elements.each(function(element) {
      element.checked = input.checked;
    });
  },
  searchByType: function (date, user_id, object_id, object_ref, fuzzy_search, types) {
    new Url('search',  'ajax_result_search_details_aggreg')
      .addParam("date", date)
      .addParam("user_id", user_id)
      .addParam("words", this.words_request)
      .addParam("object_ref_id", object_id)
      .addParam("object_ref_class",object_ref)
      .addParam("fuzzy_search", fuzzy_search)
      .addParam("types", types, true)
      .requestModal("60%","60%");
  },
  /**
   * Method to search more details about an item
   *
   * @param {Integer} object_id
   * @param {String}  object_ref
   * @param {bool}    fuzzy_search
   * @param {String}  type
   */
  searchMoreDetails: function (object_id, object_ref, fuzzy_search, type) {
    var container = "tab-"+type;
    new Url('search',  'ajax_result_search_details')
      .addParam("object_ref_id", object_id)
      .addParam("object_ref_class",object_ref)
      .addParam("type", type)
      .addParam("words", this.words_request)
      .addParam("fuzzy_search", fuzzy_search)
      .requestUpdate(container);
  },

  /**
   * Method to search more details about an item
   * @param {string} date
   * @param {Integer} user_id
   * @param {String} type
   */
  searchMoreDetailsLog: function (date, user_id, type) {
    var container = "tab-"+type;
    new Url('search',  'ajax_result_search_log_details')
      .addParam("date", date)
      .addParam("type", type)
      .addParam("user_id", user_id)
      .addParam("words", this.words_request)
      .requestUpdate(container);
  },

  /**
   * Method to filter term
   *
   * @param {Element} input
   * @param {String}  classe
   * @param {Element} table
   *
   */
  filter: function (input, classe, table) {
    table = $(table);
    table.select("tr").invoke("show");
    var nameClass = "." + classe;
    var terms = $V(input);
    if (!terms) return;
    terms= terms.split(" ");
    table.select(nameClass).each(function(e) {
      terms.each(function(term){
        if (!e.getText().like(term)) {
          e.up("tr").hide();
        }
      });
    });
  },

  /**
   * Method to add an item to an rss
   *
   * @param {Integer} id
   * @param {Integer} rss_id
   * @param {String}  type
   * @param {Integer} object_id
   * @param {String}  rmq
   *
   */
  addItemToRss : function (id, rss_id, type, object_id, rmq) {
    new Url('search',  'vw_search_item')
      .addParam("search_item_id", id)
      .addParam("rss_id", rss_id)
      .addParam("object_id", object_id)
      .addParam("object_type", type)
      .addParam("rmq", rmq)
      .requestModal("40%", "40%");
  },

  /**
   * Method to update index
   *
   * @param {Element} types
   *
   */
  updateIndex : function (types) {
    new Url('search',  'update_index')
      .addParam("types[]",  types, true)
      .requestUpdate($('tab_config_es'));
  },

  /**
   * Method to create mapping for logs
   */
  createLogMapping : function () {
   new Url('search',  'first_indexing')
    .addParam("log" , true)
    .requestUpdate("tab_config_es");
  },

  /**
   * Method to display logs results
   * @param {Element} form
   */
  displayLogResults : function (form) {
    var url = new Url('search',  'ajax_result_log_search');
    url.addFormData(form);
    url.requestUpdate('list_log_result');
    return false;
  },

  /**
   * Method to load search items added to an rss
   * @param {Integer} rss_id
   */
  loadSearchItems: function (rss_id) {
    var url = new Url('search', 'ajax_load_search_items');
    url.addParam("rss_id", rss_id);
    url.requestUpdate($('div_search_items'));
  },

  /**
   * Method to generate a csv file
   */
  downloadCSV: function() {
    var url = new Url('search', 'download_search_results', 'raw');
    url.pop(10,10, "export_recherches", null, null,
      {"results" : this.export_csv, "accept_utf8" : "1"});
  },

  getAutocompleteUser: function (form, contexte) {
    var element = form.elements.user_id,
      tokenField = new TokenField(element, {onChange: function(){}.bind(element)});

    var element_input = form.elements.user_view;
    var url = new Url("mediusers", "ajax_users_autocomplete");
    url.addParam("object_class", "CMediusers");
    url.addParam("input_field", element_input.name);

    switch (contexte) {
      case "pharmacie" :
      case "prescription" :
      case "dPboard" : url.addParam("edit", "1");
        url.addParam("praticiens", "1");
        break;
      case "log" :
      case "pmsi" :
        break;
      default : url.addParam("praticiens", "1"); break;
    }

    url.autoComplete(element_input, null, {
      minChars: 2,
      method: "get",
      dropdown: true,
      updateElement: function(selected) {
        var guid = selected.get("id");
        var _name  = selected.down().down().getText();

        var to_insert = !tokenField.contains(guid);
        tokenField.add(guid);

        if (to_insert) {
          insertTag(guid, _name);
        }

        var element_input = form.elements.user_view;
        $V(element_input, "");
      }
    });

    window.user_tag_token = tokenField;
  },

  toggleColumn: function(toggler, column) {
    var visible = column.visible();
    toggler.toggleClassName("expand", visible);

    column.toggle();
  },

  progressBar: function (id, score) {
    var container = $('score_'+id);
    var color = '#f00';
    if (score > 25 && score < 75) {
      color = '#E8AC07';
    }
    else if (score >= 75) {
      color = '#93D23F';
    }
    var data = [
      { data: score, color: color },
      { data: 100 - score, color: '#BBB' }
    ];

    jQuery.plot(container, data, {
      series: {
        pie: {
          innerRadius: 0.4,
          show: true,
          label: { show: false }
        }
      },
      legend: { show: false }
    });
  },

  manageThesaurus: function (sejour_id, contexte, callback) {
    callback = callback || function() {Search.reloadSearchAuto(sejour_id, contexte)};
    new Url('search',  'vw_search_thesaurus')
      .requestModal("90%", "90%", {onClose: callback});
  },

  reloadSearchAuto : function(sejour_id, contexte) {
    var container = 'table_main';
    if (contexte == 'pmsi') {
       container = "tab-search";
    }
    new Url('search',  'vw_search_auto')
    .addParam("sejour_id", sejour_id)
    .addParam("contexte", contexte)
    .requestUpdate(container);
  }
};