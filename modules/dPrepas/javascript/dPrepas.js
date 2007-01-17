var odPrepas;
var notWhitespace = /\S/;
var saveInProgress = 0;

function loadDatadPrepas(){
  odPrepas = MbStorage.load("dPrepas");
  Object.extend(config, odPrepas["config"]);
}

//************************************

function loadServices(){
  var url = new Url;
  url.setModuleAction("dPhospi" , "httpreq_get_services_offline");
  url.addParam("dialog"         , "1");
  url.requestUpdateOffline("systemMsg");
}

function recupdata(){
  var url = new Url;
  url.setModuleAction("dPrepas" , "httpreq_get_infos_offline");
  url.addParam("dialog"         , "1");
  url.requestUpdateOffline("systemMsg");
}


function synchro_repas(affectation_id, typerepas_id, del, iRepasId){
  var iTmpRepasId = odPrepas["oPlanningRepas"][affectation_id][typerepas_id]["_tmp_repas_id"];
  odPrepas["oPlanningRepas"][affectation_id][typerepas_id]["_tmp_repas_id"] = 0;
  if(del == 1){
    // Suppresion d'un repas
    odPrepas["oPlanningRepas"][affectation_id][typerepas_id]["repas_id"] = 0;
    odPrepas["oRepas"][iRepasId] = null;
  }else{
    // Ajout d'un repas    
    alert("Creation d'un repas\n" + affectation_id + " - " + typerepas_id + " - " + del + " - " + iRepasId);
    odPrepas["oPlanningRepas"][affectation_id][typerepas_id]["repas_id"] = iRepasId;
    odPrepas["oRepas"][iRepasId]       = {};
    odPrepas["oRepas"][iRepasId]       = odPrepas["oRepas"][0][iTmpRepasId];
    odPrepas["oRepas"][iRepasId]["_tmp_repas_id"] = 0;
    odPrepas["oRepas"][iRepasId]["repas_id"]      = iRepasId;
    odPrepas["oRepas"][0][iTmpRepasId]            = null;
  }
  //Mémorisation des données
  MbStorage.save("dPrepas",odPrepas);
}




function submitRepas(){
  var oForm = document.editRepas;
  var templateID  = 'templateNoRepas';
  var vwListPlats = Dom.cloneElemById(templateID,true);
  var oAllRepas   = odPrepas["oRepas"];
  
  oForm.action = config["urlMediboard"];
  
  // Permet d'ajouter des champs dans le formulaire
  Dom.cleanWhitespace(vwListPlats);
  var tbodyVwPlats = vwListPlats.childNodes[0];
  var linesPlats   = tbodyVwPlats.childNodes;
  var inputMenuId  = Dom.createInput("hidden", "menu_id", "");
  var thTitle      = linesPlats[0].childNodes[0];
  thTitle.appendChild(inputMenuId);
  Dom.writeElem('listPlat',vwListPlats);
  
  $H(oAllRepas).each(function (pair) { 
    var oObj = pair.value;
    var iKey = pair.key;
    if(iKey == 0){
      $H(oObj).each(function (pair) {
        if(pair.value != null){
          Form.fromObject(oForm, pair.value);
          var sNameMsgId = oObj[pair.key]["affectation_id"] + "_" + oObj[pair.key]["typerepas_id"];
          submitFormAjaxOffline(oForm, sNameMsgId);
        }
      });
    }else{
      if(pair.value != null){
        Form.fromObject(oForm, oObj);
        var sNameMsgId = oObj["affectation_id"] + "_" + oObj["typerepas_id"];
        submitFormAjaxOffline(oForm, sNameMsgId);
      }
    }
  });
}

function view_planning(){
  $('divPlanningRepas').show();
  $('divRepas').hide();
}

function saveRepas(){
  var oForm          = document.editRepas;
  var oDataForm      = new Object;
  oDataForm          = Form.toObject(oForm);
  var affectation_id = oDataForm["affectation_id"];
  var typerepas_id   = oDataForm["typerepas_id"];
  var elem           = $(affectation_id + '_' + typerepas_id);
  
  // Mémorisation des informations
  if(oDataForm["repas_id"] != 0){
    odPrepas["oRepas"][oDataForm["repas_id"]] = oDataForm;
  }else{
    var iTmpRepasId = oDataForm["_tmp_repas_id"];
    if(iTmpRepasId == 0){
      // Création d'un repas
      var iTmpRepasId = (new Date).getTime(); 
      oDataForm["_tmp_repas_id"] = iTmpRepasId;
      odPrepas["oPlanningRepas"][affectation_id][typerepas_id]["_tmp_repas_id"] = iTmpRepasId;
      odPrepas["oPlanningRepas"][affectation_id][typerepas_id]["repas_id"] = 0;
    }
    if(oDataForm["del"] == 1){
      oDataForm = null;
      odPrepas["oPlanningRepas"][affectation_id][typerepas_id]["_tmp_repas_id"] = 0;
      odPrepas["oPlanningRepas"][affectation_id][typerepas_id]["repas_id"] = 0;
    }
    odPrepas["oRepas"][0][iTmpRepasId] = oDataForm;
  }
  
  // Mémorisation des données
  MbStorage.save("dPrepas",odPrepas);
  
  // Etat dans le planning
  elem.innerHTML = "";
  viewEtatRepas(elem,affectation_id,typerepas_id);
  $('divPlanningRepas').show();
  $('divRepas').hide();
}

//Extraction des plats et plats de remplacements
function extractListPlat(oMenu, oRepas){
  var oFields     = {"plat1":null,"plat2":null,"plat3":null,"plat4":null,"plat5":null,"boisson":null,"pain":null};
  var iModifRepas = oMenu["modif"];
  var oPlats      = odPrepas["oPlats"];
  
  for(key in oFields){
    oFields[key] = document.createElement("optgroup");
    oFields[key].setAttribute("label" , "Remplacements possibles");
  }
  
  if(iModifRepas == 1){
    $H(oPlats).each(function (pair) {
      var oPlatRemplacement = pair.value;
      if(oPlatRemplacement["typerepas"] == oMenu["typerepas"]){
        var bSelectedObj = false;
        var iTypePlat = oPlatRemplacement["type"];
        if(oRepas && oRepas[iTypePlat] == oPlatRemplacement["plat_id"]){
          bSelectedObj = true;
        }
        Dom.createOptSelect(oPlatRemplacement["plat_id"],oPlatRemplacement["nom"],bSelectedObj,oFields[iTypePlat]);
      }
    } );
  }
  
  for(key in oFields){
    var oOptgroup = oFields[key];
    if (oOptgroup.hasChildNodes()){
      var oSelectObj   = Dom.createSelect(key);
      var bSelectedObj = false;
      if(oRepas && (oRepas[key] == "" || oRepas[key] == null)){
        bSelectedObj = true;
      }
      Dom.createOptSelect("",oMenu[key],bSelectedObj,oSelectObj);
      oSelectObj.appendChild(oFields[key]);
      oFields[key] = oSelectObj;
    }else{
      // Ajouter un champ hidden pour le plat
      var oInputplat = Dom.createInput("hidden", key, "");
      var oNamePlat  = document.createTextNode(oMenu[key]);
      oInputplat.appendChild(oNamePlat);
      oFields[key] = oInputplat;
    }    
  }
  return oFields;
}

function vwListMenu(typerepas_id, repas_id, tmp_repas_id){
  var oListMenus       = odPrepas["oMenus"][typerepas_id];
  
  var oVwListMenus     = Dom.cloneElemById('templateListRepas',true);
  var oLineVwMenus     = oVwListMenus.childNodes;
  var oLigneMenu       = document.createElement("tr");
  var oCelluleMenu     = Dom.createTd("button");
  var oCelluleNomMenu  = Dom.createTd("text");
  
  oLigneMenu.appendChild(oCelluleNomMenu);
  var aNameCellule = new Array("diabete","sans_sel","sans_residu");
  
  for(i=1;i<=aNameCellule.length;i++){
    var oCloneCell = oCelluleMenu.cloneNode(false)
    oLigneMenu.appendChild(oCloneCell);
  }
  
  $H(oListMenus).each(function (pair) {
    var oLine       = oLigneMenu.cloneNode(true);
    var oMenu       = pair.value;
    var oChildsLine = oLine.childNodes;
    oChildsLine.item(0).innerHTML = "<a href='#' onclick='vwPlats(" + oMenu["_id"] + ")'>" + oMenu["nom"] + "</a>";
    
    for(i=1;i<=aNameCellule.length;i++){
      var sTextNode = "";
      if(oMenu[aNameCellule[i]] == 1){
        sTextNode = "<strong>Oui</strong>";
      }
      oChildsLine.item(i).innerHTML = sTextNode;
    }
    oLineVwMenus.item(1).appendChild(oLine);
  } );
  Dom.writeElem('tdlistMenus',oVwListMenus);
}

function vwPlats(menu_id){
  var oForm        = document.editRepas;
  var repas_id     = oForm.repas_id.value;
  var tmp_repas_id = oForm._tmp_repas_id.value;
  var typerepas_id = oForm.typerepas_id.value;
  var del          = oForm._del.value;
  var oAllRepas    = odPrepas["oRepas"];
  var oMenus       = odPrepas["oMenus"];
  
  if(repas_id != 0){
    var oRepas = oAllRepas[repas_id];
  }
  if(tmp_repas_id != 0){
    var oRepas = oAllRepas[0][tmp_repas_id];
  }
  if(menu_id == "" || menu_id == null){
    var templateID = 'templateNoRepas';
  }else{
    var templateID = 'templateListPlats';
  }
  
  var vwListPlats  = Dom.cloneElemById(templateID,true);
  Dom.cleanWhitespace(vwListPlats);
  var tbodyVwPlats = vwListPlats.childNodes[0];
  var linesPlats   = tbodyVwPlats.childNodes;
  var inputMenuId  = Dom.createInput("hidden", "menu_id", menu_id);
  var thTitle      = linesPlats[0].childNodes[0];
  
  if(menu_id != "" && menu_id != null){
    if(oRepas){
      var oListMenus = oMenus[oRepas["typerepas_id"]];
    }else if(typerepas_id){
      var oListMenus = oMenus[typerepas_id];
    }
    
    var oMenu = oListMenus[menu_id];
    var sMenuName   = document.createTextNode(oMenu["nom"]);
    thTitle.appendChild(sMenuName);

    // Ecriture des plats et plats remplacements
    var aSelect = extractListPlat(oMenu, oRepas);
    for(i=1;i<linesPlats.length;i++){
      var oLinePlatEnCours  = linesPlats[i].childNodes[1];
      var sNameTypePlat     = oLinePlatEnCours.getAttribute("id");
      oLinePlatEnCours.appendChild(aSelect[sNameTypePlat]);
    }
  }
  
  thTitle.appendChild(inputMenuId);
  
  // Ecriture des boutons du formulaire
  var trButton = document.createElement("tr");
  var tdButton = Dom.createTd("button", "2");
  if(del == 0 && (repas_id != 0 || tmp_repas_id != 0)){
    // modification
    var buttonMod = Dom.cloneElemById('templateButtonMod',true);
    var buttonDel = Dom.cloneElemById('templateButtonDel',true);
    tdButton.appendChild(buttonMod);
    tdButton.appendChild(buttonDel);
  }else{
    var buttonAdd = $('templateButtonAdd').cloneNode(true);
    tdButton.appendChild(buttonAdd);
  }
  trButton.appendChild(tdButton);
  vwListPlats.appendChild(trButton);

  Dom.writeElem('listPlat',vwListPlats);
}

function vwRepas(affectation_id, typerepas_id){
  var repas          = odPrepas["oPlanningRepas"][affectation_id][typerepas_id];
  var oAllRepas      = odPrepas["oRepas"];
  var oAffectation   = odPrepas["oAffectations"][affectation_id];
  var oType          = odPrepas["oListTypeRepas"][typerepas_id];
  var oForm          = document.editRepas;
  var oButtonBack    = Dom.cloneElemById('templateHrefBack', true);
  var oConfigdPrepas = odPrepas["config"];

  oForm.repas_id.value       = repas["repas_id"];
  oForm._tmp_repas_id.value  = repas["_tmp_repas_id"];
  oForm.typerepas_id.value   = typerepas_id;
  oForm.affectation_id.value = affectation_id;
  oForm.del.value            = 0;
  oForm._del.value           = 0;
  oForm.date.value           = oConfigdPrepas["CRepas_date"];
  var sDate                  = oConfigdPrepas["CRepas_date"].substr(8,2) 
                               + " / " + oConfigdPrepas["CRepas_date"].substr(5,2) 
                               + " / " + oConfigdPrepas["CRepas_date"].substr(0,4);
  if(typeof repas != "object"){
    return;
  }
  $('divPlanningRepas').hide();
  if(repas["repas_id"] != 0){
    var oRepas = oAllRepas[repas["repas_id"]];
  }
  if(repas["_tmp_repas_id"] != 0){
    var oRepas = oAllRepas[0][repas["_tmp_repas_id"]];
  }
  if(oRepas){
    oForm._del.value = oRepas["del"];
  }
  if(oRepas && oRepas["del"] == 0){
    // Repas existant et non supprimé
    vwPlats(oRepas["menu_id"]);
    var oTextThTitle = document.createTextNode("Modification d'un repas");
    $('thRepasTitle').className  = "title modify"
  }else{
    Dom.writeElem('listPlat');
    var oTextThTitle = document.createTextNode("Enregistrement d'un repas");
    $('thRepasTitle').className  = "title"
  }
  Dom.writeElem('thRepasTitle',oButtonBack);
  $('thRepasTitle').appendChild(oTextThTitle);
  
  vwListMenu(typerepas_id, repas["repas_id"], repas["_tmp_repas_id"]);
  $('tdRepasChambre').innerHTML   = oAffectation["_view"];
  $('tdRepasTypeRepas').innerHTML = oType["nom"];
  $('tdRepasDate').innerHTML      = sDate;
  $('divRepas').show();
}

//Fonction d'ecriture de l'état d'un repas pour une affectation et un type de repas
function viewEtatRepas(elem, affectation_id, typerepas_id){
  // Récupération des repas
  var repas     = odPrepas["oPlanningRepas"][affectation_id][typerepas_id];
  var oAllRepas = odPrepas["oRepas"];
  
  // Création des différents état de Repas
  var imgRepasPlanifie = Dom.createImg("images/icons/tick-dPrepas.png");
  var imgNoRepas       = Dom.createImg("images/icons/no.png");
  var imgRepasFlag     = Dom.createImg("images/icons/flag.png");
  
  if(typeof repas == "object"){
    var urlimg = document.createElement("a");
    urlimg.setAttribute("href", "#");
    urlimg.setAttribute("onclick", "vwRepas('" + affectation_id + "','" + typerepas_id + "')");
    
    if(repas["repas_id"] == 0 && repas["_tmp_repas_id"] == 0){
      // Non plannifié
      urlimg.appendChild(imgRepasFlag);
    }else{
      if(repas["repas_id"] != 0){
        var oRepas = oAllRepas[repas["repas_id"]];
      }
      if(repas["_tmp_repas_id"] != 0){
        var oRepas = oAllRepas[0][repas["_tmp_repas_id"]];
      }
      
      if(oRepas && oRepas["del"] == 1){
        urlimg.appendChild(imgRepasFlag);
      }else if(oRepas && (oRepas["menu_id"] == "" || oRepas["menu_id"] == null)){
        urlimg.appendChild(imgNoRepas);
      }else{
        urlimg.appendChild(imgRepasPlanifie);
      }
    }
    elem.appendChild(urlimg);
  }else{
    // Ne pas planifié de repas ici
    elem.innerHTML = '-';
  }
}

//Fonction d'ecriture du planning pour 1 jour et 1 service donné
function createPlanning(){
  loadDatadPrepas();
  
  var oTypeRepas    = odPrepas["oListTypeRepas"];
  var oAffectations = odPrepas["oAffectations"];
  var oSejours      = odPrepas["oSejours"];
  var oPatients     = odPrepas["oPatients"];
  
  var oTblPlanning = document.createElement("table");
  oTblPlanning.className = "tbl";
  oTblPlanning.setAttribute("id", "tablePlanning");
  
  // Création de la ligne vide
  var oEmptyLine = document.createElement("tr");
  var oFirstLine = oEmptyLine.cloneNode(false);
  var oEmptyTD   = document.createElement("td");
  var oEmptyTH   = document.createElement("th");
  
  oEmptyTH.className = "category";
  
  oEmptyLine.appendChild(oEmptyTD.cloneNode(false));
  oEmptyLine.appendChild(oEmptyTD.cloneNode(false));
  oFirstLine.appendChild(oEmptyTD.cloneNode(false));
  oFirstLine.appendChild(oEmptyTD.cloneNode(false));
  
  $H(oTypeRepas).each(function (pair) {
    var typeRepas = pair.value;
    // Ligne vide
    var oCelluleTD = oEmptyTD.cloneNode(false);
    oCelluleTD.setAttribute("id", typeRepas["_id"]);
    oEmptyLine.appendChild(oCelluleTD);
    // Premiere ligne
    var oCelluleTH = oEmptyTH.cloneNode(false);
    oCelluleTH.innerHTML = typeRepas["nom"];
    oFirstLine.appendChild(oCelluleTH);
  } );
  
  // Bouton pour envoyer les repas
  oButtonSendRepas = document.createElement("button");
  oButtonSendRepas.setAttribute("class", "tick");
  oButtonSendRepas.setAttribute("type", "button");
  oButtonSendRepas.setAttribute("onclick", "submitRepas()");
  oButtonSendRepas.setAttribute("style", "float:right;");
  oButtonSendRepas.innerHTML = "Envoyer"
  var oTdButtonSend = Dom.createTd(null, oFirstLine.childNodes.length);
  oTdButtonSend.appendChild(oButtonSendRepas);
  
  oTblPlanning.appendChild(oTdButtonSend);
  oTblPlanning.appendChild(oFirstLine);
  
  $H(oAffectations).each(function (pair) {
    var oLine          = oEmptyLine.cloneNode(true);
    var oCurrentAffect = pair.value; 
    if(oLine.hasChildNodes()){
      // Récupération des informations necessaires
      var oSejour  = oSejours[oCurrentAffect["sejour_id"]];
      var oPatient = oPatients[oSejour["patient_id"]];
      
      var oChildsLine               = oLine.childNodes;
      oChildsLine.item(0).innerHTML = oCurrentAffect["_view"];
      oChildsLine.item(1).innerHTML = oPatient["_view"];
      
      if(oChildsLine.length>2){
        for (var i = 2; i < oChildsLine.length; i++) {
          var elem        = oChildsLine.item(i);
          var typerepasid = elem.getAttribute("id");
          elem.className  = "button";
          elem.setAttribute("id", oCurrentAffect["_id"] + "_" + typerepasid);
          viewEtatRepas(elem,oCurrentAffect["_id"],typerepasid);
        }
      }
    }
    oTblPlanning.appendChild(oLine);
  } );
  
  Dom.writeElem('divPlanningRepas',oTblPlanning);
  $('divPlanningRepas').show();
  $('divRepas').hide();
}