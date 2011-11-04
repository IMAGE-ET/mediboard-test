/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

Ressource = {
  edit: function(ressource_soin_id) {
    var url = new Url('dPprescription', 'ajax_form_ressource');
    url.addParam('ressource_soin_id', ressource_soin_id);
    url.requestUpdate("edit_ressource");
  },
  
  onSubmit: function(form) {
    return onSubmitFormAjax(form, { 
      onComplete: function() {
        Ressource.refreshList();
      }
    })
  },
  
  confirmDeletion: function(form) {
    var options = {
      typeName:'ressource', 
      objName: $V(form.libelle),
      ajax: 1
    }
    
    var ajax = {
      onComplete: function() {
        Message.refreshList();
      }
    }
    
    confirmDeletion(form, options, ajax);    
  },
  
  refreshList: function() {
    var url = new Url('dPprescription', 'ajax_list_ressource');
    url.requestUpdate('list_ressources');
  }
};
