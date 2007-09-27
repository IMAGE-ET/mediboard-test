<!-- $Id$ -->

<script type="text/javascript">
function checkFormPrint() {
  var form = document.paramFrm;
  
  if(!(checkForm(form))){
    return false;
  }
  
  popPlanning();
}

function popPlanning() {
  var form = document.paramFrm;

  var url = new Url;
  url.setModuleAction("dPhospi", "print_planning");
  url.addElement(form._date_min);
  url.addElement(form._date_max);
  url.addElement(form.ordre);
  url.addElement(form._service);
  url.addElement(form._filter_type);
  url.addElement(form.praticien_id);
  url.addElement(form._specialite);
  url.addElement(form.convalescence);
  url.popup(700, 500, "Planning");
  return;
}

function changeDate(sDebut, sFin){
  var oForm = document.paramFrm;
  oForm._date_min.value = sDebut;
  oForm._date_max.value = sFin;
  $('paramFrm__date_min_da').innerHTML = Date.fromDATETIME(sDebut).toLocaleDateTime();
  $('paramFrm__date_max_da').innerHTML = Date.fromDATETIME(sFin).toLocaleDateTime();  
}

function changeDateCal(){
  var oForm = document.paramFrm;
  oForm.select_days[0].checked = false;
  oForm.select_days[1].checked = false;
}

function pageMain() {
  regFieldCalendar("paramFrm", "_date_min", true);
  regFieldCalendar("paramFrm", "_date_max", true);
}

</script>

<form name="paramFrm" action="?m=dPhospi" method="post" onsubmit="return checkFormPrint()">

<table class="main">
  <tr>
    <td>

      <table class="form">
        <tr><th class="category" colspan="3">Choix de la période</th></tr>
        
        <tr>
          <td>{{mb_label object=$filter field="_date_min"}}</td>
          <td class="date">{{mb_field object=$filter field="_date_min" form="paramFrm" canNull="false" onchange="changeDateCal()"}} </td>
          <td rowspan="2">
            <input type="radio" name="select_days" onclick="changeDate('{{$today_deb}}','{{$today_fin}}');" value="today" checked="checked" /> 
              <label for="select_days_today">Aujourd'hui</label>
            <br /><input type="radio" name="select_days" onclick="changeDate('{{$tomorrow_deb}}','{{$tomorrow_fin}}');" value="tomorrow" /> 
              <label for="select_days_tomorrow">Lendemain</label>
          </td>
        </tr>
	    <td>{{mb_label object=$filter field="_date_max"}}</td>
        <td class="date">{{mb_field object=$filter field="_date_max" form="paramFrm" canNull="false" onchange="changeDateCal()"}} </td>
        <tr>
          <td>{{mb_label object=$filter field="_admission"}}</td>
          <td colspan="2">
            <select name="_admission">
              <option value="heure">Par heure d'admission</option>
              <option value="nom">Par nom du patient</option>
            </select>
          </td>
        </tr>

        <tr>
          <td>{{mb_label object=$filter field="_service"}}</td>
           
          <td colspan="2">
          	<select name="_service">
            	<option value="0">&mdash; Tous les services &mdash;</option>
            	{{foreach from=$listServ item=curr_serv}}
            	<option value="{{$curr_serv->service_id}}">{{$curr_serv->nom}}</option>
            	{{/foreach}}
         	</select>
         </td>
        </tr>
      </table>

    </td>
    <td>

      <table class="form">
        <tr><th class="category" colspan="2">Paramètres de filtrage</th></tr>
        <tr>
          <td>{{mb_label object=$filter field="_filter_type"}}</td>
          <td>{{mb_field object=$filter field="_filter_type" defaultOption="&mdash; Tous types d'admission &mdash;"}}</td>
        </tr>
        <tr>
          <td>{{mb_label object=$filter field="praticien_id"}}</td>
          <td><select name="praticien_id">
            <option value="0">&mdash; Tous les praticiens &mdash;</option>
            {{foreach from=$listPrat item=curr_prat}}
              <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}">{{$curr_prat->_view}}</option>
            {{/foreach}}
          </select></td>
        </tr>
        <tr>
          <td>{{mb_label object=$filter field="_specialite"}}</td>
          <td><select name="_specialite">
            <option value="0">&mdash; Toutes les spécialités &mdash;</option>
            {{foreach from=$listSpec item=curr_spec}}
              <option class="mediuser" style="border-color: #{{$curr_spec->color}};" value="{{$curr_spec->function_id}}">{{$curr_spec->text}}</option>
            {{/foreach}}
          </select></td>
        </tr>
        <tr>
          <td>{{mb_label object=$filter field="convalescence"}}</td>
          <td><select name="convalescence">
            <option value="0">&mdash; Indifférent &mdash;</option>
	        <option value="o">avec</option>
	        <option value="n">sans</option>
          </select></td>
        </tr>
      </table>

    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="form"><tr><td class="button"><button type="button" onclick="checkFormPrint()" class="print">Afficher</button></td></tr></table>
    </td>
  </tr>
</table>

</form>