/**
 * JS function Domain EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

Domain = {
  modal: null,
  url  : null,
  
  showDomain : function(domain_id, element) {
    if (element) {
      element.up("tr").addUniqueClassName('selected');
    }
    
    var url = new Url("eai", "ajax_edit_domain");
    url.addParam("domain_id", domain_id);
    url.requestUpdate("vw_domain");
  },
  
  createDomainWithIdexTag : function() {
    var url = new Url("eai", "ajax_add_domain_with_idex");
    url.requestModal(500, 300);
    Domain.modal = url.modalObject;
    Domain.modal.observe("afterClose", function(){ 
      Domain.refreshListDomains(); 
    });
    
    return false;
  },
  
  showDomainCallback : function(domain_id) {
    Domain.showDomain(domain_id);
  },
  
  refreshListDomains : function() {
    var url = new Url("eai", "ajax_refresh_list_domains");
    url.requestUpdate("vw_list_domains");
  },
  
  refreshListGroupDomains : function(domain_id) {
    var url = new Url("eai", "ajax_refresh_list_group_domains");
    url.addParam("domain_id", domain_id);
    url.requestUpdate("vw_list_group_domains");
  },
  
  refreshListIncrementerActor : function(domain_id) {
    var url = new Url("eai", "ajax_refresh_list_incrementer_actor");
    url.addParam("domain_id", domain_id);
    url.requestUpdate("vw_list_incrementer_actor");
  },

  editGroupDomain : function(group_domain_id, domain_id) {
    var url = new Url("eai", "ajax_edit_group_domain");
    url.addParam("group_domain_id", group_domain_id);
    url.addParam("domain_id"      , domain_id);
    url.requestModal(400, 150);
    Domain.modal = url.modalObject;
    Domain.modal.observe("afterClose", function(){ 
      Domain.showDomain(domain_id); 
    });
    
    return false;
  },
  
  editIncrementer : function(incrementer_id, domain_id) {
    var url = new Url("dPsante400", "ajax_edit_incrementer");
    url.addParam("incrementer_id", incrementer_id);
    url.requestModal(400, 200);
  },
  
  bindIncrementerDomain : function(incrementer_id) {
    var oForm = getForm("editDomain");
    $V(oForm.incrementer_id, incrementer_id);
    
    oForm.onsubmit();
  },
  
  bindActorDomain : function(actor_id, object) {
    console.log(object);
  },
  
  resolveConflicts : function(oForm) {  
    var url = new Url("eai", "ajax_resolve_conflicts");
    url.addParam("domains_id", $V(oForm["domains_id[]"]).join("-"));
    url.requestModal(600, 400);
        
    Domain.modal = url.modalObject;
    Domain.modal.observe("afterClose", function(){ 
      Domain.refreshListDomains(); 
    });
    
    return false;
  },  
  
  selectMergeFields : function(oForm) {
    Domain.modal.close();
    
    var url = new Url("eai", "ajax_select_merge_fields");
    url.addFormData(oForm);
    url.requestModal(600, 400, { method:"post", getParameters:{m: "eai", a: "ajax_select_merge_fields", dialog:1}});
        
    Domain.modal = url.modalObject;
    Domain.modal.observe("afterClose", function(){ 
      Domain.refreshListDomains(); 
    });
    
    return false;
  },
  
  confirm: function() {
    Modal.confirm($('merge-confirm'), { onOK: Domain.perform } );
    return false;
  },
  
  perform: function() {
    getForm("form-merge").onsubmit();
  }
}