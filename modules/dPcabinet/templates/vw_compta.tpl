<!-- $Id$ -->

<script type="text/javascript">
function checkRapport(){
  var oForm = document.printFrm;
  // Mode comptabilite
  var compta = 0;
  
  if(!(checkForm(oForm))){
    return false;
  }
  var url = new Url();
  url.setModuleAction("dPcabinet", oForm.a.value);
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
    url.popup(950, 600, "Rapport Comptabilité");
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

function viewActes(){
  var oForm = document.printFrm;
  
  if(!oForm.chir.value) {
    alert('Vous devez choisir un praticien');
    return false;
  }

  var url = new Url();
  url.setModuleAction("dPplanningOp", "vw_actes_realises");
  url.addElement(oForm._date_min);
  url.addElement(oForm._date_max);
  url.addElement(oForm.chir);
  url.addElement(oForm.typeVue);
  url.popup(950, 550, "Rapport des actes réalisés");
  
  return false;
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
          <th class="category" colspan="4">Comptabilité "Consultations"</th>
        </tr>
        <tr>
          <th>{{mb_label object=$filter_reglement field="mode"}}</th>
          <td>{{mb_field object=$filter_reglement field="mode" defaultOption="&mdash; Tout type &mdash;" canNull="true"}}</td> 
          <td colspan="2" rowspan="3" class="text">
            <div class="big-info">
              Affichage des règlements effectués, en fonction de la date de paiement.
            </div>
          </td>   
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_type_affichage"}}</th>
          <td>{{mb_field object=$filter field="_type_affichage" canNull="true"}}</td>     
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="print" type="submit" onclick="document.printFrm.a.value='print_compta';">Impression compta</button>
            <button class="print" type="submit" onclick="document.printFrm.a.value='print_bordereau';">Impression bordereau</button>
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
              Affichage de l'état des paiements, en fonction des dates de consultation.
              Permet de rentrer de nouveaux paiements.
            </div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_etat_reglement_patient"}}</th>
          <td>{{mb_field object=$filter field="_etat_reglement_patient" defaultOption="&mdash; Tous  &mdash;" canNull="true"}}</td>          
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_etat_reglement_tiers"}}</th>
          <td>{{mb_field object=$filter field="_etat_reglement_tiers" defaultOption="&mdash; Tous  &mdash;" canNull="true"}}</td>          
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="search" type="submit" onclick="document.printFrm.a.value='print_rapport';">Validation paiements</button>
            {{if $app->user_prefs.GestionFSE}}
              <button class="search" type="submit" onclick="document.printFrm.a.value='print_noemie';">Rapprochements Noemie</button>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category" colspan="4">Comptabilité "Interventions"</th>
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
          <td colspan="2" rowspan="2" class="text">
            <div class="big-info">
              Affichage de l'état des règlements de la liste des actes réalisé aux blocs.
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