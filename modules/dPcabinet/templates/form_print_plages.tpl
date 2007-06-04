<!-- $Id$ -->

<script type="text/javascript">

function checkFormPrint() {
  var form = document.paramFrm;
  if(!(checkForm(form))){
    return false;
  }
  popPlages();
}

function popPlages() {
  var form = document.paramFrm;

  var url = new Url;
  url.setModuleAction("dPcabinet", "print_plages");
  url.addElement(form.deb);
  url.addElement(form.fin);
  url.addElement(form.chir);
  url.popup(700, 550, "Planning");
}

function changeDate(sDebut, sFin){
  var oForm = document.paramFrm;
  oForm.deb.value = sDebut;
  oForm.fin.value = sFin;
  $('paramFrm_deb_da').innerHTML = Date.fromDATE(sDebut).toLocaleDate();
  $('paramFrm_fin_da').innerHTML = Date.fromDATE(sFin).toLocaleDate();  
  
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

<form name="paramFrm" action="?m=dPcabinet" method="post" onsubmit="return checkFormPrint()">

<table class="main">
  <tr>
    <td>

      <table class="form">
        <tr><th class="category" colspan="3">Choix de la période</th></tr>
        <tr>
          <th><label for="deb" title="Date de début de la période">Début</label></th>
          <td class="date">
            <div id="paramFrm_deb_da">{{$now|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" onchange="changeDateCal()" class="notNull date" name="deb" value="{{$now}}" />
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
          <th><label for="fin" title="Date de fin de la période">Fin</label></th>
          <td class="date">
            <div id="paramFrm_fin_da">{{$now|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" onchange="changeDateCal()" class="notNull date moreEquals|deb" name="fin" value="{{$now}}" />
            <img id="paramFrm_fin_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de fin"/>
          </td>
        </tr>
      </table>

    </td>
    <td>

      <table class="form">
        <tr><th class="category" colspan="2">Choix des paramètres de tri</th></tr>
        <tr>
          <th><label for="chir" title="Praticien">Praticien</label></th>
          <td><select name="chir">
            <option value="0">&mdash; Tous</option>
            {{foreach from=$listChir item=curr_chir}}
	            <option class="mediuser" style="border-color: #{{$curr_chir->_ref_function->color}};" value="{{$curr_chir->user_id}}">{{$curr_chir->_view}}</option>
            {{/foreach}}
          </select></td>
        </tr>
      </table>

    </td>
  </tr>
  <tr>
    <td colspan="2">

      <table class="form">
        <tr>
          <td class="button">
            <button type="button" class="print" onclick="checkFormPrint()">Afficher</button>
          </td>
        </tr>
        </table>

    </td>
  </tr>
</table>
</form>