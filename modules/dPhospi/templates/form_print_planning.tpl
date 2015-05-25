<!-- $Id$ -->

<script type="text/javascript">
function checkFormPrint() {
  var form = getForm("paramFrm");
  
  if(!(checkForm(form))){
    return false;
  }
  
  popPlanning();
}

function popPlanning() {
  var form = getForm("paramFrm");

  var url = new Url;
  url.setModuleAction("dPhospi", "print_planning");
  url.addElement(form._date_min);
  url.addElement(form._date_max);
  url.addElement(form._horodatage);
  url.addElement(form.ordre);
  url.addParam("_service", [$V(form._service)].flatten().join(","));
  url.addElement(form._filter_type);
  url.addParam("praticien_id", [$V(form.praticien_id)].flatten().join(","));
  url.addElement(form._specialite);
  url.addElement(form.convalescence);
  {{if $conf.dPplanningOp.CSejour.consult_accomp}}
  url.addElement(form.consult_accomp);
  {{/if}}
  url.addParam("_ccam_libelle", $V(form._ccam_libelle));
  url.addParam("_coordonnees", $V(form._coordonnees));
  url.addParam('_notes', $V(form._notes));
  url.addParam('_by_date', $V(form._by_date));
  url.popup(850, 600, "Planning");
  return;
}

function changeDate(sDebut, sFin){
  var oForm = getForm("paramFrm");
  $V(oForm._date_min, sDebut);
  $V(oForm._date_max, sFin);
  $V(oForm._date_min_da, Date.fromDATETIME(sDebut).toLocaleDateTime());
  $V(oForm._date_max_da, Date.fromDATETIME(sFin).toLocaleDateTime());  
}

function changeDateCal(minChanged){
  var oForm = getForm("paramFrm");
  oForm.select_days[0].checked = false;
  oForm.select_days[1].checked = false;
  oForm.select_days[2].checked = false;

  var minElement = oForm._date_min,
      maxElement = oForm._date_max,
      minView = oForm._date_min_da,
      maxView = oForm._date_max_da;
  
  if ((minElement.value > maxElement.value) && minChanged) {
    var maxDate = Date.fromDATETIME(minElement.value).toDATE()+' 21:00:00';
    var maxDateView = Date.fromDATETIME(maxDate).toLocaleDateTime()
    $V(maxElement, maxDate);
    $V(maxView, maxDateView); 
  }
}

function toggleMultiple(select, multiple) {
  select.size = multiple ? 10 : 1;
  select.multiple = multiple;
}

</script>

<form name="paramFrm" action="?m=dPhospi" method="post" onsubmit="return checkFormPrint()">

<table class="main">
  <tr>
    <td>

      <table class="form">
        <tr>
          <th class="category" colspan="3">Choix de la période</th>
        </tr>
        
        <tr>
          <th>{{mb_label object=$filter field="_date_min"}}</th>
          <td>{{mb_field object=$filter field="_date_min" form="paramFrm" register=true canNull="false" onchange="changeDateCal(true)"}} </td>
          <td rowspan="2">
            <input type="radio" name="select_days" onclick="changeDate('{{$yesterday_deb}}','{{$yesterday_fin}}');" value="yesterday" /> 
            <label for="select_days_yesterday">Hier</label>
            <br />
            <input type="radio" name="select_days" onclick="changeDate('{{$today_deb}}','{{$today_fin}}');" value="today" checked="checked" /> 
            <label for="select_days_today">Aujourd'hui</label>
            <br />
            <input type="radio" name="select_days" onclick="changeDate('{{$tomorrow_deb}}','{{$tomorrow_fin}}');" value="tomorrow" /> 
            <label for="select_days_tomorrow">Demain</label>
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$filter field="_date_max"}}</th>
          <td>{{mb_field object=$filter field="_date_max" form="paramFrm" register=true canNull="false" onchange="changeDateCal(false)"}} </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$filter field="_admission"}}</th>
          <td colspan="2">
            <select name="_admission" style="width: 15em;">
              <option value="heure">Par heure d'admission</option>
              <option value="nom">Par nom du patient</option>
            </select>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$filter field=_horodatage}}</th>
          <td colspan="2">{{mb_field object=$filter field=_horodatage style="width: 15em;"}}</td>
        </tr>

        <tr>
          <th>{{mb_label object=$filter field="_service"}}</th>
          <td colspan="2">
            <select name="_service" style="width: 15em;">
              <option value="0">&mdash; {{tr}}All{{/tr}}</option>
              {{foreach from=$listServ item=curr_serv}}
                <option value="{{$curr_serv->service_id}}">{{$curr_serv->nom}}</option>
              {{/foreach}}
            </select>
            <label style="vertical-align: top;">
              <input type="checkbox" name="_multiple_services" onclick="toggleMultiple(this.form._service, this.checked)"/> Multiple
            </label>
          </td>
        </tr>
      </table>

    </td>
    <td>

      <table class="form">
        <tr>
          <th class="category" colspan="3">Paramètres de filtre</th>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_filter_type"}}</th>
          <td colspan="2">{{mb_field object=$filter field="_filter_type" emptyLabel="All" style="width: 15em;"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="praticien_id"}}</th>
          <td colspan="2">
            <select name="praticien_id" style="width: 15em;">
              <option value="0">&mdash; {{tr}}All{{/tr}}</option>
              {{mb_include module=mediusers template=inc_options_mediuser list=$listPrat}}
            </select>
            <label style="vertical-align: top;">
              <input type="checkbox" onclick="toggleMultiple(this.form.praticien_id, this.checked)"/>Multiple
            </label>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_specialite"}}</th>
          <td colspan="2">
            <select name="_specialite" style="width: 15em;">
              <option value="0">&mdash; {{tr}}All{{/tr}}</option>
              {{foreach from=$listSpec item=curr_spec}}
                <option class="mediuser" style="border-color: #{{$curr_spec->color}};" value="{{$curr_spec->function_id}}">{{$curr_spec->text}}</option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="convalescence"}}</th>
          <td colspan="2">
            <select name="convalescence" style="width: 15em;">
              <option value="0">&mdash; Indifférent</option>
              <option value="o">avec</option>
              <option value="n">sans</option>
            </select>
          </td>
        </tr>
        
        {{if $conf.dPplanningOp.CSejour.consult_accomp}}
        <tr>
          <th>{{mb_label object=$filter field="consult_accomp"}}</th>
          <td colspan="2">
            <select name="consult_accomp">
              <option value="0">&mdash; Indifférent</option>
              <option value="oui">oui</option>
              <option value="non">non</option>
            </select>
          </td>
        </tr>
        {{/if}}
        
        <tr>
          <th>{{mb_label object=$filter field="_ccam_libelle"}}</th>
          <td colspan="2">{{mb_field object=$filter field="_ccam_libelle"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_coordonnees"}}</th>
          <td colspan="2">{{mb_field object=$filter field="_coordonnees"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field=_notes}}</th>
          <td colspan="2">{{mb_field object=$filter field=_notes}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field=_by_date}}</th>
          <td colspan="2">{{mb_field object=$filter field=_by_date}}</td>
        </tr>
      </table>

    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="form">
        <tr>
          <td class="button">
            <button type="button" onclick="checkFormPrint()" class="print">Afficher</button>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

</form>