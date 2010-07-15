// $Id$

ProtocoleSelector = {
  sForm            : null,
  sForSejour       : null,
  sChir_id         : null,
  sChir_id_easy    : null,
  sLibelle         : null,
  sLibelle_easy    : null,
  sCodes_ccam      : null,
  sCodes_ccam_easy : null,
  sDuree_prevu     : null,
  sHour_op         : null,
  sMateriel        : null,
  sExamen          : null,
  sDepassement     : null,
  sForfait         : null,
  sFournitures     : null,
  sRques_op        : null,
  sLibelle_sejour  : null,
  sType            : null,
  sConvalescence   : null,
  sDP              : null,
  sRques_sej       : null,
  sProtoPrescAnesth: null,
  sProtoPrescChir  : null,
  sServiceId	   : null,
  sServiceId_easy  : null,
  options : {
    width : 800,
    height: 500
  },

  pop: function() {
    var oForm     = (this.sForm && getForm(this.sForm)) || document.editOp;
    var oFormEasy = document.editOpEasy;
    var oSejourForm = document.editSejour;
    var url = new Url("dPplanningOp", "vw_protocoles");
    url.addParam("chir_id", oForm[this.sChir_id].value);
    url.setFragment(this.sForSejour == 1 ? 'sejour': 'interv');
    url.popup(this.options.width, this.options.height, "Protocole");
  },
  
  set: function(protocole) {
    var oOpForm     = document.editOp;
    var oSejourForm = document.editSejour;
    var oOpFormEasy = document.editOpEasy;
    
    // Champs de l'intervention
    if (oOpForm) {
      $V(oOpForm[this.sChir_id], protocole.chir_id, true);
      $V(oOpForm[this.sServiceId], protocole.service_id, true);
      if(oOpFormEasy) {
        $V(oOpFormEasy[this.sChir_id_easy]   , protocole.chir_id);
        $V(oOpFormEasy[this.sServiceId_easy] , protocole.service_id);
        $V(oOpFormEasy[this.sLibelle_easy]   , protocole.libelle);
        $V(oOpFormEasy[this.sCodes_ccam_easy], protocole.codes_ccam);
      }
      
      $V(oOpForm[this.sCodes_ccam],        protocole.codes_ccam);
      $V(oOpForm[this.sLibelle],           protocole.libelle);
      $V(oOpForm[this.sHour_op],           protocole._hour_op);
      $V(oOpForm[this.sMin_op],            protocole._min_op);
      $V(oOpForm[this.sMateriel],          protocole.materiel);
      $V(oOpForm[this.sExamen],            protocole.examen);
      
      if (oOpForm[this.sDepassement] && oOpForm[this.sForfait] && oOpForm[this.sFournitures]) {
        $V(oOpForm[this.sDepassement],       protocole.depassement, false);
        $V(oOpForm[this.sForfait],           protocole.forfait, false);
        $V(oOpForm[this.sFournitures],       protocole.fournitures, false);
      }
      
      $V(oOpForm[this.sRques_op], protocole.rques_operation);
    }
    else {
      $V(oSejourForm[this.sChir_id], protocole.chir_id, true);
    }
    
    // Champs du séjour
    if(!oSejourForm.sejour_id.value || oSejourForm[this.sDuree_prevu].value < protocole.duree_hospi) {
      $V(oSejourForm[this.sDuree_prevu], protocole.duree_hospi);
      oSejourForm[this.sType].value = protocole.type;
    }
    if(!oSejourForm.sejour_id.value || !oSejourForm[this.sServiceId].value) {
      $V(oSejourForm[this.sServiceId], protocole.service_id);
    }
    if(!oSejourForm.sejour_id.value || !oSejourForm[this.sDP].value) {
      $V(oSejourForm[this.sDP], protocole.DP);
    }
    if(!oSejourForm.sejour_id.value || !oSejourForm[this.sLibelle_sejour].value) {
      $V(oSejourForm[this.sLibelle_sejour], protocole.libelle_sejour);
    }
    if(oSejourForm.sejour_id.value && oSejourForm[this.sConvalescence].value) {
      $V(oSejourForm[this.sConvalescence], oSejourForm[this.sConvalescence].value+"\n"+protocole.convalescence);
    } else {
      $V(oSejourForm[this.sConvalescence], protocole.convalescence);
    }
    if(oSejourForm.sejour_id.value && oSejourForm[this.sRques_sej].value) {
      $V(oSejourForm[this.sRques_sej], oSejourForm[this.sRques_sej].value+"\n"+protocole.rques_sejour);
    } else {
      $V(oSejourForm[this.sRques_sej], protocole.rques_sejour);
    }
    
    if (window.refreshListCCAM) {
      refreshListCCAM("expert");
      refreshListCCAM("easy");
    }
    
    if (oSejourForm[this.sProtoPrescAnesth]) {
      if(protocole.protocole_prescription_anesth_id){
        $V(oSejourForm[this.sProtoPrescAnesth], "prot-"+protocole.protocole_prescription_anesth_id);
      } else {
        $V(oSejourForm[this.sProtoPrescAnesth], "");
      }
    }
    refreshViewProtocoleAnesth(protocole.protocole_prescription_anesth_id);
    refreshListProtocolesPrescription(protocole.chir_id, oSejourForm[this.sProtoPrescChir], protocole.protocole_prescription_chir_id);
  }
};
