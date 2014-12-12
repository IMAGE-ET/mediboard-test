/**
 * $Id$
 *
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

Thesaurus = window.Thesaurus || {

  updateListThesaurus: function (start) {
    var url = new Url('search',  'ajax_list_thesaurus');
    url.addParam("start_thesaurus", start);
    url.requestUpdate('list_thesaurus_entry');

  },

  displayResultsThesaurus: function(form){
    var url = new Url('search',  'ajax_result_thesaurus');
    url.addFormData(form);
    url.requestUpdate('list_log_result');
    return false;
  },

  addeditThesaurusEntry: function (search_agregation, search_body, search_user_id, search_types, search_contexte, thesaurus_entry, callback) {
   var callback = callback ||  function () {Thesaurus.updateListThesaurus();};
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

  addPatternToEntry : function (pattern) {
    var token = "";
    var oform = getForm('addeditFavoris');
    var value = oform.entry.value;
    var startPos = oform.entry.selectionStart;
    var endPos = oform.entry.selectionEnd;
    var text = value.substring(startPos,endPos);
    if (!text) {
      var debchaine = value.substring(0 , startPos);
      var finchaine = value.substring(startPos);
      text ="MOT";
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
  addTargetCallback : function (id, obj) {
    var form = getForm("cibleTarget");

    if (!obj._ui_messages[4] && $V(form.elements.del) != '1') {
      this.insertTag(id, this.name_code, obj.object_class);
      this.name_code =  null;
    }
  },

  addeditThesaurusCallback : function(id, obj) {
    this.addeditThesaurusEntry(null, null,null,null,null, id);
  },

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

  submitThesaurusEntry : function (form, callback) {
    var callback = callback ||  function () {console.log ("toto"); Control.Modal.close();};
    return onSubmitFormAjax(form, {onComplete: callback});
  }
};