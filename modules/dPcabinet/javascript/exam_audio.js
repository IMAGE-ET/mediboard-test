// $Id$

// --- Audiométrie Tonale

var iMinTonalPerte = -10;
var iMaxTonalPerte = 120;
var iMaxIndexFrequence = 7;
  
function changeTonalValue(sCote, sConduction, iFrequence, iNewValue) {
  var oForm = document.editFrm;
  var sElementName = printf("_%s_%s[%i]", sCote, sConduction, iFrequence);
  var oElement = oForm.elements[sElementName];
  var nFrequence = 125 * Math.pow(2, iFrequence);
  
  // Do not use !iNewValue which is also true for a 0 value    
  if (iNewValue == null) {
    sInvite = printf("Modifier la perte pour l'oreille %s à %iHz'", sCote, nFrequence);
    sAdvice = printf("Merci de fournir une valeur comprise (en dB) entre %i et %i", iMinTonalPerte, iMaxTonalPerte);

    iNewValue = prompt(sInvite + "\n" + sAdvice, oElement.value);

    // Do not user !iNewValue which is also true for empty string    
    if (iNewValue == null) {
      return;
    }
    
    if (isNaN(iNewValue) || iNewValue < iMinTonalPerte || iNewValue > iMaxTonalPerte) {
      alert("Valeur incorrecte : " + iNewValue + "\n" + sAdvice);
      return;
    }
  }
  
  oElement.value = iNewValue;
  if(oForm.examaudio_id.value==""){
    oForm.submit();
  }else{
    submitFormAjax(oForm, 'systemMsg', { onComplete : window["reloadGraphTonale"+sCote]});
  }
}

function changeTonalValueMouse(event, sCote) {
  var oImg = $("tonal_" + sCote);

  var iLegendMargin = 75; 
  var oGraphMargins = {
    left  : 45,
    top   : 30,
    right : 20 + iLegendMargin,
    bottom: 15
  }
  
  var oGraphRect = {
    x : Position.cumulativeOffset(oImg)[0] + oGraphMargins.left,
    y : Position.cumulativeOffset(oImg)[1] + oGraphMargins.top ,
    w : oImg.width  - oGraphMargins.left - oGraphMargins.right ,
    h : oImg.height - oGraphMargins.top  - oGraphMargins.bottom
  }
  
  var iStep = oGraphRect.w / (iMaxIndexFrequence+1);
  var iRelatX = Event.pointerX(event) - oGraphRect.x; 
  var iRelatY = Event.pointerY(event) - oGraphRect.y;
  
  var iSelectedIndex = parseInt(iRelatX / iStep);
  var iSelectedDb = parseInt(iMinTonalPerte - (iRelatY / oGraphRect.h * (iMinTonalPerte - iMaxTonalPerte)));
      
  if (iRelatX < 0 || iRelatX > oGraphRect.w || iSelectedDb < iMinTonalPerte || iSelectedDb > iMaxTonalPerte) {
    alert("Merci de cliquer à l'intérieur de l'audiogramme");
    return;
  }
  
  var oForm = document.editFrm;
  changeTonalValue(sCote, $V(oForm._conduction), iSelectedIndex, iSelectedDb);
}

function changeTonalValueMouseGauche(event) {
  changeTonalValueMouse(event, "gauche");
}

function changeTonalValueMouseDroite(event) {
  changeTonalValueMouse(event, "droite");
}

// --- Tympanométrie

var iMinTympanAdmittance = 0;
var iMaxTympanAdmittance = 15;
var iMaxIndexPression = 7;

function changeTympanValue(sCote, iPression, iNewValue) {
  var oForm = document.editFrm;
  var sElementName = printf("_%s_tympan[%i]", sCote, iPression);
  var oElement = oForm.elements[sElementName];
  
  // Do not use !iNewValue which is also true for a 0 value    
  if (iNewValue == null) {
    var sPression = 100*iPression - 400;
    var sInvite = printf("Modifier l'admittance pour l'oreille %s à la pression %s mm H²0", sCote, sPression);
    var sAdvice = printf("Merci de fournir une valeur comprise (en dixième de ml) entre %i et %i", iMinTympanAdmittance, iMaxTympanAdmittance);

    iNewValue = prompt(sInvite + "\n" + sAdvice, oElement.value);

    // Do not use !iNewValue which is also true for empty string    
    if (iNewValue == null) {
      return;
    }
    
    if (isNaN(iNewValue) || iNewValue < iMinTympanAdmittance || iNewValue > iMaxTympanAdmittance) {
      alert("Valeur incorrecte : " + iNewValue + "\n" + sAdvice);
      return;
    }
  }
  
  oElement.value = iNewValue;
  oForm.action += "#tympan";
  if(oForm.examaudio_id.value==""){
    oForm.submit();
  }else{
    submitFormAjax(oForm, 'systemMsg', { onComplete : window["reloadGraphTympan"+sCote]});
  }
}


function changeTympanValueMouse(event, sCote) {
  var oImg = $("tympan_" + sCote);

  var oGraphMargins = {
    left  : 35,
    top   : 20,
    right : 10,
    bottom: 30
  }
  
  var oGraphRect = {
    x : Position.cumulativeOffset(oImg)[0] + oGraphMargins.left,
    y : Position.cumulativeOffset(oImg)[1] + oGraphMargins.top ,
    w : oImg.width  - oGraphMargins.left - oGraphMargins.right ,
    h : oImg.height - oGraphMargins.top  - oGraphMargins.bottom
  }

  var iStep = oGraphRect.w / (iMaxIndexPression+1);
  var iRelatX = Event.pointerX(event) - oGraphRect.x; 
  var iRelatY = Event.pointerY(event) - oGraphRect.y;

  var iSelectedIndex = parseInt(iRelatX / iStep);
  var iSelectedAdmittance = parseInt(iMaxTympanAdmittance - (iRelatY / oGraphRect.h * (iMaxTympanAdmittance - iMinTympanAdmittance)));
      
  if (iRelatX < 0 || iRelatX > oGraphRect.w || iSelectedAdmittance < iMinTympanAdmittance || iSelectedAdmittance > iMaxTympanAdmittance) {
    alert("Merci de cliquer à l'intérieur du tympanogramme");
    return;
  }
  
  changeTympanValue(sCote, iSelectedIndex, iSelectedAdmittance);
}

function changeTympanValueMouseGauche(event) {
  changeTympanValueMouse(event, "gauche");
}


function changeTympanValueMouseDroite(event) {
  changeTympanValueMouse(event, "droite");
}

// --- Audiométrie vocale

var iMinVocalDB = 0;
var iMaxVocalDB = 120;
var iMinVocalPc = 0;
var iMaxVocalPc = 100;
var iMaxKey = 7;

function changeVocalValue(sCote, iKey, iNewDBValue, iNewPcValue) {
  var oForm = document.editFrm;
  var sElements = printf("_%s_vocale[%i]", sCote, iKey);

  var oElementDB = oForm.elements[sElements + "[0]"];
  if (!iNewDBValue) {
    sInvite = printf("Modifier la valeur de réponse pour le point #%d concernant l'oreille %s", iKey, sCote);
    sAdvice = printf("Merci de fournir une valeur comprise (en dB) entre %i et %i", iMinVocalDB, iMaxVocalDB);

    iNewDBValue = prompt(sInvite + "\n" + sAdvice, oElementDB.value);

    // Do not user !iNewValue which is also true for empty string    
    if (iNewDBValue == null) {
      return;
    }
    
    if (isNaN(iNewDBValue) || iNewDBValue < iMinVocalDB || iNewDBValue > iMaxVocalDB) {
      alert("Valeur incorrecte : " + iNewDBValue + "\n" + sAdvice);
      return;
    }
  }

  var oElementPc = oForm.elements[sElements + "[1]"];
  if (!iNewPcValue) {
    sInvite = printf("Modifier le pourcentage pour le point #%d concernant l'oreille %s", iKey, sCote);
    sAdvice = printf("Merci de fournir une valeur comprise (en pourcentage) entre %i et %i", iMinVocalPc, iMaxVocalPc);

    iNewPcValue = prompt(sInvite + "\n" + sAdvice, oElementPc.value);

    // Do not user !iNewValue which is also true for empty string    
    if (iNewPcValue == null) {
      return;
    }
    
    if (isNaN(iNewPcValue) || iNewPcValue < iMinVocalPc || iNewPcValue > iMaxVocalPc) {
      alert("Valeur incorrecte : " + iNewPcValue + "\n" + sAdvice);
      return;
    }
  }

  oElementDB.value = iNewDBValue;
  oElementPc.value = iNewPcValue;
  oForm.action += "#vocal";
  if(oForm.examaudio_id.value==""){
    oForm.submit();
  }else{
    submitFormAjax(oForm, 'systemMsg', { onComplete : reloadGraphVocale});
  }
}

function changeVocalValueMouse(event) {
  var oImg = $("image_vocal");

  var oGraphMargins = {
    left  : 40,
    top   : 45,
    right : 20,
    bottom: 20
  }
  
  var oGraphRect = {
    x : Position.cumulativeOffset(oImg)[0] + oGraphMargins.left,
    y : Position.cumulativeOffset(oImg)[1] + oGraphMargins.top ,
    w : oImg.width  - oGraphMargins.left - oGraphMargins.right ,
    h : oImg.height - oGraphMargins.top  - oGraphMargins.bottom
  }
  
  var iRelatX = Event.pointerX(event) - oGraphRect.x; 
  var iRelatY = Event.pointerY(event) - oGraphRect.y;

  if (iRelatX < 0 || iRelatX > oGraphRect.w || iRelatY < 0 || iRelatY > oGraphRect.h) {
    alert("Merci de cliquer à l'intérieur de l'audiogramme");
    return;
  }
  
  var iSelectedDB = parseInt(iRelatX / oGraphRect.w * iMaxVocalDB);
  var iSelectedPc = parseInt((1 -iRelatY / oGraphRect.h) * iMaxVocalPc);
  
  var oForm = document.editFrm;
  var sCote = $V(oForm._oreille);
  var iKey = 0;
  for (iKey = 0; iKey <= iMaxKey; ++iKey) {
    var sElements = printf("_%s_vocale[%i]", sCote, iKey);
    var oElementDB = oForm.elements[sElements + "[0]"];
    var oElementPc = oForm.elements[sElements + "[1]"];

    if (!oElementDB.value && !oElementPc.value) {
      break;
    }

    if (oElementDB.value == iSelectedDB) {
      break;
    }
  }
  
  if (iKey > iMaxKey) {
  	alert("Impossible d'ajouter un point supplémentaire");
  	return;
  }
  
  changeVocalValue(sCote, iKey, iSelectedDB, iSelectedPc);
}

Main.add(function () {
  new PairEffect("dataTonal");
  new PairEffect("dataVocal");
});

function reloadAllGraphs() {
  reloadGraphTonale();
  reloadGraphTympan();
}

function reloadBilan(){
  var oForm = document.editFrm;
  var url = new Url("dPcabinet", "httpreq_vw_examaudio_bilan");
  url.addParam("examaudio_id", oForm.examaudio_id.value);
  url.requestUpdate('td_bilan', { waitingText : null });
}

function reloadGraphTonale(sCote){
  var oForm = document.editFrm;
  if(typeof(sCote)=="string" && (sCote=="droite" || sCote=="gauche")){
    var url = new Url("dPcabinet", "httpreq_vw_examaudio_tonal");
    url.addParam("side", sCote);
    url.addParam("examaudio_id", oForm.examaudio_id.value);
    url.requestUpdate('td_graph_tonal_'+sCote, { waitingText : null });
  }else{
    reloadGraphTonale('gauche');
    reloadGraphTonale('droite');
    reloadBilan();
  }
}
function reloadGraphTonaledroite(){
  reloadGraphTonale('droite');
  reloadBilan();
}
function reloadGraphTonalegauche(){
  reloadGraphTonale('gauche');
  reloadBilan();
}

function reloadGraphTympan(sCote){
  var oForm = document.editFrm;
  if(typeof(sCote)=="string" && (sCote=="droite" || sCote=="gauche")){
    var url = new Url("dPcabinet", "httpreq_vw_examaudio_tympan");
    url.addParam("side", sCote);
    url.addParam("examaudio_id", oForm.examaudio_id.value);
    url.requestUpdate('td_graph_tympan_'+sCote, { waitingText : null });
  }else{
    reloadGraphVocale();
    reloadGraphTympan('droite');
    reloadGraphTympan('gauche');
  }
}
function reloadGraphTympandroite(){
  reloadGraphTympan('droite');
}
function reloadGraphTympangauche(){
  reloadGraphTympan('gauche');
}

function reloadGraphVocale(){
  var oForm = document.editFrm;
  var url = new Url("dPcabinet", "httpreq_vw_examaudio_vocale");
  url.addParam("examaudio_id", oForm.examaudio_id.value);
  url.requestUpdate('td_graph_vocal', { waitingText : null });
}