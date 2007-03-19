<!-- $Id$ -->

<script type="text/javascript">
function checkFormPrint() {
  var form = document.paramFrm;
    
  if(!(checkForm(form))){
    return false;
  }
  
  popPlanning();
}

function popCode(type) {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "code_selector");
  url.addElement(document.paramFrm.chir);
  url.addParam("type", type);
  url.popup(600, 500, type);
}

function setCode(code, type) {
  var oForm = document.paramFrm;
  var oField = oForm.code_ccam;
  oField.value = code;
}

function popPlanning() {
  form = document.paramFrm;
  var url = new Url;
  url.setModuleAction("dPbloc", "view_planning");
  url.addElement(form.deb);
  url.addElement(form.fin);
  url.addElement(form.vide);
  url.addElement(form.code_ccam, "CCAM");
  url.addElement(form.type);
  url.addElement(form.chir);
  url.addElement(form.spe);
  url.addElement(form.salle);
  url.popup(700, 550, 'Planning');
}

function changeDate(sDebut, sFin){
  var oForm = document.paramFrm;
  var date_debut  = makeDateFromDATE(sDebut);
  var date_fin    = makeDateFromDATE(sFin);
  
  oForm.deb.value = sDebut;
  oForm.fin.value = sFin;
  $('paramFrm_deb_da').innerHTML = makeLocaleDateFromDate(date_debut);
  $('paramFrm_fin_da').innerHTML = makeLocaleDateFromDate(date_fin);
}

function changeDateCal(){
  var oForm = document.paramFrm;
  oForm.select_days[0].checked = false;
  oForm.select_days[1].checked = false;
  oForm.select_days[2].checked = false;
  oForm.select_days[3].checked = false;
}
function pageMain() {
  regFieldCalendar("paramFrm", "deb");
  regFieldCalendar("paramFrm", "fin");
}

</script>


<form name="paramFrm" action="?m=dPbloc" method="post" onsubmit="return checkFormPrint()">

<table class="main">
  <tr>
    <td>

      <table class="form">
        <tr><th class="category" colspan="3">Choix de la période</th></tr>
        <tr>
          <th><label for="deb" title="Date de début de la recherche">Début</label></th>
          <td class="date">
            <div id="paramFrm_deb_da">{{$now|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" onchange="changeDateCal()" name="deb" class="notNull date" value="{{$now}}" />
            <img id="paramFrm_deb_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de début"/>
          </td>
          <td rowspan="2">
            <input type="radio" name="select_days" onclick="changeDate('{{$now}}','{{$now}}');"  value="day" checked="checked" /> 
              <label for="select_days_day">Jour courant</label>
            <br /><input type="radio" name="select_days" onclick="changeDate('{{$tomorrow}}','{{$tomorrow}}');" value="tomorrow" /> 
              <label for="select_days_tomorrow">Lendemain</label>
            <br /><input type="radio" name="select_days" onclick="changeDate('{{$week_deb}}','{{$week_fin}}');" value="week" /> 
              <label for="select_days_week">Semaine courante</label>
            <br /><input type="radio" name="select_days" onclick="changeDate('{{$month_deb}}','{{$month_fin}}');" value="month" /> 
              <label for="select_days_month">Mois courant</label>
          </td>
        </tr>
        <tr>
          <th><label for="fin" title="Date de fin de la recherche">Fin</label></th>
          <td class="date">
            <div id="paramFrm_fin_da">{{$now|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" onchange="changeDateCal()" name="fin" class="notNull date moreEquals|deb" value="{{$now}}" />
            <img id="paramFrm_fin_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de fin"/>
          </td>
        </tr>
        <tr>
          <th><label for="vide" title="Afficher ou cacher les plages vides dans le rapport">Afficher les plages vides</label></th>
          <td colspan="2"><input type="checkbox" name="vide" /></td>
        </tr>
        <tr>
          <th><label for="code_ccam" title="Rechercher en fonction d'un code CCAM">Code CCAM</label></th>
          <td><input type="text" name="code_ccam" size="10" value="" /></td>
          <td class="button"><button type="button" class="search" onclick="popCode('ccam')">sélectionner un code</button></td>
        </tr>
      </table>

    </td>
    <td>

      <table class="form">
        <tr><th class="category" colspan="2">Choix des paramètres de tri</th></tr>
        <tr>
          <th><label for="type" title="Recherche en fonction de la présence dans le planning">Affichage des interventions</label></th>
          <td><select name="type">
            <option value="0">&mdash; Toutes les interventions &mdash;</option>
            <option value="1">insérées dans le planning</option>
            <option value="2">à insérer dans le planning</option>
          </select></td>
        </tr>
        <tr>
          <th><label for="chir" title="Rechercher en fonction du praticien">Praticiens</label></th>
          <td><select name="chir">
            <option value="0">&mdash; Tous les praticiens &mdash;</option>
            {{foreach from=$listPrat item=curr_prat}}
              <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" >
                {{$curr_prat->_view}}
              </option>
            {{/foreach}}
          </select></td>
        </tr>
        <tr>
          <th><label for="spe" title="Rechercher en fonction d'une spécialité opératoire">Specialité</label></th>
          <td><select name="spe">
            <option value="0">&mdash; Toutes les spécialités &mdash;</option>
            {{foreach from=$listSpec item=curr_spec}}
              <option value="{{$curr_spec->function_id}}" class="mediuser" style="border-color: #{{$curr_spec->color}};">
                {{$curr_spec->text}}
              </option>
            {{/foreach}}
          </select></td>
        </tr>
        <tr>
          <th><label for="salle" title="Rechercher en fonciton d'une salle d'opération">Salle</label></th>
          <td><select name="salle">
            <option value="0">&mdash; Toutes les salles &mdash;</option>
            {{foreach from=$listSalles item=curr_salle}}
	            <option value="{{$curr_salle->salle_id}}">{{$curr_salle->nom}}</option>
            {{/foreach}}
          </select></td>
        </tr>
      </table>

    </td>
  </tr>
  <tr>
    <td colspan="2">

      <table class="form"><tr><td class="button"><button class="print" type="button" onclick="checkFormPrint()">Afficher</button></td></tr></table>

    </td>
  </tr>
</table>

</form>