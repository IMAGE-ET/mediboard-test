<!-- $Id$ -->
{{mb_include_script module="dPplanningOp" script="ccam_selector"}}


<script type="text/javascript">
function checkFormPrint() {
  var form = document.paramFrm;
    
  if(!(checkForm(form))){
    return false;
  }
  
  popPlanning();
}


function popPlanning() {
  form = document.paramFrm;
  var url = new Url;
  url.setModuleAction("dPbloc", "view_planning");
  url.addElement(form._date_min);
  url.addElement(form._date_max);
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
  oForm.select_days[3].checked = false;
}
function pageMain() {
  regFieldCalendar("paramFrm", "_date_min");
  regFieldCalendar("paramFrm", "_date_max");
}

</script>


<form name="paramFrm" action="?m=dPbloc" method="post" onsubmit="return checkFormPrint()">
<input type="hidden" name="_class_name" value="COperation" />
<input type="hidden" name="_chir" value="{{$chir}}" />
<table class="main">
  <tr>
    <td>
      <table class="form">
        <tr><th class="category" colspan="3">Choix de la période</th></tr>
        <tr>
          <td>{{mb_label object=$filter field="_date_min"}}</td>
          <td class="date">{{mb_field object=$filter field="_date_min" form="paramFrm" canNull="false" onchange="changeDateCal()"}} </td>
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
           <td>{{mb_label object=$filter field="_date_max"}}</td>
           <td class="date">{{mb_field object=$filter field="_date_max" form="paramFrm" canNull="false" onchange="changeDateCal()"}} </td>
        </tr>
        <tr>
          <td>{{mb_label object=$filter field="_plage"}}</td>
          <td colspan="2">{{mb_field object=$filter field="_plage" checked="checked"}}</td>
        </tr>
        <tr>
          <td>{{mb_label object=$filter field="_codes_ccam"}}</td>
          <td><input type="text" name="code_ccam" size="10" value="" /></td>
          <td class="button"><button type="button" class="search" onclick="CCAMSelector.init()">sélectionner un code</button>
          <script type="text/javascript">
          CCAMSelector.init = function(){
            var oForm = document.paramFrm;
            this.eClass = oForm._class_name;
            this.eChir = oForm._chir;
            this.eView = oForm.code_ccam;
            this.pop();
          }
          </script>
          </td>
        </tr>
      </table>

    </td>
    <td>

      <table class="form">
        <tr><th class="category" colspan="2">Choix des paramètres de tri</th></tr>
        <tr>
          <td>{{mb_label object=$filter field="_intervention"}}</td>
          <td><select name="type">
            <option value="0">&mdash; Toutes les interventions &mdash;</option>
            <option value="1">insérées dans le planning</option>
            <option value="2">à insérer dans le planning</option>
          </select></td>
        </tr>
        <tr>
          <td>{{mb_label object=$filter field="_prat_id"}}</td>
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
		  <td>{{mb_label object=$filter field="_specialite"}}</td>
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
          <td>{{mb_label object=$filter field="salle_id"}}</td>
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