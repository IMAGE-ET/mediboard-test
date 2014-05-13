updateDebiteur = function(debiteur_id) {
  var url = new Url('dPfacturation', 'ajax_edit_debiteur');
  url.addParam('debiteur_id'   , debiteur_id);
  url.addParam('debiteur_desc' , 1);
  url.requestUpdate("reload_debiteur_desc");
}

delReglement = function(reglement_id, facture_class, facture_id){
  var oForm = getForm('reglement-delete');
  $V(oForm.reglement_id, reglement_id);

  return confirmDeletion(oForm, { ajax: true, typeName:'le règlement' }, {
    onComplete : function() {
      if ($('a_reglements_consult')) {
        Reglement.reload(true);
      }
      reloadFactureModal(facture_id, facture_class);
      if (!$('load_facture')) {
        Facture.url.refreshModal();
      }
      else {
        if ($('a_reglements_consult')) {
          Reglement.reload(true);
        }
        reloadFactureModal(facture_id, facture_class);
      }
    }
  });
}

editReglementDate = function(reglement_id, date, facture_id, facture_class){
  var oForm = getForm('reglement-edit-date');
  $V(oForm.reglement_id, reglement_id);
  $V(oForm.date,         date);

  return onSubmitFormAjax(oForm, function() {
    if ($('a_reglements_consult')) {
      Reglement.reload(true);
    }
    reloadFactureModal(facture_id, facture_class);
  });
}

editAquittementDate = function(date, facture_id, facture_class){
  var oForm = getForm('edit-date-aquittement-'+facture_class+'-'+facture_id);
  $V(oForm.patient_date_reglement,     date);

  return onSubmitFormAjax(oForm, function() {
    if ($('a_reglements_consult')) {
      Reglement.reload(true);
    }
    reloadFactureModal(facture_id, facture_class);
  });
}

addReglement = function (form){
  return onSubmitFormAjax(form, function() {
    if ($('a_reglements_consult')) {
      Reglement.reload(true);
    }
    reloadFactureModal($V(form.object_id), $V(form.object_class));
  });
}

modifMontantBVR = function (form, num_bvr){
  var eclat = num_bvr.split('>')[0];
  form.montant.value = eclat.substring(2, 12)/100;
}