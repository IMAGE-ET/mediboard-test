// $Id$

var PlageOpSelector = {
  sForm               : null,  // Ici, on ne se sert pas de ce formulaire
  sPlage_id           : null,  // Identifiant de la plage
  sSalle_id           : null,  // Identifiant de la salle
  sDate               : null,  // Date de la plage
  sPlage_id_easy      : null,
  sSalle_id_easy      : null,
  sDateEasy           : null,
  s_hour_entree_prevue: null,
  s_min_entree_prevue : null,
  s_date_entree_prevue: null,
  
  prepared: {
    plage_id: null,
    sDate: null,
    bAdm: null,
    dAdm : null
  },
  
  options : {
    width : 600,
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

  set: function(plage_id, salle_id, sDate, bAdm, typeHospi, hour_entree, min_entree) {
    // Declaration de formulaires
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
           
      var dAdm = Date.fromDATE(sDate);
      // Initialize admission date according to operation date
      if (bAdm == "veille") {
        dAdm.addDays(-1);
      }
    
      if (bAdm != "aucune") {
        dAdm.setHours(hour_entree);
        dAdm.setMinutes(min_entree);
    
        oSejourForm._date_entree_prevue_da.value = dAdm.toLocaleDate();
      }
      
      oSejourForm._curr_op_date.value = sDate;
        
      if(typeHospi == "comp" && oSejourForm[this.sType].value=="ambu"){
        oSejourForm[this.sType].value = "comp";
      }
    }  
    
    // Sauvegarde des valeurs dans l'objet prepared
    this.prepared.dAdm = dAdm;
    this.prepared.plage_id = plage_id;
    this.prepared.salle_id = salle_id;
    this.prepared.sDate = sDate;
    this.prepared.bAdm = bAdm;
    
    // Lancement de l'execution du set
    window.setTimeout( window.PlageOpSelector.doSet , 1);
  },
  
  
  doSet: function(){
    var oOpForm     = document.editOp;
    var oSejourForm = document.editSejour;
   
    $V(oOpForm[PlageOpSelector.sPlage_id], PlageOpSelector.prepared.plage_id);
    $V(oOpForm[PlageOpSelector.sSalle_id], PlageOpSelector.prepared.salle_id);
    $V(oOpForm[PlageOpSelector.sDate]    , PlageOpSelector.prepared.sDate);
   
    if(PlageOpSelector.prepared.bAdm != "aucune"){ 
      $V(oSejourForm[PlageOpSelector.s_hour_entree_prevue], PlageOpSelector.prepared.dAdm.getHours());
      $V(oSejourForm[PlageOpSelector.s_min_entree_prevue],  PlageOpSelector.prepared.dAdm.getMinutes());
      $V(oSejourForm[PlageOpSelector.s_date_entree_prevue], PlageOpSelector.prepared.dAdm.toDATE());
    }
  }
}
