/**
 * @package    Mediboard
 * @subpackage system
 * @version    $Revision: 7167 $
 * @author     SARL OpenXtrem
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

ListeChoix = {
  edit: function(liste_id) {
    Form.onSubmitComplete = liste_id == '0' ? ListeChoix.onSubmitComplete : Prototype.emptyFunction;

    var url = new Url('compteRendu', 'ajax_edit_liste_choix');
    url.addParam('liste_id', liste_id);
    url.requestModal(600);
    url.modalObject.observe("afterClose", ListeChoix.refreshList);
  },
  
  onSubmitComplete: function (guid, properties) {
    Control.Modal.close();
    var id = guid.split('-')[1];
    ListeChoix.edit(id);
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form);
  },

  onSubmitChoix: function(form) {
    return onSubmitFormAjax(form, ListeChoix.refreshChoix);
  },
    
  confirmDeletion: function(button) {
  var form = button.form;
    var options = {
      typeName: 'liste de choix', 
      objName: $V(form.nom)
    };
    
    var ajax = function() {
      Control.Modal.close();
    };
    
    confirmDeletion(form, options, ajax);    
  },
  
  filter: function() {
    ListeChoix.refreshList();
    return false;
  },
  
  refreshChoix: function() {
    var form = getForm('Add-Choix');
    var url = new Url('compteRendu', 'ajax_list_choix');
    url.addElement(form.liste_id);
    url.requestUpdate('list-choix', function() {
      var add = getForm('Add-Choix');
      add.focusFirstElement();
    });
  },

  refreshList: function() {
  var form = getForm('Filter');
    var url = new Url('compteRendu', 'ajax_list_listes_choix');
    url.addElement(form.user_id);
    url.requestUpdate('list-listes_choix');
  },
  
  importCSV: function(owner_guid) {
    var url = new Url('compteRendu', 'listes_choix_import_csv');
    url.addParam('owner_guid', owner_guid);
    url.pop(500, 400, 'Import de listes de choix');
  },
  
  exportCSV: function(owner_guid, ids) {
    var url = new Url('compteRendu', 'listes_choix_export_csv', 'raw');
    url.addParam('owner_guid', owner_guid);
    url.addParam('ids', ids);
    url.open();
  }
};
