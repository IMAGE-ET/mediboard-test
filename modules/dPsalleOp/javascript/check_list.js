/**
 * $Id$
 *
 * @category SalleOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CheckList = {
  /**
   * {Url}
   */
  urlCategory: null,

  /**
   * {Url}
   */
  urlItemType: null,

  updateObject: function(select) {
    var form = select.form;
    var parts = $V(select).split(/-/);
    $V(form.object_class, parts[0]);
    $V(form.object_id,   (parts[1] === "none" ? "" : parts[1]));
  },

  editItemCategory: function(list_type_id, cat_id, callback) {
    var url = new Url('dPsalleOp', 'vw_daily_check_item_category');
    url.addParam("list_type_id", list_type_id);
    url.addParam("item_category_id", cat_id);
    url.requestModal(800, 700);

    url.modalObject.observe("afterClose", function(){
      if (callback) {
        CheckListGroup.modal_checklist.refreshModal();
      }
      else {
        location.reload();
      }
    });
    CheckList.urlCategory = url;
  },
  callbackItemCategory: function(id, obj) {
    CheckList.editItemCategory(obj.list_type_id, id, 0);
  },

  editItemType: function(category_id, item_type_id) {
    var url = new Url('dPsalleOp', 'vw_daily_check_item_type');
    url.addParam("item_category_id", category_id);
    url.addParam("item_type_id", item_type_id);
    url.requestModal(600, 400);

    url.modalObject.observe("afterClose", function(){
      CheckList.urlCategory.refreshModal();
    });

    CheckList.urlItemType = url;
  },

  preview: function(object_class, object_id, type) {
    var url = new Url("dPsalleOp", 'vw_daily_check_list_preview');
    url.addParam("object_class" , object_class);
    url.addParam("object_id"    , object_id);
    url.addParam("type"         , type);
    url.requestModal(900, 700);
  }
};