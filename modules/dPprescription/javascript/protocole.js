/* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

var Protocole = {
  // Ajout de protocole
  add : function(){
    var oFormPrat = document.selPrat;
    var oForm = document.addProtocolePresc;
    oForm.function_id.value = oFormPrat.function_id.value;
    oForm.praticien_id.value = oFormPrat.praticien_id.value;
    oForm.group_id.value = oFormPrat.group_id.value;
    return onSubmitFormAjax(oForm);
  },
  // Suppression de protocole
  remove : function(protocole_id){
		var oFormProt = getForm("delProt");
		$V(oFormProt.prescription_id, protocole_id);
		
    return onSubmitFormAjax(oFormProt, {
      onComplete: function(){
        Protocole.refreshList();
      } 
    } );
  },
  // Refresh de la liste des protocoles
  refreshList : function(protocoleSel_id) {
    var oForm = document.selPrat;
    var url = new Url("dPprescription", "httpreq_vw_list_protocoles");
    url.addParam("praticien_id", oForm.praticien_id.value);
    url.addParam("function_id", oForm.function_id.value);
    url.addParam("group_id", oForm.group_id.value);
    url.addParam("protocoleSel_id", protocoleSel_id);
    url.requestUpdate("protocoles");
  },
  refreshListProt: function(){
    Protocole.refreshList();
    Prescription.reload('','','','1');
  },
  // Edition d'un protocole
  edit : function(protocole_id, praticien_id, function_id) {
    Prescription.reload(protocole_id,"","","1");
    //Protocole.refreshList(protocole_id);
  },
  duplicate: function(protocole_id, protocole_dest_id){
    var url = new Url("dPprescription", "httpreq_duplicate_protocole");
    url.addParam("protocole_id", protocole_id);
		url.addParam("protocole_dest_id", protocole_dest_id);
    url.requestUpdate("systemMsg");
  },  
  // Gestion des packs de protocoles
  addPack: function(){
    var oFormPrat = document.selPrat;
    var oForm = document.createPack;
    $V(oForm.function_id, $V(oFormPrat.function_id));
    $V(oForm.praticien_id, $V(oFormPrat.praticien_id));
    $V(oForm.group_id, $V(oFormPrat.group_id));
    return onSubmitFormAjax(oForm);
  },
  removePack: function(oFormDel){
    var oFormPrat = document.selPrat;
    submitFormAjax(oFormDel, 'systemMsg' , {
      onComplete: function(){
        Protocole.refreshListPack();
        Protocole.viewPack("");
      } 
    } );
  },
  viewPack: function(pack_id, type_prot){
   var url = new Url("dPprescription", "httpreq_vw_pack");
   url.addParam("pack_id", pack_id);
   if (type_prot) {
     url.addParam("type_prot", type_prot);
   }
   url.requestUpdate("view_pack");
  },
  refreshListPack: function(pack_id){
    var oFormPrat = document.selPrat;
    var url = new Url("dPprescription", "httpreq_vw_list_pack");
    url.addParam("praticien_id", $V(oFormPrat.praticien_id));
    url.addParam("function_id", $V(oFormPrat.function_id));
    url.addParam("group_id", $V(oFormPrat.group_id));
    url.addParam("pack_id", pack_id);
    url.requestUpdate("view_list_pack");
  },
  reloadAfterAddPack: function(pack_id) {
    var oFormPrat = document.selPrat;
    Protocole.viewPack(pack_id);
    Protocole.refreshListPack(pack_id);
  },
  addProtocoleToPack: function(protocole_id){
    var oFormAddProtocole = document.addDelProtocoleToPack;
    oFormAddProtocole.prescription_id.value = protocole_id;
    submitFormAjax(oFormAddProtocole, 'systemMsg', { 
    	onComplete: function() { Protocole.viewPack(oFormAddProtocole.prescription_protocole_pack_id.value);  } 
    } );
  },
  delProtocoleToPack: function(pack_item_id){
    var oFormDelProtocole = document.addDelProtocoleToPack;
    oFormDelProtocole.del.value = "1";
    oFormDelProtocole.prescription_protocole_pack_item_id.value = pack_item_id;
    submitFormAjax(oFormDelProtocole, 'systemMsg', { 
    	onComplete: function() { Protocole.viewPack(oFormDelProtocole.prescription_protocole_pack_id.value);  } 
    } );
  },
  importProtocole: function(form) {
    var oForm = getForm(form);
    var url = new Url("dPprescription", "ajax_vw_import_protocole");
    var praticien_id = $V(oForm.praticien_id);
    var function_id = $V(oForm.function_id);
    var group_id = $V(oForm.group_id);
    if (!praticien_id && !function_id && !group_id) {
      alert("Veuillez choisir un praticien, cabinet ou établissement");
      return;
    }
    url.addParam("praticien_id", $V(oForm.praticien_id));
    url.addParam("function_id", $V(oForm.function_id));
    url.addParam("group_id", $V(oForm.group_id));
    url.popup(400, 150);
  },
  exportProtocole: function(prescription_id) {
    var url = new Url("dPprescription", "ajax_export_protocole");
    url.addParam("prescription_id", prescription_id);
    url.addParam("suppressHeaders", 1);
    url.popup();
  },
  exportProtocoles: function() {
    var oForm = getForm("exportProtocoles");
    if (!$V(oForm.praticien_id) && !$V(oForm.function_id) && !$V(oForm.group_id)) {
      alert("Veuillez choisir un praticien, cabinet ou établissement");
      return;
    }
    if (parseInt($V(oForm.lower_bound)) > parseInt($V(oForm.upper_bound))) {
      alert("Erreur d'intervalle !");
      return;
    }
    oForm.submit();
  },
  exportSchema: function() {
    var url = new Url("dPprescription", "ajax_create_schema");
    url.addParam("suppressHeaders", 1);
    url.addParam("dialog", 1);
    url.popup();
  }   
}