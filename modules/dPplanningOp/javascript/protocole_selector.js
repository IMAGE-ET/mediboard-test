// $Id$

ProtocoleSelector = {
  sForm            : null,
  sForSejour       : null,
  sChir_id         : null,
  sChir_id_easy    : null,
  sChir_view       : null,
  sLibelle         : null,
  sLibelle_easy    : null,
  sCodes_ccam      : null,
  sCodes_ccam_easy : null,
  sCote            : null,
  sDuree_prevu     : null,
  sDuree_prevu_heure : null,
  sTime_op         : null,
  sMateriel        : null,
  sExamenPerop     : null,
  sExamen          : null,
  sDepassement     : null,
  sForfait         : null,
  sFournitures     : null,
  sRques_op        : null,
  sLibelle_sejour  : null,
  sType            : null,
  sTypePec         : null,
  sFacturable      : null,
  sTypeAnesth      : null,
  sDuree_uscpo     : null,
  sDuree_preop     : null,
  sConvalescence   : null,
  sDP              : null,
  sRques_sej       : null,
  sProtoPrescAnesth: null,
  sProtoPrescChir  : null,
  sServiceId       : null,
  sServiceId_easy  : null,
  sForceType       : null,
  sPresencePreop   : null,
  sPresencePostop  : null,
  sUf_hebergement_id : null,
  sUf_medicale_id  : null,
  sUf_soins_id     : null,
  sCharge_id       : null,
  sTypesRessourcesIds : null,
  sExamExtempo   : null,
  options : {},

  pop: function() {
    var oForm     = (this.sForm && getForm(this.sForm)) || getForm("editOp");
    var oFormEasy = getForm("editOpEasy");
    var oSejourForm = getForm("editSejour");
    
    var url = new Url("dPplanningOp", "vw_protocoles");
    url.addParam("dialog", 1);
    url.addParam("chir_id", oForm[this.sChir_id].value);
    
    if (this.sForceType) {
      url.addParam("sejour_type", this.sForceType);
    }
    
    url.addParam("singleType", this.sForSejour == 1 ? 'sejour': 'interv');
    //url.modal(this.options);
    url.requestModal(1000, 700, this.options);
    url.modalObject.observe("afterClose", function() {
      ProtocoleSelector.reloadInitCCAMSelector(oForm.name);
    });
  },
  
  set: function(protocole) {
    var oOpForm     = getForm("editOp");
    var oSejourForm = getForm("editSejour");
    var oOpFormEasy = getForm("editOpEasy");

    // Champs de l'intervention
    if (oOpForm) {
      if (protocole.chir_id && protocole.chir_view) {
        $V(oOpForm[this.sChir_view], protocole.chir_view, true);
        $V(oOpForm[this.sChir_id], protocole.chir_id, true);
      }

      $V(oOpForm[this.sServiceId], protocole.service_id, true);
      if(oOpFormEasy) {
        if (protocole.chir_id && protocole.chir_view) {
          $V(oOpFormEasy[this.sChir_id_easy]   , protocole.chir_id);
        }
        $V(oOpFormEasy[this.sServiceId_easy] , protocole.service_id);
        $V(oOpFormEasy[this.sLibelle_easy]   , protocole.libelle);
        $V(oOpFormEasy[this.sCodes_ccam_easy], protocole.codes_ccam);
      }
      
      $V(oOpForm[this.sCodes_ccam],        protocole.codes_ccam);
      $V(oOpForm[this.sLibelle],           protocole.libelle);
      $V(oOpForm[this.sPresencePreop],     protocole.presence_preop);
      $V(oOpForm[this.sPresencePreop+"_da"], protocole.presence_preop);
      $V(oOpForm[this.sPresencePostop],    protocole.presence_postop);
      $V(oOpForm[this.sPresencePostop+"_da"], protocole.presence_postop);
      $V(oOpForm[this.sCote],              protocole.cote);
      $V(oOpForm[this.sTypeAnesth],        protocole.type_anesth);
      $V(oOpForm[this.sTime_op],           protocole._time_op);
      $V(oOpForm[this.sMateriel],          protocole.materiel);
      $V(oOpForm[this.sExamenPerop],       protocole.exam_per_op);
      $V(oOpForm[this.sExamen],            protocole.examen);
      $V(oOpForm[this.sDuree_uscpo],       protocole.duree_uscpo);
      $V(oOpForm[this.sDuree_preop],       protocole.duree_preop);
      $V(oOpForm[this.sExamExtempo],       protocole.exam_extempo);

      if (oOpForm[this.sTypesRessourcesIds]) {
        var types_ressources_ids = $V(oOpForm[this.sTypesRessourcesIds]);
        if (types_ressources_ids != "") {
          types_ressources_ids += "," + protocole._types_ressources_ids;
        }
        else {
          types_ressources_ids = protocole._types_ressources_ids;
        }

        $V(oOpForm[this.sTypesRessourcesIds], types_ressources_ids);
      }
      
      if (oOpForm[this.sDepassement] && oOpForm[this.sForfait] && oOpForm[this.sFournitures]) {
        $V(oOpForm[this.sDepassement],       protocole.depassement, false);
        $V(oOpForm[this.sForfait],           protocole.forfait, false);
        $V(oOpForm[this.sFournitures],       protocole.fournitures, false);
      }
      
      $V(oOpForm[this.sRques_op], protocole.rques_operation);
      $V(oOpForm[this.sProtoPrescAnesth], protocole.protocole_prescription_anesth_id);
    }
    else {
      $V(oSejourForm[this.sChir_id], protocole.chir_id, true);
      $V(oSejourForm[this.sChir_view], protocole.chir_view, true);
    }
    
    // Champs du séjour
    if(!oSejourForm.sejour_id.value || oSejourForm[this.sDuree_prevu].value < protocole.duree_hospi) {
      $V(oSejourForm[this.sDuree_prevu], protocole.duree_hospi);
      if (this.sType) {
        $V(oSejourForm[this.sType], protocole.type, false);
      }
    }

    if (this.sCharge_id) {
      window.updateListCPI(oSejourForm, function() {
        oSejourForm[this.sCharge_id].value = protocole.charge_id;
      }.bind(this, protocole));
    }

    $V(oSejourForm[this.sDuree_prevu_heure], protocole.duree_heure_hospi);

    if (this.sUf_hebergement_id && oSejourForm[this.sUf_hebergement_id] && !oSejourForm[this.sUf_hebergement_id].value) {
      $V(oSejourForm[this.sUf_hebergement_id], protocole.uf_hebergement_id);
    }
    if (this.sUf_medicale_id && oSejourForm[this.sUf_medicale_id] && !oSejourForm[this.sUf_medicale_id].value) {
      $V(oSejourForm[this.sUf_medicale_id], protocole.uf_medicale_id);
    }
    if (this.sUf_soins_id && oSejourForm[this.sUf_soins_id] && !oSejourForm[this.sUf_soins_id].value) {
      $V(oSejourForm[this.sUf_soins_id], protocole.uf_soins_id);
    }
    if (this.sTypePec) {
      $V(oSejourForm[this.sTypePec], protocole.type_pec);
    }
    if (this.sFacturable) {
      $V(oSejourForm[this.sFacturable], protocole.facturable);
    }
    if(this.sServiceId && oSejourForm[this.sServiceId] && (!oSejourForm.sejour_id.value || !oSejourForm[this.sServiceId].value)) {
      $V(oSejourForm[this.sServiceId], protocole.service_id);
    }
    if(this.sDP && (!oSejourForm.sejour_id.value || !oSejourForm[this.sDP].value)) {
      $V(oSejourForm[this.sDP], protocole.DP);
    }
    if(!oSejourForm.sejour_id.value || !oSejourForm[this.sLibelle_sejour].value) {
      $V(oSejourForm[this.sLibelle_sejour], protocole.libelle_sejour);
    }
    if(this.sConvalescence && (oSejourForm.sejour_id.value && oSejourForm[this.sConvalescence].value)) {
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
    
    if (oSejourForm[this.sProtoPrescChir] && protocole.protocole_prescription_chir_id != 'prot-') {
      $V(oSejourForm[this.sProtoPrescChir], protocole.protocole_prescription_chir_id);
      $V(oSejourForm.libelle_protocole, protocole.libelle_protocole_prescription_chir);
    }
    else {
      $V(oSejourForm.libelle_protocole, protocole.libelle_protocole_prescription_chir);
      $V(oSejourForm[this.sProtoPrescChir], "");
    } 
    
    if (window.refreshViewProtocoleAnesth) {
      refreshViewProtocoleAnesth(protocole.protocole_prescription_anesth_id);
    }
  },
  reloadInitCCAMSelector: function(form_name) {
    if (window.CCAMSelector) {
      CCAMSelector.init = function(){
        var oForm     = (ProtocoleSelector.sForm && getForm(ProtocoleSelector.sForm)) || getForm("editOp");
        this.sForm  = oForm.name;
        this.sView  = "_codes_ccam";
        this.sChir  = "chir_id";
        this.sClass = "_class";
        this.pop();
      }
    }
  }
};
