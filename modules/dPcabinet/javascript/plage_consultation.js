/* $Id: plage_consultation.js $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author SARL OpenXtrem
* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*/

PlageConsultation  = {
  status_images : ["images/icons/status_red.png", "images/icons/status_orange.png", "images/icons/status_green.png"],
  modal: null,
  
  edit: function(plageconsult_id) {
    var url = new Url('cabinet', 'edit_plage_consultation');
    url.addParam('plageconsult_id', plageconsult_id);
    url.requestModal(800);
    this.modal = url.modalObject;
  },
  
  onSubmit: function(form) {
    return onSubmitFormAjax(form, function() {
      PlageConsultation.refreshList();
      PlageConsultation.modal.close();
    });
  },
  
  checkForm: function(form) {
    if (!checkForm(form)) {
      return false;
    }

    if (form.nbaffected.value!= 0 && form.nbaffected.value != "") {
      Console.debug(form.debut.value);
      Console.debug(form.fin.value);
      if (form.debut.value > form._firstconsult_time.value || form.fin.value < form._lastconsult_time.value){
        if (!(confirm("Certaines consultations se trouvent en dehors de la plage de consultation.\n\nVoulez-vous appliquer les modifications ?"))){
          return false;
        }
      }  
    }
          
    return true;
  },
  
  resfreshImageStatus : function(element){
    if (!element.get('id')) {
      return;
    }
  
    element.title = "";
    element.src   = "style/mediboard/images/icons/loading.gif";
    
    url.addParam("source_guid", element.get('guid'));
    url.requestJSON(function(status) {
      element.src = PlageConsultation.status_images[status.reachable];
      });
  }
};