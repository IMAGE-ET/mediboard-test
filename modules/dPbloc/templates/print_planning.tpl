{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

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
  url.addParam("_plage", $V(form._plage));
  url.addElement(form._codes_ccam);
  url.addElement(form._intervention);
  url.addElement(form._prat_id);
  url.addElement(form._specialite);
  url.addElement(form._bloc_id);
  url.addElement(form.salle_id);
  url.addElement(form.type);
  url.addParam("_ccam_libelle", $V(form._ccam_libelle));
  url.addParam("_coordonnees", $V(form._coordonnees));
  url.popup(900, 550, 'Planning');
}

function changeDate(sDebut, sFin){
  var oForm = document.paramFrm;
  oForm._date_min.value = sDebut;
  oForm._date_max.value = sFin;
  oForm._date_min_da.value = Date.fromDATE(sDebut).toLocaleDate();
  oForm._date_max_da.value = Date.fromDATE(sFin).toLocaleDate();  
}

function changeDateCal(){
  var oForm = document.paramFrm;
  oForm.select_days[0].checked = false;
  oForm.select_days[1].checked = false;
  oForm.select_days[2].checked = false;
  oForm.select_days[3].checked = false;
}

</script>


<form name="paramFrm" action="?m=dPbloc" method="post" onsubmit="return checkFormPrint()">
<input type="hidden" name="_class_name" value="COperation" />
<input type="hidden" name="_chir" value="{{$chir}}" />
<table class="main">
  <tr>
    <td>
      <table class="form">
        <tr>
          <th class="category" colspan="3">Choix de la période</th>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_date_min"}}</th>
          <td>{{mb_field object=$filter field="_date_min" form="paramFrm" canNull="false" onchange="changeDateCal()" register=true}} </td>
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
           <th>{{mb_label object=$filter field="_date_max"}}</th>
           <td>{{mb_field object=$filter field="_date_max" form="paramFrm" canNull="false" onchange="changeDateCal()" register=true}} </td>
        </tr>
        <tr>
          <th class="category" colspan="3">Types d'intervention</th>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_intervention"}}</th>
          <td colspan="2">
            <select name="_intervention">
              <option value="0">&mdash; Toutes les interventions &mdash;</option>
              <option value="1">insérées dans le planning</option>
              <option value="2">à insérer dans le planning</option>
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$filterSejour field="type"}}</th>
          <td colspan="2">
            {{mb_field object=$filterSejour field="type" canNull=true defaultOption="&mdash; Tous les types"}}
          </td>
        </tr>
      </table>

    </td>
    <td>

      <table class="form">
        <tr>
          <th class="category" colspan="2">Autres filtres</th>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_prat_id"}}</th>
          <td>
            <select name="_prat_id" onchange="this.form._specialite.value = '0';">
              <option value="0">&mdash; Tous les praticiens &mdash;</option>
              {{foreach from=$listPrat item=curr_prat}}
                <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" >
                  {{$curr_prat->_view}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_specialite"}}</th>
          <td>
            <select name="_specialite" onchange="this.form._prat_id.value = '0';">
              <option value="0">&mdash; Toutes les spécialités &mdash;</option>
              {{foreach from=$listSpec item=curr_spec}}
                <option value="{{$curr_spec->function_id}}" class="mediuser" style="border-color: #{{$curr_spec->color}};">
                  {{$curr_spec->text}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_bloc_id"}}</th>
          <td>
            <select name="_bloc_id">
              <option value="0">&mdash; Tous les blocs &mdash;</option>
              {{foreach from=$listBlocs item=curr_bloc}}
                <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $filter->_bloc_id}}selected="selected"{{/if}}>
                  {{$curr_bloc->_view}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="salle_id"}}</th>
          <td>
            <select name="salle_id">
              <option value="0">&mdash; Toutes les salles &mdash;</option>
              {{foreach from=$listBlocs item=curr_bloc}}
                <optgroup label="{{$curr_bloc->_view}}">
                {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
                  <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $filter->salle_id}}selected="selected"{{/if}}>
                    {{$curr_salle->nom}}
                  </option>
                {{/foreach}}
                </optgroup>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_codes_ccam"}}</th>
          <td><input type="text" name="_codes_ccam" size="10" value="" />
          <button type="button" class="search" onclick="CCAMSelector.init()">sélectionner un code</button>
          <script type="text/javascript">
          CCAMSelector.init = function(){
            this.sForm  = "paramFrm";
            this.sClass = "_class_name";
            this.sChir  = "_chir";
            this.sView  = "_codes_ccam";
            this.pop();
          }
          </script>
          </td>
        </tr>

      </table>

    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="form">
        <tr>
          <th class="category" colspan="2">Paramètres d'affichage</th>
        </tr>
        {{assign var="class" value="CPlageOp"}}
            
        <tr>
          <th>
            <label for="_coordonnees_1" title="Afficher ou cacher le numéro de tel du patient">Afficher les coordonnées du patient</label>
          </th>
          <td>  
            <label for="_coordonnees_1">Oui</label>
            <input type="radio" name="_coordonnees" value="1" /> 
            <label for="_coordonnees_0">Non</label>
            <input type="radio" name="_coordonnees" value="0" checked="checked"/> 
          </td>
        </tr>
        <tr>
          <th style="width: 50%">{{mb_label object=$filter field="_plage"}}</th>
          <td>  
            {{assign var="var" value="plage_vide"}}
            <label for="_plage">Oui</label>
            <input type="radio" name="_plage" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
            <label for="_plage">Non</label>
            <input type="radio" name="_plage" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_ccam_libelle"}}</th>
          <td>  
            {{assign var="var" value="libelle_ccam"}}
            <label for="_ccam_libelle">Oui</label>
            <input type="radio" name="_ccam_libelle" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
            <label for="_ccam_libelle">Non</label>
            <input type="radio" name="_ccam_libelle" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
          </td>
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