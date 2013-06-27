var oFormTPtri = getForm("editLineTPtri");

// UpdateFields de l'autocomplete de medicaments
updateFieldsMedicamentTPtri = function(selected) {
  // Submit du formulaire avant de faire le selection d'un nouveau produit
  if(oFormTPtri._code.value){
    onSubmitFormAjax(oFormTPtri, { onComplete: function() {
      updateTPtri(selected);
      DossierMedical.reloadDossiersMedicaux();
    } } );
  }
  else {
      updateTPtri(selected);
  }
}

updateTPtri = function(selected){
  resetEditLineTPtri();
  Element.cleanWhitespace(selected);
  var dn = selected.childElements();
  $V(oFormTPtri._code, dn[0].innerHTML);
  $("_libelletri").insert("<button type='button' class='cancel notext' onclick='resetEditLineTPtri(); resetFormTPtri();'></button><a href=\"#nothing\" onclick=\"Prescription.showMonographyMedicament('','','"+selected.down(".code-cis").getText()+"')\">"+selected.down(".libelle").getText()+"</a>");
  $V(oFormTPtri.produit, '');
  $('button_submit_traitementtri').focus();
}

resetEditLineTPtri = function(){
  $("_libelletri").update("");
  oFormTPtri._code.value = '';
}

resetFormTPtri = function(){
  $V(oFormTPtri.commentaire, '');
  $V(oFormTPtri.token_poso, '');
  $('addPosoLinetri').update('');

  $V(oFormTPtri.long_cours, 1);
  $V(oFormTPtri.__long_cours, true);
  }

// Autocomplete des medicaments
var urlAuto2 = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
urlAuto2.autoComplete(oFormTPtri.produit ,"_produit_auto_completetri", {
  minChars: 3,
  updateElement: updateFieldsMedicamentTPtri,
  callback: function(input, queryString){
    return (queryString + "&produit_max=40");
  }
} );

refreshAddPosotri = function(code){
  var url = new Url("dPprescription", "httpreq_vw_select_poso");
  url.addParam("_code", code);
  url.addParam("addform", "tri");
  url.requestUpdate("addPosoLinetri");
}
