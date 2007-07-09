// $Id: $

var ProtocoleSelector = {
  options : {
    width : 700,
    height: 500
  },

  pop: function() {
    var url = new Url();
    url.setModuleAction("dPplanningOp", "vw_protocoles");
    url.addParam("chir_id", this.eChir_id.value);
    url.popup(this.options.width, this.options.height, "Protocole");
  },
  
  set: function(protocole) {  
    Form.Element.setValue(this.eChir_id, protocole.chir_id);
    Form.Element.setValue(this.eDuree_prevu, protocole.duree_hospi);
    Form.Element.setValue(this.eChir_id_easy, protocole.chir_id);
    Form.Element.setValue(this.eLibelle_easy, protocole.libelle);
    Form.Element.setValue(this.eCodes_ccam_easy, protocole.codes_ccam); 
    
    this.eCodes_ccam.value    = protocole.codes_ccam;
    this.eLibelle.value       = protocole.libelle;
    this.eHour_op.value       = protocole._hour_op;
    this.eMin_op.value        = protocole._min_op;
    this.eMateriel.value      = protocole.materiel;
    this.eExamen.value        = protocole.examen;
    this.eDepassement.value   = protocole.depassement;
    this.eForfait.value       = protocole.forfait;
    this.eFournitures.value   = protocole.fournitures;
    this.eRques_op.value      = protocole.rques_operation;
    this.eType.value          = protocole.type;
    this.eConvalescence.value = protocole.convalescence;
    this.eDP.value            = protocole.DP;
    this.eRques_sej.value     = protocole.rques_sejour;
    
    refreshListCCAM("expert");
    refreshListCCAM("easy");
  }
}
