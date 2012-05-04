<!-- $Id: vw_compta2.tpl 14998 2012-03-23 12:46:24Z flaviencrochard $ -->

<script type="text/javascript">
function checkRapport(){
  var oForm = document.printFrm;
  // Mode comptabilite
  var compta = 0;
  
  if(!checkForm(oForm)){
    return false;
  }
  var url = new Url("dPcabinet", oForm.a.value);
  url.addParam("compta", compta);
  url.addElement(oForm.a);
  url.addElement(oForm._date_min);
  url.addElement(oForm._date_max);
  url.addElement(oForm.chir);
  url.addElement(oForm._etat_reglement_patient);
  url.addElement(oForm._etat_reglement_tiers);
  url.addElement(oForm.mode);
  url.addElement(oForm._type_affichage);
  url.addParam("cs", $V(oForm.cs));
  if(compta == 1){
    url.popup(950, 600, "Rapport Comptabilit�");
  } else {
    url.popup(950, 600, "Rapport");
  }
  return false;
}

function changeDate(sDebut, sFin){ 
  var oForm = document.printFrm;
  oForm._date_min.value = sDebut;
  oForm._date_max.value = sFin;
  oForm._date_min_da.value = Date.fromDATE(sDebut).toLocaleDate();
  oForm._date_max_da.value = Date.fromDATE(sFin).toLocaleDate();  
}

function viewActes(etab){
  var oForm = document.printFrm;
  
  if(!oForm.chir.value) {
    alert('Vous devez choisir un praticien');
    return false;
  }

  var url = new Url("dPplanningOp", "vw_actes_realises");
  url.addElement(oForm._date_min);
  url.addElement(oForm._date_max);
  url.addElement(oForm.chir);
  url.addParam("etab", etab);
  if (etab) {
    url.addElement(oForm.tyepVueEtab);
  }
  else {
    url.addElement(oForm.typeVue);
  }
  url.popup(950, 550, "Rapport des actes r�alis�s");
  
  return false;
}

printFacture = function(edit_justificatif, edit_bvr) {
  var oForm = document.printFrm;
  var url = new Url('dPcabinet', 'edit_bvr');
  url.addParam('edition_justificatif', edit_justificatif);
  url.addParam('edition_bvr', edit_bvr);
  url.addParam('_date_min', oForm._date_min.value);
  url.addParam('_date_max', oForm._date_max.value);
  url.addParam('prat_id', oForm.chir.value);
  url.requestUpdate(SystemMessage.id);
}
</script>

<table class="main">
  <tr>
    <td>
      <form name="printFrm" action="?" method="get" onSubmit="return checkRapport()">
      <input type="hidden" name="a" value="" />
      <input type="hidden" name="dialog" value="1" />
      <table class="form">
        <tr>
          <th class="title" colspan="4">Edition de rapports</th>
        </tr>
        <tr>
          <th class="category" colspan="3">Choix de la periode</th>
          <th class="category">{{mb_label object=$filter field="_prat_id"}}</th>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_date_min"}}</th>
          <td>{{mb_field object=$filter field="_date_min" form="printFrm" canNull="false" register=true}}</td>
          <td rowspan="2">
            <table>
              <tr>
                <td>
                  <input type="radio" name="select_days" onclick="changeDate('{{$now}}','{{$now}}');"  value="day" checked="checked" /> 
                  <label for="select_days_day">Jour courant</label>
                  <br />
                  <input type="radio" name="select_days" onclick="changeDate('{{$yesterday}}','{{$yesterday}}');"  value="yesterday" /> 
                  <label for="select_days_yesterday">La veille</label>
                  <br />
                  <input type="radio" name="select_days" onclick="changeDate('{{$week_deb}}','{{$week_fin}}');" value="week" /> 
                  <label for="select_days_week">Semaine courante</label>
                  <br />
                </td>
                <td>
                  <input type="radio" name="select_days" onclick="changeDate('{{$month_deb}}','{{$month_fin}}');" value="month" /> 
                  <label for="select_days_month">Mois courant</label>
                  <br />
                  <input type="radio" name="select_days" onclick="changeDate('{{$three_month_deb}}','{{$month_fin}}');" value="three_month" /> 
                  <label for="select_days_three_month">3 derniers mois</label>
                </td>
              </tr>
            </table>
          </td>
          <td rowspan="2">
            <select name="chir">
              {{if $listPrat|@count > 1}}
              <option value="">&mdash; Tous</option>
              {{/if}}
              {{foreach from=$listPrat item=curr_prat}}
              <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}">{{$curr_prat->_view}}</option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_date_max"}}</th>
          <td>{{mb_field object=$filter field="_date_max" form="printFrm" canNull="false" register=true}} </td>
        </tr> 
        
        <tr>
          <th class="category" colspan="4">Comptabilit� "Consultations"</th>
        </tr>
        <tr>
          <th>{{mb_label object=$filter_reglement field="mode"}}</th>
          <td>{{mb_field object=$filter_reglement field="mode" emptyLabel="All" canNull="true"}}</td> 
          <td colspan="2" rowspan="3" class="text">
            <div class="big-info">
              Affichage des r�glements effectu�s, en fonction de la date de paiement.
            </div>
          </td>   
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_type_affichage"}}</th>
          <td>{{mb_field object=$filter field="_type_affichage" canNull="true"}}</td>     
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="print" type="submit" onclick="document.printFrm.a.value='print_compta2';">Impression compta</button>
            <button class="print" type="submit" onclick="document.printFrm.a.value='print_bordereau2';">Impression bordereau</button>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            <hr />
          </td>
        </tr>
        <tr>
          <th>Consultations gratuites</th>
          <td>
            Oui<input type="radio" name="cs" value="1" checked="checked"/>
            Non<input type="radio" name="cs" value="0" />
          </td>
          <td colspan="2" rowspan="4" class="text">
            <div class="big-info">
              Affichage de l'�tat des paiements, en fonction des dates de consultation.
              Permet de rentrer de nouveaux paiements.
            </div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_etat_reglement_patient"}}</th>
          <td>{{mb_field object=$filter field="_etat_reglement_patient" emptyLabel="All" canNull="true"}}</td>          
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_etat_reglement_tiers"}}</th>
          <td>{{mb_field object=$filter field="_etat_reglement_tiers" emptyLabel="All" canNull="true"}}</td>          
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="search" type="submit" onclick="document.printFrm.a.value='print_rapport2';">Validation paiements</button>
            {{if $app->user_prefs.GestionFSE}}
              <button class="search" type="submit" onclick="document.printFrm.a.value='print_noemie';">Rapprochements Noemie</button>
            {{/if}}
          </td>
        </tr>
        {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
          <tr>
            <td class="button" colspan="2">
              <button type="button" class="pdf" onclick="printFacture(0, 1);">Impression des BVR</button>
              <button type="button" class="pdf" onclick="printFacture(1, 0);">Justificatifs de remboursement</button>
            </td>
          </tr>
        {{/if}}
        <tr>
          <th class="category" colspan="4">Comptabilit� Etablissement</th>
        </tr>
        <tr>
          <th>
            <label for="typeVueEtab">Type d'affichage</label>
          </th>
          <td>
            <select name="typeVueEtab">
            <option value="1">Liste compl�te</option>
            <option value="2">Totaux</option>
            </select>
          </td>
          <td colspan="2" rowspan="2" class="text">
            <div class="big-info">
              Affichage de l'�tat des r�glements de la liste des actes r�alis�s aux blocs.
            </div>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button type="button" class="search" onclick="viewActes(1);">Validation paiements</button>
          </td>
        </tr>
        <tr>
          <th class="category" colspan="4">Comptabilit� "Interventions"</th>
        </tr>
        <tr>
          <th>
            <label for="typeVue">Type d'affichage</label>
          </th>
          <td>
            <select name="typeVue">
            <option value="1">Liste compl�te</option>
            <option value="2">Totaux</option>
            </select>
          </td>
          <td colspan="2" rowspan="2" class="text">
            <div class="big-info">
              Affichage de l'�tat des r�glements de la liste des actes r�alis�s aux blocs.
            </div>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button type="button" class="search" onclick="viewActes();">Validation paiements</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>