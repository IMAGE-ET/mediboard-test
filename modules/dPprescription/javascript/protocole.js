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
  remove : function(oForm){
    var oFormPrat = document.selPrat;
    submitFormAjax(oForm, 'systemMsg', {
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
  duplicate: function(protocole_id){
    var url = new Url("dPprescription", "httpreq_duplicate_protocole");
    url.addParam("protocole_id", protocole_id);
    url.requestUpdate("systemMsg");
  },
  preview: function(protocole_id){
    var url = new Url("dPprescription", "httpreq_preview_protocole");
    url.addParam("protocole_id", protocole_id);
    url.popup(800,600, "Previsualisation protocole");
  },
  
  // Gestion des packs de protocoles
  addPack: function(){
    var oFormPrat = document.selPrat;
    var oForm = document.createPack;
    oForm.function_id.value = oFormPrat.function_id.value;
    oForm.praticien_id.value = oFormPrat.praticien_id.value;
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
  viewPack: function(pack_id){
   var url = new Url("dPprescription", "httpreq_vw_pack");
   url.addParam("pack_id", pack_id);
   url.requestUpdate("view_pack", { onComplete: function(){ Protocole.refreshListPack(pack_id); } } );
  },
  refreshListPack: function(pack_id){
    var oFormPrat = document.selPrat;
    var url = new Url("dPprescription", "httpreq_vw_list_pack");
    url.addParam("praticien_id", oFormPrat.praticien_id.value);
    url.addParam("function_id", oFormPrat.function_id.value);
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
  previewPack: function(pack_id){
    var url = new Url("dPprescription", "httpreq_preview_protocole");
    url.addParam("pack_id", pack_id);
    url.popup(800,600, "Previsualisation pack");
  }
}