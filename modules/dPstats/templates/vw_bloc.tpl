{{mb_include_script module="dPplanningOp" script="ccam_selector"}}





<script type="text/javascript">

function zoomGraphIntervention(date){
  url = new Url();
  url.setModuleAction("dPstats", "graph_activite_zoom");
  url.addParam("suppressHeaders", 1);
  url.addParam("date"         , date);
  url.addParam("salle_id"     , "{{$filter->salle_id}}");
  url.addParam("prat_id"      , "{{$filter->_prat_id}}");
  url.addParam("codeCCAM"     , "{{$filter->_codes_ccam|smarty:nodefaults|escape:"javascript"}}");
  url.addParam("discipline_id", "{{$filter->_specialite}}");
  url.addParam("size"         , 2);
  url.popup(760, 400, "ZoomMonth");
}

function pageMain() {
  regFieldCalendar("bloc", "_date_min");
  regFieldCalendar("bloc", "_date_max");
}

</script>

<table class="main">
  <tr>
    <td>
      <form name="bloc" action="index.php" method="get" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPstats" />
      <input type="hidden" name="_chir" value="{{$user_id}}" />
      <input type="hidden" name="_class_name" value="" />
      <table class="form">
        <tr>
          <th colspan="4" class="category">Activité du bloc opératoire</th>
        </tr>
        <tr>
           <td>{{mb_label object=$filter field="_date_min"}}</td>
          <td class="date">{{mb_field object=$filter field="_date_min" form="bloc" canNull="false"}} </td>
          <td>{{mb_label object=$filter field="salle_id"}}</td>
          <td>
            <select name="salle_id">
              <option value="0">&mdash; Toutes les salles</option>
              {{foreach from=$listSalles item=curr_salle}}
              <option value="{{$curr_salle->salle_id}}" {{if $curr_salle->salle_id == $filter->salle_id }}selected="selected"{{/if}}>
                {{$curr_salle->nom}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td>{{mb_label object=$filter field="_date_max"}}</td>
          <td class="date">{{mb_field object=$filter field="_date_max" form="bloc" canNull="false"}} </td>
          <td>{{mb_label object=$filter field="_prat_id"}}</td>
          <td>
            <select name="prat_id">
              <option value="0">&mdash; Tous les praticiens</option>
              {{foreach from=$listPrats item=curr_prat}}
              <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $filter->_prat_id}}selected="selected"{{/if}}>
                {{$curr_prat->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td>{{mb_label object=$filter field="codes_ccam"}}</td>
          <td>
            {{mb_field object=$filter field="codes_ccam" canNull="true" size="8"}}
            <button class="search" type="button" onclick="CCAMSelector.init()">Rechercher</button>
          
            <script type="text/javascript">
              CCAMSelector.init = function(){
                var oForm = document.bloc;
                this.eView = oForm.codes_ccam;
                this.eChir = oForm._chir;
                this.eClass = oForm._class_name;
                this.pop();
              }
            </script>
            
          </td>
          <td>{{mb_label object=$filter field="_specialite"}}</td>
          <td>
            <select name="discipline_id">
              <option value="0">&mdash; Toutes les spécialités</option>
              {{foreach from=$listDisciplines item=curr_disc}}
              <option value="{{$curr_disc->discipline_id}}" {{if $curr_disc->discipline_id == $filter->_specialite }}selected="selected"{{/if}}>
                {{$curr_disc->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="4" class="button"><button class="search" type="submit">Afficher</button></td>
        </tr>
        <tr>
          <td colspan="4" class="button">
            {{$map_graph_interventions|smarty:nodefaults}}
            <img usemap="#graph_interventions" alt="Nombre d'interventions" src='?m=dPstats&amp;a=graph_activite&amp;suppressHeaders=1&amp;debut={{$filter->_date_min}}&amp;fin={{$filter->_date_max}}&amp;salle_id={{$filter->salle_id}}&amp;prat_id={{$filter->_prat_id}}&amp;codeCCAM={{$filter->codes_ccam}}&amp;discipline_id={{$filter->_specialite}}' />
            {{if $filter->_prat_id}}
              <img alt="Occupation des plages" src='?m=dPstats&amp;a=graph_praticienbloc&amp;suppressHeaders=1&amp;debut={{$filter->_date_min}}&amp;fin={{$filter->_date_max}}&amp;salle_id={{$filter->salle_id}}&amp;prat_id={{$filter->_prat_id}}&amp;codeCCAM={{$filter->codes_ccam}}' />
            {{elseif $filter->_specialite}}
              <img alt="Répartition par praticiens" src='?m=dPstats&amp;a=graph_pratdiscipline&amp;suppressHeaders=1&amp;debut={{$filter->_date_min}}&amp;fin={{$filter->_date_max}}&amp;salle_id={{$filter->salle_id}}&amp;discipline_id={{$filter->_specialite}}&amp;codeCCAM={{$filter->codes_ccam}}' />
            {{else}}
              <img alt="Patients par jour par salle" src='?m=dPstats&amp;a=graph_patjoursalle&amp;suppressHeaders=1&amp;debut={{$filter->_date_min}}&amp;fin={{$filter->_date_max}}&amp;salle_id={{$filter->salle_id}}&amp;prat_id={{$filter->_prat_id}}&amp;codeCCAM={{$filter->codes_ccam}}' />
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>