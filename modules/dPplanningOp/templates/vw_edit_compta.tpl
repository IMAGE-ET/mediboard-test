<script type="text/javascript">

function viewActes(){
  var form = document.paramFrm;

  var url = new Url();
  url.setModuleAction("dPplanningOp", "vw_actes_realises");
  url.addElement(form._date_min);
  url.addElement(form._date_max);
  url.addElement(form.chir_id);
  url.addElement(form.typeVue);
  url.addElement(form.etatReglement);
  url.popup(950, 550, "Rapport des actes réalisés");
  
  return false;
}

function changeDate(sDebut, sFin){
  var oForm = document.paramFrm;
  oForm._date_min.value = sDebut;
  oForm._date_max.value = sFin;
  $('paramFrm__date_min_da').innerHTML = Date.fromDATE(sDebut).toLocaleDate();
  $('paramFrm__date_max_da').innerHTML = Date.fromDATE(sFin).toLocaleDate();  
}

function changeDateCal(){
  var oForm = document.paramFrm;
  oForm.select_days[0].checked = false;
  oForm.select_days[1].checked = false;
  oForm.select_days[2].checked = false;
}
</script>

<form name="paramFrm" method="get" action="?$m">
<input type="hidden" name="a" value="" />
<input type="hidden" name="dialog" value="1" />
<table class="main">
  <tr>
    <td>
      <table class="form">
        <tr>
          <th class="category" colspan="3">Choix de la période</th>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_date_min"}}</th>
          <td class="date">{{mb_field object=$filter field="_date_min" form="paramFrm" canNull="false" onchange="changeDateCal()" register=true}} </td>
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
           <td class="date">{{mb_field object=$filter field="_date_max" form="paramFrm" canNull="false" onchange="changeDateCal()" register=true}} </td>
        </tr>
        <tr>
          <th class="category" colspan="3">Critères d'affichage</th>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="chir_id"}}</th>
          <td colspan="2">
            <select name="chir_id">
            {{foreach from=$praticiens item="praticien"}}
              <option value="{{$praticien->_id}}">{{$praticien->_view}}</option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>Type d'affichage</th>
          <td colspan="2">
            <select name="typeVue">
            <option value="1">Liste complète</option>
            <option value="2">Totaux</option>
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="4" style="text-align: center">
            <button class="search" onclick="viewActes(); return false;">Voir les actes réalisés</button>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</form>