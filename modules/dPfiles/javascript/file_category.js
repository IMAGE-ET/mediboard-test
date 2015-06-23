/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

FilesCategory = {
  modal_cat    : null,
  object_guids : [],

  loadList : function() {
    var url = new Url("files", "ajax_list_categories");
    url.requestUpdate('list_file_category');
  },

  openInfoReadFilesGuid : function(object_guid) {
    var parts = object_guid.split("-");
    var  object_class = parts[0];
    var  object_id    = parts[1];

    var url = new Url('files', "ajax_modal_object_files_category");
    url.addParam('object_guid', object_guid);
    url.requestModal("700", "500");
    url.modalObject.observe('afterClose',
      FilesCategory.iconInfoReadFilesGuid.curry(object_class, [object_id]));
    FilesCategory.modal_cat = url;
  },

  reloadModal : function() {
    if (FilesCategory.modal_cat) {
      FilesCategory.modal_cat.refreshModal();
    }
  },

  addObjectGuid : function(object_guid) {
    FilesCategory.object_guids.push(object_guid);
  },

  showUnreadFiles : function() {
    var tab = {};

    FilesCategory.object_guids.each(function (object_guid) {
      var parts = object_guid.split("-");
      var  object_class = parts[0];
      var  object_id    = parts[1];

      if (!tab[object_class]) {
        tab[object_class] = [];
      }

      tab[object_class].push(object_id);
    });

    $H(tab).each(function(pair) {
      FilesCategory.iconInfoReadFilesGuid(pair.key, pair.value);
    });
  },

  iconInfoReadFilesGuid : function(object_class, object_ids) {
    new Url('files', "ajax_check_object_files_category")
      .addParam('object_class', object_class)
      .addParam('object_ids'  , object_ids.join("-"))
      .requestJSON(function(obj) {
        $H(obj).each(function(pair) {
          var element = $(pair.key+"_check_category");
          element.setVisible(pair.value > 0);
          element.down("span").update(pair.value);
        });
      });
  },

  edit : function(category_id, element) {
    if (element) {
      element.up('tr').addUniqueClassName('selected');
    }

    new Url("files", "ajax_edit_category")
      .addParam("category_id", category_id)
      .requestModal(800, 600)
      .modalObject.observe("afterClose", function() {
        FilesCategory.loadList();
      });
  },

  callback : function(id) {
    Control.Modal.close();
    FilesCategory.loadList();
    FilesCategory.edit(id);
  },

  checkMergeSelected : function(oinput) {
    var selected = $$("#list_file_categories input:checked");

    if (selected.length > 2) {
      //$(selected)[0].checked = false;
      $(oinput).checked = false;    // unckeck the last
    }
  },

  mergeSelected : function() {
    var selected = $$("#list_file_categories input:checked");
    if (selected.length < 2) {
      return;
    }

    var objects_id = [];
    selected.each(function(element) {
      objects_id.push($(element).get("id"));
    });

    var elements = objects_id.join('-');

    var url = new Url("system", "object_merger");
    url.addParam('objects_class', 'CFilesCategory');
    url.addParam('objects_id', elements);
    url.addParam('mode', 'fast');
    url.popup(800, 600, "merge_patients");
  },

  changePage: function(page) {
    $V(getForm('listFilter').page, page);
  }
};

onMergeComplete = function() {
  FilesCategory.loadList();
};