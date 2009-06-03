<script type="text/javascript">
var tabs = null;

Main.add(function () {
  tabs = Control.Tabs.create('tab-echantillonnage', false);
  Calendar.regField(getForm("echantillonage").debut);
});

function showTab(iTab) {
  $('btn_etape'+iTab).show();
  tabs.changeTabAndFocus("etape"+iTab);
}

function extractSelectedMultiple(url, sField){
  var oForm  = document.echantillonage;
  var oField = oForm.elements[sField];
  for (var i = 0; i < oField.options.length; i++){
    if (oField.options[i].selected) {
      url.addParam(sField, oField.options[i].value);
    }
  }
  return url;
}

function goto_etape2(){
  var oForm = document.echantillonage;
  var validation = false;
  
  if(!oForm._create_group.length || (oForm._create_group.length && oForm._create_group[0].checked)){
    if(oForm.etablissement.value == ""){
      alert("Veuillez saisir un nom pour l'etablissement");
    }else{
      validation = true;
    }
  }else{
    validation = true;
  }
  if(validation){   
    var url = new Url;
    url.setModuleAction("{{$m}}", "httpreq_echantillonnage_etape2");
    if(oForm._create_group.length && oForm._create_group[1].checked){
      url.addParam("group_id", oForm.groups_selected.value);
    }
    url.requestUpdate("etape2", {onComplete: showTab(2)});
    showTab(2);
  }
}

function goto_etape3(){
  var oForm = document.echantillonage;
  var validation1 = false;
  var validation2 = false;
  var validation3 = false;
  var sAlert = "";
  if(oForm._nb_cab.value>=1 || oForm._nb_anesth.value>=1){ validation1 = true; }
  if(oForm._create_group.length && oForm._create_group[1].checked && oForm.elements["fct_selected[]"].selectedIndex==-1){
    if(!validation1){
      sAlert += "Vous devez créer des fonctions ou en selectionner.\n";
    }
  }else{
    validation1 = true;
  }
  
  if(oForm._nb_salles.value>=1){ validation3 = true; }
  if(oForm.elements["salles_selected[]"]){
    if(oForm.elements["salles_selected[]"].selectedIndex==-1){
      if(!validation3){
        sAlert += "Vous devez créer des salles ou en selectionner.\n";
      }
    }else{
      validation3 = true;
    }
  }
  
  if(oForm._nb_services.value>=1){ validation2 = true; }
  if(oForm.elements["services_selected[]"]){
    if(oForm.elements["services_selected[]"].selectedIndex==-1){
      if(!validation2){
        sAlert += "Vous devez créer des services ou en selectionner.\n";
      }
    }else{
      validation2 = true;
    }
  }
  
  if(validation1 && validation2 && validation3){   
    var url = new Url;
    url.setModuleAction("{{$m}}", "httpreq_echantillonnage_etape3");
    url.addParam("_nb_cab", oForm._nb_cab.value);
    url.addParam("_nb_anesth", oForm._nb_anesth.value);
    if(oForm._create_group.length && oForm._create_group[1].checked){
      url = extractSelectedMultiple(url, "fct_selected[]");
    }
    url.requestUpdate("etape3", {method : "get"});
    showTab(3);
  }else{
    alert(sAlert);
  }
}

function goto_etape4(){
  var oForm = document.echantillonage;
  var validation = false;
  if(oForm._nb_prat.value>=1){
    validation = true;
  }
  if(oForm.elements["prat_selected[]"]){
    if(oForm.elements["prat_selected[]"].selectedIndex==-1){
      if(!validation){
        alert("Vous devez créer des praticiens ou en selectionner.");
      }
    }else{
      validation = true;
    }
  }else if(!validation){
    alert("Veuillez choisir le nombre de praticiens à créer.");
  }
  if(validation){
    var url = new Url;
    url.setModuleAction("{{$m}}", "httpreq_echantillonnage_etape4");
    url.addParam("_nb_services", oForm._nb_services.value);
    url.requestUpdate("etape4");
    showTab(4);
  }
}
</script>