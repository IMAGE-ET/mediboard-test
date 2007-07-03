// $Id: $

var PlageSelector = {
  ePlage_id           : null,  // Identifiant de la plage
  eSDate              : null,  // Date de la plage
  e_hour_entree_prevue: null,
  e_min_entree_prevue : null,
  e_date_entree_prevue: null,
  options : {
    width : 450,
    height: 450
  },


  pop: function(iChir, iHour_op, iMin_op, iGroup_id, iOperation_id) {
    
    if (checkChir() && checkDuree()) {
      var url = new Url();
      url.setModuleAction("dPplanningOp", "plage_selector");
      url.addParam("chir", iChir);
      url.addParam("curr_op_hour", iHour_op);
      url.addParam("curr_op_min", iMin_op);
      url.addParam("group_id", iGroup_id);
      url.addParam("operation_id",iOperation_id);
      url.popup(this.options.width, this.options.height, "Plage");
    } 
    
  },
  
  
  
  
  set: function(plage_id, sDate, bAdm) {
    var oOpForm     = document.editOp;
    var oSejourForm = document.editSejour;
 
    if(!oSejourForm._duree_prevue.value) {
      oSejourForm._duree_prevue.value = 0;
    }

    if (plage_id) {
      if(oOpForm.plageop_id.value != plage_id) {
        oOpForm.rank.value = 0;
      }
     
      this.ePlage_id.value = plage_id;
      this.eSDate.value = sDate;
      if(this.ePlage_id.onchange) {
        this.ePlage_id.onchange();
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
        this.e_hour_entree_prevue.value = dAdm.getHours();
        this.e_min_entree_prevue.value = dAdm.getMinutes();
        this.e_date_entree_prevue.value = makeDATEFromDate(dAdm);
        var div_rdv_adm = document.getElementById("editSejour__date_entree_prevue_da");
        div_rdv_adm.innerHTML = makeLocaleDateFromDate(dAdm);
      }
      
      this.e_date_entree_prevue.onchange();
    }
  }
}
