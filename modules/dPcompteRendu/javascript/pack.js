/**
 * @package    Mediboard
 * @subpackage system
 * @version    $Revision: 7167 $
 * @author     SARL OpenXtrem
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

Pack = {
  edit: function(pack_id) {
  	Form.onSubmitComplete = pack_id == '0' ? Pack.onSubmitComplete : Prototype.emptyFunction;

  	var url = new Url('compteRendu', 'ajax_edit_pack');
    url.addParam('pack_id', pack_id);
    url.requestModal(600);
    url.modalObject.observe("afterClose", Pack.refreshList);
  },
  
  onSubmitComplete: function (guid, properties) {
    Control.Modal.close();
    var id = guid.split('-')[1];
    Pack.edit(id);
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form)
  },

  confirmDeletion: function(form) {
    var options = {
      typeName: 'Pack ', 
      objName: $V(form.nom)
    };
    
    var ajax = Control.Modal.close;
    
    confirmDeletion(form, options, ajax);    
  },
  
  filter: function() {
	  Pack.refreshList();
    return false;
  },
  
  onSubmitModele: function(form) {
   return onSubmitFormAjax(form, function() {
     Pack.refreshListModeles();
     $V(form.modele_id, '', false);
   });
  },
    
  refreshList: function() {
    var form = getForm('Filter');
    var url = new Url('compteRendu', 'ajax_list_pack');
    url.addFormData(form);
    url.requestUpdate('list-packs');
  },

  refreshListModeles: function() {
    var form = getForm('Edit-CPack');
    var url = new Url('compteRendu', 'ajax_list_modeles_links');
    url.addElement(form.pack_id);
    url.requestUpdate('list-modeles-links');
  },

  refreshFormModeles: function() {
    var form = getForm('Edit-CPack');
    
    // Nothing on creation
    if ($V(form.pack_id).empty()) {
      return;
    }
    
    // Request
    var url = new Url('compteRendu','ajax_form_modeles_links');
    url.addParam('filter_class', $V(form.object_class));
    url.addParam('object_guid', Pack.makeGuid(form));
    url.addParam('pack_id', $V(form.pack_id));
    url.requestUpdate('form-modeles-links');
  },
  
  makeGuid: function(form) {
    var object_guid = '';

    if (form.user_id && $V(form.user_id    ) != '') object_guid = 'CMediUsers-' + $V(form.user_id    );
    if (form.function_id && $V(form.function_id) != '') object_guid = 'CFunctions-' + $V(form.function_id);
    if (form.group_id && $V(form.group_id   ) != '') object_guid = 'CGroups-'    + $V(form.group_id   );

    return object_guid;
  },
  
  changeClass: function(input) {
    Pack.refreshFormModeles(input.value, Pack.makeGuid(input.form));
  }
};



