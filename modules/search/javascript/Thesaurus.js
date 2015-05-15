/**
 * $Id$
 *
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

Thesaurus = window.Thesaurus || {
  url_addeditThesaurusEntry: null,
  /**
   * Method to update the list of the thesaurus
   * @param {Integer} start
   */
  updateListThesaurus:       function (start) {
    var url = new Url('search', 'ajax_list_thesaurus');
    url.addParam("start_thesaurus", start);
    url.requestUpdate('list_thesaurus_entry');
  },

  /**
   * Method to display the list of the thesaurus
   * @param {Element} form
   */
  displayResultsThesaurus: function (form) {
    var url = new Url('search', 'ajax_result_thesaurus');
    url.addFormData(form);
    url.requestUpdate('list_log_result');
    return false;
  },

  /**
   * Method to display the list of the thesaurus
   * @param {Boolean} search_agregation
   * @param {String} search_body
   * @param {Integer} search_user_id
   * @param {Element} search_types
   * @param {String} search_contexte
   * @param {String} thesaurus_entry
   * @param {Function} callback
   */
  addeditThesaurusEntryManual: function (search_agregation, search_body, search_user_id, search_types, search_contexte, thesaurus_entry, callback) {
    callback = callback || function () {
      Thesaurus.updateListThesaurus(null);
    };
    var user_id = (search_user_id) ? search_user_id : User.id;
    var url = new Url('search', 'ajax_addedit_thesaurus_entry');
    url.addParam("search_agregation", search_agregation);
    url.addParam("search_body", search_body);
    url.addParam("search_user_id", user_id);
    url.addParam("search_types[]", search_types, true);
    url.addParam("search_contexte", search_contexte);
    url.addParam("thesaurus_entry", thesaurus_entry);
    Thesaurus.url_addeditThesaurusEntry = url;
    url.requestModal("65%", "70%", {
      onClose: callback
    });

    this.modal = url.modalObject;
  },

  /**
   * Method to display the list of the thesaurus
   * @param {Element} form
   * @param {String} thesaurus_entry
   * @param {Function} callback
   */
  addeditThesaurusEntry: function (form, thesaurus_entry, callback) {
    if (form) {
      var search_agregation = (form.aggregate) ? form.aggregate.value : null;
      var search_body = (form.words) ? $V(form.words) : "";
      var search_types = (form.elements['names_types[]']) ? $V(form.elements['names_types[]']) : null;
      var search_contexte = (form.contexte) ? $V(form.contexte) : "";
      var start = (form.start) ? form.start : null;
    }
    callback = callback || function () {
      Thesaurus.updateListThesaurus(start);
    };

    var url = new Url('search', 'ajax_addedit_thesaurus_entry');
    url.addParam("search_agregation", search_agregation);
    url.addParam("search_body", search_body);
    url.addParam("search_user_id", User.id);
    url.addParam("search_types[]", search_types, true);
    url.addParam("search_contexte", search_contexte);
    url.addParam("thesaurus_entry", thesaurus_entry);
    Thesaurus.url_addeditThesaurusEntry = url;
    url.requestModal("65%", "70%", {
      onClose: callback
    });

    this.modal = url.modalObject;
  },

  /**
   * Method to display the list of the thesaurus
   * @param {String} pattern
   * @param {Element} form
   */
  addPatternToEntry: function (pattern, form) {
    var token = "";
    var oform = (form) ? form : getForm('addeditFavoris');
    var value = (form) ? oform.words.value : oform.entry.value;
    var entry = (form) ? oform.words : oform.entry;
    var caret = entry.caret();
    var startPos = caret.begin;
    var endPos = caret.end;
    var text = value.substring(startPos, endPos);
    if (!text) {
      var debchaine = value.substring(0, startPos);
      var finchaine = value.substring(startPos);
      text = "MOT";
      value = debchaine + text + finchaine;
    }

    var deb_selection = startPos + 2;
    var fin_selection = deb_selection + text.length;

    switch (pattern) {
      case "add" :
        token = "( " + text + " && MOT2 ) ";
        if (text != "MOT") {
          deb_selection = startPos + 6 + text.length;
          fin_selection = deb_selection + 4;
        }
        break;
      case "or" :
        token = "( " + text + " || MOT2 ) ";
        if (text != "MOT") {
          deb_selection = startPos + 6 + text.length;
          fin_selection = deb_selection + 4;
        }
        break;
      case "not" :
        token = " !" + text + " ";
        break;
      case "like" :
        token = " " + text + "~ ";
        deb_selection = startPos + 1;
        fin_selection = deb_selection + text.length;
        break;
      case "obligation" :
        token = " +" + text + " ";
        break;
      case "prohibition" :
        token = " -" + text + " ";
        break;
      case "without_negatif" :
        token = " ++" + text + " ";
        deb_selection = startPos + 3;
        fin_selection = deb_selection + text.length;
        break;
      default :
        break;
    }

    entry.value = value.replace(text, token);
    entry.caret(deb_selection, fin_selection);
  },

  /**
   * Method to display the list of the thesaurus
   * @param {Integer} thesaurus_entry_id
   * @param {Function} callback
   */
  addeditTargetEntry: function (thesaurus_entry_id, callback) {
    var url = new Url('search', 'ajax_addedit_target_entry');
    url.addParam("thesaurus_entry_id", thesaurus_entry_id);
    url.requestModal("40%", "40%", {
      onClose: callback
    });
  },

  name_code: null,

  /**
   * Method callback after addTarget
   *
   * @param {Integer} id
   * @param {Element} obj
   */
  addTargetCallback: function (id, obj) {
    var form = getForm("cibleTarget");

    if (!obj._ui_messages[4] && $V(form.elements.del) != '1') {
      this.insertTag(id, this.name_code, obj.object_class);
      this.name_code = null;
    }
  },

  /**
   * Method callback after addeditTarget
   *
   * @param {Integer} id
   * @param {Element} obj
   */
  addeditThesaurusCallback: function (id, obj) {
    this.addeditThesaurusEntry(null, id);
  },

  /**
   * Method callback after addTarget
   *
   * @param {Integer} id
   * @param {String} name
   * @param {String} obj_class
   */
  insertTag: function (id, name, obj_class) {
    var tag = $(obj_class + "-" + id);
    var color = "";
    switch (obj_class) {
      case "CCodeCIM10" :
        color = "#CCFFCC";
        break;
      case "CCodeCCAM"  :
        color = "rgba(153, 204, 255, 0.6)";
        break;
      case "CMedicamentClasseATC"  :
        color = "rgba(240, 255, 163,0.6)";
        break;
      default :
        color = "";
    }

    if (!tag) {
      var btn = DOM.button({
        "type":      "submit",
        "className": "delete",
        "style":     "display: inline-block !important",
        "onclick":   "$V(this.form.elements.search_thesaurus_entry_target_id," + id + ");$V(this.form.elements.del,'1');  this.form.onsubmit() ; this.up('li').next('br').remove(); this.up('li').remove();"
      });
      var li = DOM.li({
        "className": "tag",
        "style":     "background-color:" + color + "; cursor:auto"
      }, name, btn);

      $(obj_class + "_tags").insert(li).insert(DOM.br());
    }
  },

  /**
   * Method to submit thesaurus entry
   *
   * @param form
   * @param callback
   *
   * @returns {Boolean}
   */
  submitThesaurusEntry: function (form, callback) {
    callback = callback || function () {
      Control.Modal.close();
    };
    return onSubmitFormAjax(form, {onComplete: callback});
  },

  getAutocompleteFavoris: function (form) {
    var element_input = form.elements.words;
    var contextes = ["generique", $V(form.elements.contexte)];

    var url = new Url("search", "ajax_seek_autocomplete_thesaurus_entry");
    url.addParam("object_class", "CSearchThesaurusEntry");
    url.addParam("input_field", element_input.name);
    url.addParam("user_id", User.id);
    url.addParam("contextes[]", contextes, true);
    url.autoComplete(element_input, null, {
      minChars:      2,
      method:        "get",
      dropdown:      true,
      updateElement: function (selected) {
        if (selected.down("span", "1").getText() != "") {
          var _name = selected.down("span", "1").getText();
          $V(element_input, _name);
          $V(form.elements.aggregate, selected.down().get("aggregate"));
          var types = selected.down().get("types");
          if (types) {
            types = types.split("|");
          }
          $V(form.elements["names_types[]"], types);
        }
      }
    });
  },

  filterListThesaurus: function (form) {
    var url = new Url('search', 'ajax_list_thesaurus');
    url.addFormData(form);
    url.requestUpdate('list_thesaurus_entry');
    return false;
  }
};