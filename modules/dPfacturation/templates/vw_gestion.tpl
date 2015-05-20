{{mb_script module=facturation script=facture}}
<script>
  function checkRapport(){
    var oForm = document.printFrm;
    var oFormcompta = document.printCompta;
    // Mode comptabilite
    var compta = 0;

    if(!oForm.chir.value && oForm.a.value == "print_actes") {
      alert('Vous devez choisir un praticien');
      return false;
    }
    if(!(checkForm(oForm))){
      return false;
    }
    var url = new Url();
    url.setModuleAction("facturation", oForm.a.value);
    url.addParam("compta", compta);
    url.addElement(oForm.a);
    url.addElement(oForm._date_min);
    url.addElement(oForm._date_max);
    url.addElement(oForm.chir);

    url.addElement(oFormcompta._etat_reglement_patient);
    url.addElement(oFormcompta._etat_reglement_tiers);
    url.addElement(oFormcompta.mode);
    url.addElement(oFormcompta._type_affichage);
    url.addElement(oFormcompta.typeVue);
    url.addParam("cs", $V(oFormcompta.cs));
    url.addParam("_all_group_money", $V(oFormcompta._all_group_money));
    url.addParam("_all_group_compta", $V(oFormcompta._all_group_compta));
    if(compta == 1){
      url.popup(950, 600, "Rapport Comptabilité");
    } else {
      url.popup(950, 600, "Rapport");
    }
    return false;
  }

  function viewActes(){
    var oForm = document.printFrm;
    var oFormcompta = document.printCompta;
    if(!oForm.chir.value) {
      alert('Vous devez choisir un praticien');
      return false;
    }

    var url = new Url();
    url.setModuleAction("dPplanningOp", "vw_actes_realises");
    url.addElement(oForm._date_min);
    url.addElement(oForm._date_max);
    url.addElement(oForm.chir);
    url.addElement(oFormcompta.typeVue);
    url.addElement(oFormcompta.bloc_id);
    url.popup(950, 550, "Rapport des actes réalisés");

    return false;
  }

  function checkBills(facture_class){
    var oForm = getForm("printFrm");
    var url = new Url('tarmed', 'ajax_send_file_http');
    url.addParam('prat_id'    , oForm.chir.value);
    url.addParam('date_min'   , $V(oForm._date_min));
    url.addParam('date_max'   , $V(oForm._date_max));
    url.addParam('facture_class'  , facture_class);
    url.addParam('check'      ,true);
    url.requestUpdate('check_bill', { onComplete:function() {
      var suppressHeaders = 0;
      if ($('check_bill_ok')) {
        suppressHeaders = 1;
      }
      downloadBills(facture_class, suppressHeaders);
    }} );
  }

  function downloadBills(facture_class, suppressHeaders) {
    var oForm = getForm("printFrm");
    var url = new Url('tarmed', 'ajax_send_file_http');
    url.addParam('prat_id'  , oForm.chir.value);
    url.addParam('date_min' , $V(oForm._date_min));
    url.addParam('date_max' , $V(oForm._date_max));
    url.addParam('facture_class'  , facture_class);
    url.addParam('check'      , suppressHeaders ? 0 : 1 );
    url.addParam('suppressHeaders', suppressHeaders);
    url.popup(1000, 600);
//  url.addParam('user'     , $V(oForm.user));
//  url.addParam('pwd'      , $V(oForm.pwd));
  }

  function viewTotaux() {
    var oForm = getForm("printFrm");
    var url = new Url("facturation", "ajax_total_cotation");
    url.addParam("chir_id", $V(oForm.chir));
    url.addParam('date_min', $V(oForm._date_min));
    url.addParam('date_max', $V(oForm._date_max));
    url.popup(1000, 600);
  }
  function popupImport(facture_class) {
    var url = new Url('facturation', 'vw_rapprochement_banc');
    url.addParam('facture_class', facture_class);
    url.popup(1200, 600, 'Import des règlements');
  }
</script>
{{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
  <div id="check_bill" style="display:none;"></div>
{{/if}}
<form name="printCompta" action="?" method="get" onSubmit="return checkRapport()">
<input type="hidden" name="a" value="" />
<input type="hidden" name="dialog" value="1" />
<table class="main form">
{{if $conf.dPfacturation.CFactureCabinet.view_bill}}
  <tr>
    <th class="category" colspan="4">Comptabilité Cabinet</th>
  </tr>
  <tr>
    <td colspan="2" class="button" style="width:50%;">
      <button class="print" type="submit" onclick="document.printFrm.a.value='print_noncote';">Consultations non cotées</button>
      <div class="small-info" style="text-align:center;">
        Affichage des consultations non cotées, en fonction de la date de consultation.
      </div>
    </td>
    <td colspan="2" class="button">
      <button class="print" type="submit" onclick="document.printFrm.a.value='print_retrocession';">Rétrocession des remplacements</button>
      <div class="small-info" style="text-align:center;">
        Affichage des consultations effectuées par un remplaçant, en fonction de la date de consultation.
      </div>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="4" style="padding-top:5px;padding-bottom:5px;">
      <hr />
    </td>
  </tr>
  <tr>
    <th>Consultations gratuites</th>
    <td>
      <label for="cs_1">Oui</label>
      <input type="radio" name="cs" value="1" checked="checked"/>
      <label for="cs_0">Non</label>
      <input type="radio" name="cs" value="0" />
    </td>
    <td colspan="2"></td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="_etat_reglement_patient"}}</th>
    <td>{{mb_field object=$filter field="_etat_reglement_patient" emptyLabel="All" canNull="true"}}</td>
    <th>{{mb_label object=$filter_reglement field="mode"}}</th>
    <td>{{mb_field object=$filter_reglement field="mode" emptyLabel="All" canNull="true"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="_etat_reglement_tiers"}}</th>
    <td>{{mb_field object=$filter field="_etat_reglement_tiers" emptyLabel="All" canNull="true"}}</td>
    <th>{{mb_label object=$filter field="_type_affichage"}}</th>
    <td>{{mb_field object=$filter field="_type_affichage" canNull="true"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="_all_group_money"}}</th>
    <td>{{mb_field object=$filter field="_all_group_money" typeEnum=checkbox}}</td>
    <th>{{mb_label object=$filter field="_all_group_compta"}}</th>
    <td>{{mb_field object=$filter field="_all_group_compta" typeEnum=checkbox}}</td>
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button class="search" type="submit" onclick="document.printFrm.a.value='print_rapport';">Validation paiements</button>
      {{if $app->user_prefs.GestionFSE}}
        <button class="search" type="submit" onclick="document.printFrm.a.value='print_noemie';">Rapprochements Noemie</button>
      {{/if}}
    </td>
    <td>
      <button class="print" type="submit" onclick="document.printFrm.a.value='print_compta';" style="float:right;">Impression compta</button>
    </td>
    <td>
      <button class="print" type="submit" onclick="document.printFrm.a.value='print_bordereau';" style="float:left;">Impression bordereau</button>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <div class="small-info" style="text-align:center;">
        Affichage de l'état des paiements, en fonction des dates de consultation.<br/>
        Permet de rentrer de nouveaux paiements.
      </div>
    </td>
    <td colspan="2">
      <div class="small-info" style="text-align:center;">
        Affichage des règlements effectués, en fonction de la date de paiement.
      </div>
    </td>
  </tr>
  {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
    <tr>
      <td class="button" colspan="4">
        <hr />
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button class="print" type="submit" onclick="document.printFrm.a.value='print_tva';">Totaux TVA</button>
      </td>
      <td colspan="2"></td>
    </tr>
    <tr>
      <td colspan="2">
        <div class="small-info" style="text-align:center;">
          Affichage le total assujetti à la TVA ainsi que la répartition par taux.
        </div>
      </td>
      <td colspan="2"></td>
    </tr>
  {{/if}}
  {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
    <tr>
      <td class="button" colspan="4">
        <hr />
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="button" onclick="popupImport('CFactureCabinet');" class="hslip">Importer un fichier V11</button>
      </td>
      <td colspan="2" class="text">
        <div class="small-info" id="response_send_bill">
          Rapprochement bancaire des règlements grace au fichier V11 pour les factures de cabinet
        </div>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="4">
        <hr />
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="button" class="pdf" onclick="Facture.printGestion('bvr', 'CFactureCabinet', document.printFrm);">Impression des BVR</button>
        <button type="button" class="pdf" onclick="Facture.printGestion('justificatif', 'CFactureCabinet', document.printFrm);">Justificatifs de remboursement</button>
      </td>
      <td colspan="2" rowspan="2" class="text">
        <div class="small-info" id="response_send_bill">
          Envoi des factures à la Caisse des Médecins et stockage au format pdf.
        </div>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        {{*
        Identifiant:  <input name="user" type="text" value="" /><br/>
        Mot de passe: <input name="pwd"  type="password" value="" /><br/>
        <button type="button" class="send" onclick="checkBills();">Envoi à la Caisse des Medecins</button>
        *}}
        <button type="button" class="send" onclick="checkBills('CFactureCabinet');">Générer dossier pour la Caisse des medecins</button>
      </td>
    </tr>
  {{/if}}
{{/if}}
{{if $conf.dPfacturation.CFactureEtablissement.view_bill}}
  <tr>
    <th class="category" colspan="4">Comptabilité Etablissement</th>
  </tr>
  <tr>
    <th>
      <label for="typeVue">Type d'affichage</label>
    </th>
    <td>
      <select name="typeVue">
        <option value="1">Liste complète</option>
        <option value="2">Totaux</option>
      </select>
    </td>
    <td colspan="2" rowspan="3" class="text">
      <div class="small-info">
        Affichage de l'état des règlements de la liste des actes réalisés par l'établissement.<br/>
        La sélection d'un bloc affiche uniquement les séjours ayant des intervention placées dans celui-ci.
      </div>
    </td>
  </tr>
  <tr>
    <th>
      <label for="bloc_id">{{tr}}CBlocOperatoire{{/tr}}</label>
    </th>
    <td>
      <select name="bloc_id">
        <option value="">&mdash; Tous</option>
        {{foreach from=$blocs item=_bloc}}
          <option value="{{$_bloc->_id}}">{{$_bloc}}</option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="2">
      {{if $conf.ref_pays == 1}}
        <button type="button" class="search" onclick="viewActes();">Validation paiements</button>
      {{else}}
        <button class="print" type="submit" onclick="document.printFrm.a.value='print_actes';">Validation paiements</button>
      {{/if}}
    </td>
  </tr>
  {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
    <tr>
      <td class="button" colspan="4">
        <hr />
      </td>
    </tr>
    <tr>
    <tr>
      <td class="button" colspan="2">
        <button type="button" onclick="popupImport('CFactureEtablissement');" class="hslip">Importer un fichier V11</button>
      </td>
      <td colspan="2" class="text">
        <div class="small-info" id="response_send_bill">
          Rapprochement bancaire des règlements grace au fichier V11 pour les factures d'établissement
        </div>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="4">
        <hr />
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="button" class="pdf" onclick="Facture.printGestion('bvr', 'CFactureEtablissement', document.printFrm);">Impression des BVR</button>
        <button type="button" class="pdf" onclick="Facture.printGestion('justificatif', 'CFactureEtablissement', document.printFrm);">Justificatifs de remboursement</button>
      </td>
      <td rowspan="2" colspan="2" class="text">
        <div class="small-info" id="response_send_bill">
          Envoi des factures à la Caisse des Médecins et stockage au format pdf.
        </div>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="button" class="send" onclick="checkBills('CFactureEtablissement');">Générer dossier pour la Caisse des medecins</button>
      </td>
    </tr>
  {{/if}}
{{/if}}
<tr>
  <th class="category" colspan="4">
    Totaux
  </th>
</tr>
<tr>
  <td colspan="2" class="button">
    <button type="button" class="search" onclick="viewTotaux();">Totaux des cotations</button>
  </td>
  <td colspan="2"></td>
</tr>
</table>
</form>