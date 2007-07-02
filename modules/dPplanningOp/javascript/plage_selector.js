// $Id: $

var PlageSelector = {
  eChir : null,
  eHour_op: null,
  eMin_op: null,
  eGroup_id : null,
  eOperation_id: null,
  e_hour_entree_prevue: null,
  e_min_entree_prevue: null,
  e_date_entree_prevue: null,
  options : {
    width : 450,
    height: 450
  },


  pop: function() {
    
    if (checkChir() && checkDuree()) {
      var url = new Url();
      url.setModuleAction("dPplanningOp", "plage_selector");
      url.addParam("chir", this.eChir.value);
      url.addParam("curr_op_hour", this.eHour_op.value);
      url.addParam("curr_op_min", this.eMin_op.value);
      url.addParam("group_id", this.eGroup_id.value);
      //{{if $op->operation_id}}
      url.addParam("operation_id",this.eOperation_id.value);
      //{{/if}}
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
    
      updateSortiePrevue();
    }
  }
}
