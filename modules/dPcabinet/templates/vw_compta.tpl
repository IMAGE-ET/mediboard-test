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
  if(oForm.a.value == "print_compta"){
    // Declenchement du mode comptabilite
    oForm.a.value = "print_rapport";
    var compta = 1;
  }
  url.setModuleAction("dPcabinet", oForm.a.value);
  url.addParam("compta", compta);
  url.addElement(oForm._date_min);
  url.addElement(oForm.a);
  url.addElement(oForm._date_min);
  url.addElement(oForm._date_max);
  url.addElement(oForm.chir);
  url.addElement(oForm._etat_reglement_patient);
  url.addElement(oForm._etat_reglement_tiers);
  url.addElement(oForm.patient_mode_reglement);
  url.addElement(oForm._type_affichage);
  url.addParam("cs", getCheckedValue(oForm.cs));
  if(compta == 1){
    url.popup(700, 550, "Rapport Comptabilité");
  } else {
    url.popup(700, 550, "Rapport");
  }
  return false;
}

function changeDate(sDebut, sFin){ 
  var oForm = document.printFrm;
  oForm._date_min.value = sDebut;
  oForm._date_max.value = sFin;
  $('printFrm__date_min_da').innerHTML = Date.fromDATE(sDebut).toLocaleDate();
  $('printFrm__date_max_da').innerHTML = Date.fromDATE(sFin).toLocaleDate();  
}

function pageMain() {
  regFieldCalendar("printFrm", "_date_min");
  regFieldCalendar("printFrm", "_date_max");
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
          <th class="title" colspan="3">Edition de rapports</th>
        </tr>
        <tr>
          <th class="category" colspan="3">Choix de la periode</th>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_date_min"}}</th>
          <td class="date">{{mb_field object=$filter field="_date_min" form="printFrm" canNull="false"}}</td>
          <td rowspan="2">
            <input type="radio" name="select_days" onclick="changeDate('{{$now}}','{{$now}}');"  value="day" checked="checked" /> 
            <label for="select_days_day">Jour courant</label>
            <br /><input type="radio" name="select_days" onclick="changeDate('{{$week_deb}}','{{$week_fin}}');" value="week" /> 
            <label for="select_days_week">Semaine courante</label>
            <br /><input type="radio" name="select_days" onclick="changeDate('{{$month_deb}}','{{$month_fin}}');" value="month" /> 
            <label for="select_days_month">Mois courant</label>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_date_max"}}</th>
          <td class="date">{{mb_field object=$filter field="_date_max" form="printFrm" canNull="false"}} </td>
        </tr> 
        
        <tr>
          <th class="category" colspan="3">Critères d'affichage</th>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_prat_id"}}</th>
          <td colspan="2">
            <select name="chir">
              <!-- <option value="">&mdash; Tous &mdash;</option> -->
              {{foreach from=$listPrat item=curr_prat}}
              <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}">{{$curr_prat->_view}}</option>
              {{/foreach}}
            </select>
          </td>
        <tr>
          <th>{{mb_label object=$filter field="patient_mode_reglement"}}</th>
          <td colspan="2">{{mb_field object=$filter field="patient_mode_reglement" defaultOption="&mdash; Tout type &mdash;" canNull="true"}}</td>    
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_type_affichage"}}</th>
          <td colspan="2">{{mb_field object=$filter field="_type_affichage" canNull="true"}}</td>     
        </tr>
        <tr>
          <td class="button" colspan="3">
            <button class="print" type="submit" onclick="document.printFrm.a.value='print_compta';">Impression compta</button>
            <button class="print" type="submit" onclick="document.printFrm.a.value='print_bordereau';">Impression bordereau</button>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="3">
            <hr />
          </td>
        </tr>
        <tr>
          <th>Consultations gratuites</th>
          <td colspan="2">
            Oui<input type="radio" name="cs" value="1" checked="checked"/>
            Non<input type="radio" name="cs" value="0" />
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_etat_reglement_patient"}}</th>
          <td colspan="2">{{mb_field object=$filter field="_etat_reglement_patient" defaultOption="&mdash; Tous  &mdash;" canNull="true"}}</td>          
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_etat_reglement_tiers"}}</th>
          <td colspan="2">{{mb_field object=$filter field="_etat_reglement_tiers" defaultOption="&mdash; Tous  &mdash;" canNull="true"}}</td>          
        </tr>
        <tr>
          <td class="button" colspan="3">
            <button class="search" type="submit" onclick="document.printFrm.a.value='print_rapport';">Validation paiements</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>