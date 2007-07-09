<script language="Javascript" type="text/javascript">
function checkPlage() {
  var form = document.editFrm;
  
  if (!checkForm(form)) {
    return false;
   }
    
  if (form.chir_id.value == "" && form.spec_id.value == "") {
    alert("Merci de choisir un chirurgien ou une spécialité");
    form.chir_id.focus();
    return false;
  }
  
  if (form._heurefin.value < form._heuredeb.value || (form._heurefin.value == form._heuredeb.value && form._minutefin.value <= form._minutedeb.value)) {
    alert("L'heure de début doit être supérieure à la l'heure de fin");
    form._heurefin.focus();
    return false;
  }
  
  return true;
}

function pageMain() {
  regRedirectFlatCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
}

function popPlanning(debut) {
  var url = new Url;
  url.setModuleAction("dPbloc", "view_planning");
  url.addParam("_date_min", debut);
  url.addParam("_date_max", debut);
  url.popup(700, 550, "Planning");
}
</script>
<table class="main">
  <tr>
    <td class="greedyPane" style="text-align:center;">
      <a href="#" onclick="popPlanning('{{$date}}');">
        <strong>{{$date|date_format:"%A %d %B"}}</strong><br />
        <img src="images/icons/print.png" height="15" width="15" alt="imprimer" border="0" />
      </a>
      <table id="planningBloc">
        <tr>
          <th><strong>{{$date|date_format:"%a %d %b"}}</strong></th>
          {{foreach from=$listHours|smarty:nodefaults item=curr_hours}}
          <th colspan="4" class="heure">{{$curr_hours}}:00</th>
          {{/foreach}}         
        </tr>
        {{foreach from=$listSalles item=curr_salle key=keySalle}}
        <tr>
          <td class="salle">{{$curr_salle->nom}}</td>
          {{foreach from=$listHours|smarty:nodefaults item=curr_hour}}
          {{foreach from=$listMins|smarty:nodefaults item=curr_min key=keymin}}
            {{assign var="keyAff" value="$keySalle-$curr_hour:$curr_min"}}
            
            {{if is_string($arrayAffichage.$keyAff) &&  $arrayAffichage.$keyAff== "empty"}}
              <td class="empty{{if !$keymin}} firsthour{{/if}}"></td>
            {{elseif is_string($arrayAffichage.$keyAff) &&  $arrayAffichage.$keyAff== "full"}}
           
            {{else}}
              {{if $arrayAffichage.$keyAff->chir_id}}
                {{assign var=colorCell value=$arrayAffichage.$keyAff->_ref_chir->_ref_function->color}}
              {{else}}
                {{assign var=colorCell value=$arrayAffichage.$keyAff->_ref_spec->color}}
              {{/if}}
              
              {{assign var="pct" value=$arrayAffichage.$keyAff->_fill_rate}}
              {{if $pct gt 100}}
              {{assign var="pct" value=100}}
              {{/if}}
              {{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
              {{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
              {{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
              {{else}}{{assign var="backgroundClass" value="full"}}
              {{/if}}
              <td nowrap="nowrap" style="vertical-align: top; text-align: center;white-space: normal;background-color:#{{$colorCell}};" colspan="{{$arrayAffichage.$keyAff->_nbQuartHeure}}" title="{{$arrayAffichage.$keyAff->_fill_rate}} % du temps occupé">
                <div class="progressBar" style="height: 3px;">
                  <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;height: 3px;border-right: 2px solid #000;">
                  </div>
                </div>
                <strong>
                <a href="index.php?m=dPbloc&amp;tab=vw_edit_interventions&amp;plageop_id={{$arrayAffichage.$keyAff->plageop_id}}" title="Agencer les interventions">
                  {{$arrayAffichage.$keyAff->_view}}
                </a> ({{$arrayAffichage.$keyAff->_nb_operations}})
                <a href="index.php?m=dPbloc&amp;tab=vw_edit_planning&amp;plageop_id={{$arrayAffichage.$keyAff->plageop_id}}&amp;date={{$date}}">
                  <img src="images/icons/edit.png" alt="editer la plage" title="Editer la plage" border="0" height="16" width="16" />
                </a>
                </strong>
              </td>
            {{/if}}
           {{/foreach}}
          {{/foreach}}
        </tr>
        {{/foreach}} 
      </table>
      {{if $plagesel->plageop_id}}
      <a class="buttonnew" href="index.php?m=dPbloc&amp;tab=vw_edit_planning&amp;plageop_id=0">
        Créer une nouvelle plage opératoire
      </a>
      {{/if}}
      {{if $can->edit}}
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkPlage()">
      <input type="hidden" name="dosql" value="do_plagesop_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="plageop_id" value="{{$plagesel->plageop_id}}" />

      <table class="form">
        <tr>
          {{if $plagesel->plageop_id}}
          <th class="category modify" colspan="6">
            <a style="float:right;" href="#" onclick="view_log('CPlageOp',{{$plagesel->plageop_id}})">
              <img src="images/icons/history.gif" alt="historique" />
            </a>
            Modifier la plage opératoire
          {{else}}
          <th class="category" colspan="6">
            Ajouter une plage opératoire
          {{/if}}
          </th>
        </tr>
        <tr>
         <th>{{mb_label object=$plagesel field="chir_id"}}</th>
         <td>
          <select name="chir_id" class="{{$plagesel->_props.chir_id}}">
            <option value="">&mdash; Choisir un chirurgien</option>
            {{foreach from=$specs item=currFct key=keyFct}}
            <optgroup label="{{$currFct->_view}}">
              {{foreach from=$currFct->_ref_users item=currUser}}
              <option class="mediuser" style="border-color: #{{$currFct->color}};" value="{{$currUser->user_id}}" 
              {{if $plagesel->chir_id == $currUser->user_id}}selected="selected"{{/if}}>
                {{$currUser->_view}}
              </option>
              {{/foreach}}
            </optgroup>
            {{/foreach}}
          </select>
        </td>
        <th>{{mb_label object=$plagesel field="salle_id"}}</th>
        <td>
          <select name="salle_id" class="{{$plagesel->_props.salle_id}}">
            <option value="">&mdash; Choisir une salle</option>
            {{foreach from=$listSalles item=salle}}
            <option value="{{$salle->salle_id}}" {{if $plagesel->salle_id == $salle->salle_id}} selected="selected"{{/if}} >
              {{$salle->nom}}
            </option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$plagesel field="spec_id"}}</th>
        <td>
          <select name="spec_id" class="{{$plagesel->_props.spec_id}}">
            <option value="">&mdash; Choisir une spécialité</option>
            {{foreach from=$specs item=spec}}
              <option value="{{$spec->function_id}}" class="mediuser" style="border-color: #{{$spec->color}};"
              {{if $spec->function_id == $plagesel->spec_id}}selected="selected"{{/if}}>
                {{$spec->text}}
              </option>
            {{/foreach}}
          </select>
        </td>
        <th>{{mb_label object=$plagesel field="date"}}</th>
        <td class="date">
          {{if $plagesel->plageop_id}}
          <div id="editFrm_date_da">{{$plagesel->date|date_format:"%d/%m/%Y"}}</div>
          <input type="hidden" name="date" value="{{$plagesel->date}}" />
          {{else}}
          <div id="editFrm_date_da">{{$date|date_format:"%d/%m/%Y"}}</div>
          <input type="hidden" name="date" value="{{$date}}" />
          {{/if}}
          <!-- Possibilité de changer la date ? -->
          <!--img id="editFrm_date_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date"/-->
        </td>
      </tr>
      
      <tr>
        <th>{{mb_label object=$plagesel field="anesth_id"}}</th>
        <td>
          <select name="anesth_id">
            <option value="">&mdash; Choisir un anesthésiste</option>
            {{foreach from=$anesths item=anesth}}
            <option value="{{$anesth->user_id}}" {{if $plagesel->anesth_id == $anesth->user_id}} selected="selected" {{/if}} >
              {{$anesth->_view}}
            </option>
            {{/foreach}}
    	  </select>
        </td>
        <th>{{mb_label object=$plagesel field="_heuredeb"}}</th>
        <td>
          <select name="_heuredeb" class="notNull num">
          {{foreach from=$listHours|smarty:nodefaults item=heure}}
            <option value="{{$heure|string_format:"%02d"}}" {{if $plagesel->_heuredeb == $heure}} selected="selected" {{/if}} >
              {{$heure|string_format:"%02d"}}
            </option>
          {{/foreach}}
          </select>
          :
          <select name="_minutedeb">
          {{foreach from=$listMins|smarty:nodefaults item=minute}}
            <option value="{{$minute|string_format:"%02d"}}" {{if $plagesel->_minutedeb == $minute}} selected="selected" {{/if}} >
              {{$minute|string_format:"%02d"}}
            </option>
          {{/foreach}}
          </select>
        </td>
      </tr>
      
      <tr>
        <th>
          <label for="_repeat" title="Nombre de plages à créer">Nombre de plages</label>
        </th>
        <td>
          <input type="text" class="notNull num min|1" name="_repeat" size="1" value="1" />
        </td>
    	  <th>{{mb_label object=$plagesel field="_heurefin"}}</th>
        <td>
          <select name="_heurefin" class="notNull num">
            {{foreach from=$listHours|smarty:nodefaults item=heure}}
            <option value="{{$heure|string_format:"%02d"}}" {{if $plagesel->_heurefin == $heure}} selected="selected" {{/if}} >
              {{$heure|string_format:"%02d"}}
            </option>
            {{/foreach}}
          </select>
          :
          <select name="_minutefin">
            {{foreach from=$listMins|smarty:nodefaults item=minute}}
            <option value="{{$minute|string_format:"%02d"}}" {{if $plagesel->_minutefin == $minute}} selected="selected" {{/if}} >
              {{$minute|string_format:"%02d"}}
            </option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <th>
          <label for="_type_repeat" title="Espacement des plages">Type de répétition</label>
        </th>
        <td>
          <select name="_type_repeat">
            <option value="1">Normale</option>
            <option value="2">Une semaine sur deux</option>
            <option value="3">Une semaine sur trois</option>
          </select>
        </td>
        <th>{{mb_label object=$plagesel field="_min_inter_op"}}</th>
        <td>
          <select name="_min_inter_op" class="notNull num pos">
          {{foreach from=$_temps_inter_op item=curr_inter_op}}
            <option value="{{$curr_inter_op}}" {{if $plagesel->_min_inter_op == $curr_inter_op}} selected ="selected" {{/if}}>{{$curr_inter_op}}</option>
          {{/foreach}}
          </select>
          min
        </td>
      </tr>
      <tr>
        <td class="button" colspan="4">
        {{if $plagesel->plageop_id}}
          <button type="submit" class="modify">Modifier</button>
        {{else}}
          <button type="submit" class="new">Ajouter</button>
        {{/if}}
        </td>
      </tr>
    </table>
    </form>

    {{if $plagesel->plageop_id}}
      <form name="removeFrm" action="?m={{$m}}" method="post">
      <input type="hidden" name="dosql" value="do_plagesop_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="plageop_id" value="{{$plagesel->plageop_id}}" /> 
      <table class="form">
        <tr>
          <th class="category" colspan="2">Supprimer la plage opératoire</th>
        </tr>  
        <tr>
          <th>Supprimer cette plage pendant</th> 
          <td><input type="text" name="_repeat" size="1" value="1" /> semaine(s)</td>
        </tr>   
        <tr>
          <td class="button" colspan="2">
            <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'la plage opératoire',objName:'{{$plagesel->_view|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
          </td>
        </tr>
      </table>
      </form>
    {{/if}}
    {{/if}}
   </td>
   <td>
     {{include file="inc_legende_planning.tpl"}}
   </td>
  </tr>
</table>