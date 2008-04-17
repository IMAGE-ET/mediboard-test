var ImedsResultsWatcher = {
  debut         : null,
  fin           : null,
  listNumSejours: {},
  listIdSejours : {},
  results       : {},
  addSejour: function(sejour_id, num_dossier) {
    if(sejour_id && num_dossier && num_dossier != '-') {
      this.listNumSejours[sejour_id] = num_dossier;
      this.listIdSejours[num_dossier] = sejour_id;
    }
  },
  loadResults: function(debut, fin) {
    var url = new Url;
    url.setModuleAction("dPImeds"     , "httpreq_soap_labo_results");
    url.addObjectParam("date_debut"   , this.debut);
    url.addObjectParam("date_fin"     , this.fin);
    url.addObjectParam("list_sejours" , Object.values(this.listNumSejours).join("-"));
    url.requestUpdate("systemMsg", { waitingText: "Attente des résultats de laboratoire" } );
  },
  setResults: function(results) {
    if(results.GetInfoLaboResult > 1) {
      this.results = results.listeInfoLabo.InfoLabo;
    }
    else if(this.results.listeInfoLabo) {
      this.results = {0: results.listeInfoLabo.InfoLabo};
    }
    $H(this.results).each(function(result) { ImedsResultsWatcher.showResult(result.value) });
  },
  showResult: function(result) {
    sejour_id = this.listIdSejours[result.NumSejour];
    if(result.IsLaboEntreDate && sejour_id && $("labo_hot_for_"+sejour_id)) {
      $("labo_hot_for_"+sejour_id).show();
    }else if(result.IsLabo && sejour_id && $("labo_for_"+sejour_id)) {
      $("labo_for_"+sejour_id).show();
    }
  }
}