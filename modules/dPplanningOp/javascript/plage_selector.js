// $Id: $

var PlageOpSelector = {
  sForm               : null,  // Ici, on ne se sert pas de ce formulaire
  sPlage_id           : null,  // Identifiant de la plage
  sDate               : null,  // Date de la plage
  sPlage_id_easy      : null,
  sDateEasy           : null,
  s_hour_entree_prevue: null,
  s_min_entree_prevue : null,
  s_date_entree_prevue: null,
  options : {
    width : 450,
    height: 450
  },

  pop: function(iChir, iHour_op, iMin_op, iGroup_id, iOperation_id) {
    if (checkChir() && checkDuree()) {
      var url = new Url();
      url.setModuleAction("dPplanningOp", "plage_selector");
      url.addParam("chir"        , iChir);
      url.addParam("curr_op_hour", iHour_op);
      url.addParam("curr_op_min" , iMin_op);
      url.addParam("group_id"    , iGroup_id);
      url.addParam("operation_id", iOperation_id);
      url.popup(this.options.width, this.options.height, "Plage");
    }
  },

  set: function(plage_id, sDate, bAdm) {
    var oOpForm     = document.editOp;
    var oSejourForm = document.editSejour;
    var oOpFormEasy = document.editOpEasy;
 
    if(!oSejourForm._duree_prevue.value) {
      oSejourForm._duree_prevue.value = 0;
    }

    if (plage_id) {
      if(oOpForm.plageop_id.value != plage_id) {
        oOpForm.rank.value = 0;
      }
      
      Form.Element.setValue(oOpForm[this.sPlage_id], plage_id);
      Form.Element.setValue(oOpForm[this.sDate]    , sDate);
      if(oOpFormEasy) {
        Form.Element.setValue(oOpFormEasy[this.sPlage_id_easy], plage_id);
        Form.Element.setValue(oOpFormEasy[this.sDate_easy]    , sDate);
      }
     
      var dAdm = makeDateFromLocaleDate(sDate);
      oOpForm._date.value = makeDATEFromDate(dAdm);
      // Initialize admission date according to operation date
      switch(bAdm) {
        case 0 :
          dAdm.setHours(this.heure_entree_veille);
          dAdm.setDate(dAdm.getDate()-1);
          break;
        case 1 :
          dAdm.setHours(this.heure_entree_jour);
          break;
      }
    
      if (bAdm != 2) {
        oSejourForm[this.s_hour_entree_prevue].value = dAdm.getHours();
        oSejourForm[this.s_min_entree_prevue].value  = dAdm.getMinutes();
        oSejourForm[this.s_date_entree_prevue].value = makeDATEFromDate(dAdm);
        var div_rdv_adm = document.getElementById("editSejour__date_entree_prevue_da");
        div_rdv_adm.innerHTML = makeLocaleDateFromDate(dAdm);
      }
      
      oSejourForm[this.s_date_entree_prevue].onchange();
    }
  }
}
