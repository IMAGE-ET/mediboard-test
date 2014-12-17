/**
 * $Id$
 *
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

Thesaurus = window.Thesaurus || {

  /**
   * Method to update the list of the thesaurus
   * @param {Integer} start
   */
  updateListThesaurus: function (start) {
    var url = new Url('search',  'ajax_list_thesaurus');
    url.addParam("start_thesaurus", start);
    url.requestUpdate('list_thesaurus_entry');

  },

  /**
   * Method to display the list of the thesaurus
   * @param {Integer} start
   */
  displayResultsThesaurus: function(form){
    var url = new Url('search',  'ajax_result_thesaurus');
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
  addeditThesaurusEntry: function (search_agregation, search_body, search_user_id, search_types, search_contexte, thesaurus_entry, callback) {
    callback = callback ||  function () {Thesaurus.updateListThesaurus();};
    var url = new Url('search', 'ajax_addedit_thesaurus_entry');
    url.addParam("search_agregation", search_agregation);
    url.addParam("search_body", search_body);
    url.addParam("search_user_id", search_user_id);
    url.addParam("search_types[]", search_types, true);
    url.addParam("search_contexte", search_contexte);
    url.addParam("thesaurus_entry", thesaurus_entry);
    url.requestModal("65%", "70%", {
      onClose: callback
    });

    window.url_addeditThesaurusEntry = url;
    this.modal = url.modalObject;
  },

  /**
   * Method to display the list of the thesaurus
   * @param {String} pattern
   */
  addPatternToEntry : function (pattern) {
    var token = "";
    var oform = getForm('addeditFavoris');
    var value = oform.entry.value;
    var entry = oform.entry;
    var caret = entry.caret();
    var startPos = caret.begin;
    var endPos = caret.end;
    var text = value.substring(startPos,endPos);
    if (!text) {
      var debchaine = value.substring(0 , startPos);
      var finchaine = value.substring(startPos);
      text = "MOT";
      value = debchaine + text + finchaine;
    }

    var deb_selection = startPos + 2;
    var fin_selection = deb_selection + text.length;

    switch(pattern) {
      case "add" :
        token = "( "+ text +" && MOT2 ) ";
        if (text != "MOT") {
          deb_selection = startPos + 6 + text.length;
          fin_selection = deb_selection + 4;
        }
        break;
      case "or" :
        token = "( "+ text +" || MOT2 ) ";
        if (text != "MOT") {
          deb_selection = startPos + 6 + text.length;
          fin_selection = deb_selection + 4;
        }
        break;
      case "not" :
        token = " !"+ text+ " ";
        break;
      case "like" :
        token = " " + text+ "~ ";
        deb_selection = startPos + 1;
        fin_selection = deb_selection + text.length;
        break;
      case "obligation" :
        token = " +"+ text+ " ";
        break;
      case "prohibition" :
        token = " -"+ text+ " ";
        break;
      case "without_negatif" :
        token = "( -pas -aucun -sans -aucune -aucun -si +"+ text+ ") ";
        break;
      default :
        break;
    }

    oform.entry.value = value.replace(text, token);
    oform.entry.caret(deb_selection, fin_selection);
  },

  /**
   * Method to display the list of the thesaurus
   * @param {Integer} thesaurus_entry_id
   * @param {Function} callback
   */
  addeditTargetEntry : function (thesaurus_entry_id, callback) {
    var url = new Url('search',  'ajax_addedit_target_entry');
    url.addParam("thesaurus_entry_id", thesaurus_entry_id);
    url.modal({
      width     : "40%",
      height    : "40%",
      afterClose: callback
    });
  },

  name_code : null,

  /**
   * Method callback after addTarget
   *
   * @param {Integer} id
   * @param {Element} obj
   */
  addTargetCallback : function (id, obj) {
    var form = getForm("cibleTarget");

    if (!obj._ui_messages[4] && $V(form.elements.del) != '1') {
      this.insertTag(id, this.name_code, obj.object_class);
      this.name_code =  null;
    }
  },

  /**
   * Method callback after addeditTarget
   *
   * @param {Integer} id
   * @param {Element} obj
   */
  addeditThesaurusCallback : function(id, obj) {
    this.addeditThesaurusEntry(null, null,null,null,null, id);
  },

  /**
   * Method callback after addTarget
   *
   * @param {Integer} id
   * @param {String} name
   * @param {String} obj_class
   */
  insertTag : function (id, name, obj_class) {
    var tag = $(obj_class + "-" + id);

    if (!tag) {
      var btn = DOM.button({
        "type": "submit",
        "className": "delete",
        "style": "display: inline-block !important",
        "onclick": "$V(this.form.elements.search_thesaurus_entry_target_id,"+ id +");$V(this.form.elements.del,'1');  this.form.onsubmit() ; this.up('li').next('br').remove(); this.up('li').remove();"
      });
      var li = DOM.li({
        "className": "tag"
      }, name, btn);

      $(obj_class+"_tags").insert(li).insert(DOM.br());
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
  submitThesaurusEntry : function (form, callback) {
    callback = callback ||  function () {Control.Modal.close();};
    return onSubmitFormAjax(form, {onComplete: callback});
  }
};