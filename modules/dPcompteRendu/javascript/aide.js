/**
 * $Id$
 *
 * @category Modèles
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

Aide = {
  edit: function(aide_id, user_id) {
    var url = new Url("compteRendu", "ajax_edit_aide");
    url.addParam("aide_id", aide_id);
    url.addNotNullParam("user_id", user_id);
    url.requestModal("60%", "60%");
  },

  loadTabsAides: function(form) {
    var url = new Url("compteRendu", "httpreq_vw_list_aides");
    url.addFormData(form);
    url.requestUpdate("tabs_aides");
    return false;
  },

  remove: function(aide_id, aide_view) {
    var form = getForm("deleteAide");
    $V(form.aide_id, aide_id);
    confirmDeletion(form, {typeName: 'l\'aide', objName: aide_view}, Aide.loadTabsAides.curry(getForm("filterFrm")));
  },

  exportAidesCSV: function(owner, object_class, aides_ids) {
    var url = new Url("compteRendu", "aides_export_csv", "raw");
    url.addParam("owner", owner);
    url.addParam("object_class", object_class);
    url.pop(400, 300, "export_csv", null, null, {
      id:           aides_ids.join("-"),
      owner:        owner,
      object_class: object_class
    })
  },

  popupImport: function(owner_guid) {
    var url = new Url("compteRendu", "aides_import_csv");
    url.addParam("owner_guid", owner_guid);
    url.pop(500, 400, "Import d'aides à la saisie");
    return false;
  },

  getListDependValues: function(select, object_class, field) {
    if (select.hasClassName("loaded")) return;

    var oldValue = $V(select);
    var oldValueHTML = select.selectedOptions[0].innerHTML;
    var url = new Url("compteRendu", "httpreq_select_enum_values");
    url.addParam("object_class", object_class);
    url.addParam("field", field);
    url.requestUpdate(select, function() {
      select.addClassName("loaded");
      // Si la valeur n'est plus présente dans le select, on l'ajoute
      if ($A(select.options).pluck("value").indexOf(oldValue) == -1) {
        select.insert(DOM.option({value: oldValue}, oldValueHTML));
      }
      $V(select, oldValue, false);
    });
  }
};