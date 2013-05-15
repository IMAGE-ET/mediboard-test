/**
 *
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

FieldSelector = {

  openSection : function(id, link) {
    $$(".section").each(function(elt) {
      elt.hide();
    });

    $("section-"+id).show();
    $(link).up('tr').addUniqueClassName("selected");
  },

  openSubSection : function(id, link) {
    $$(".subsection").each(function(elt) {
      elt.hide();
    });
    $("subsection-"+id).show();
    $(link).up('tr').addUniqueClassName("selected");
  },

  insertField : function(elt, inputId) {
    var texte_etiq = window.text_focused;
    if (!texte_etiq) {
      var texte_etiq = $(inputId);
    }
    var caret = texte_etiq.caret();
    texte_etiq.caret(caret.begin, caret.end, elt + " ");
    texte_etiq.caret(caret.begin + elt.length+1);
    texte_etiq.fire('ui:change');
  },

  searchField : function(element, oclass) {
    var url = new Url("compteRendu","ajax_search_field");
    url.addParam("search", element);
    url.addParam("class", oclass);
    if (element) {
      $("FieldSelectorTable").hide();
      url.requestUpdate("FieldSearchResultTable");
      $("FieldSearchResultTable").show();
    }
    else {
      $("FieldSearchResultTable").hide();
      $("FieldSelectorTable").show();
    }
  }
}