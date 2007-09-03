<!-- $Id: $ -->
{{mb_include_script module="dPplanningOp" script="ccam_selector"}}
{{mb_include_script module="dPplanningOp" script="plage_selector"}}

<form name="editOp" action="?m={{$m}}" method="post" onsubmit="return checkFormOperation()">

<input type="hidden" name="dosql" value="do_planning_aed" />
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="del" value="0" />
{{mb_field object=$op field="operation_id" hidden=1 prop=""}}
{{mb_field object=$op field="sejour_id" hidden=1 prop=""}}
{{mb_field object=$op field="commande_mat" hidden=1 prop=""}}
{{mb_field object=$op field="rank" hidden=1 prop=""}}
<input type="hidden" name="annulee" value="{{$op->annulee|default:"0"}}" />
<input type="hidden" name="_group_id" value="{{$sejour->group_id}}" />
<input type="hidden" name="_class_name" value="COperation" />

     
<table class="form">
  <tr>
    <th class="category" colspan="3">
      {{if $op->operation_id}}
      
      <div class="idsante400" id="COperation-{{$op->operation_id}}"></div>
      
      <a style="float:right;" href="#" onclick="view_log('COperation',{{$op->operation_id}})">
        <img src="images/icons/history.gif" alt="{{tr}}History.desc{{/tr}}" />
      </a>
      <div style="float:left;" class="noteDiv {{$op->_class_name}}-{{$op->_id}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
      </div>
      {{/if}}
      {{tr}}msg-COperation-informations{{/tr}}
    </th>
  </tr>
  
  {{if $op->annulee == 1}}
  <tr>
    <th class="category cancelled" colspan="3">
    {{tr}}COperation-annulee{{/tr}}
    </th>
  </tr>
  {{/if}}

  <tr>
    <th>
      {{mb_label object=$op field="chir_id"}}
    </th>
    <td colspan="2">
      <select name="chir_id" class="{{$op->_props.chir_id}}" onchange="synchroPrat(); Value.synchronize(this); removePlageOp(true);">
        <option value="">&mdash; Choisir un chirurgien</option>
        {{foreach from=$listPraticiens item=curr_praticien}}
        <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}" {{if $chir->user_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
        {{$curr_praticien->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th>
      {{mb_label object=$op field="codes_ccam" defaultFor="_codeCCAM"}}
    </th>
    <td>
      <input type="text" name="_codeCCAM" ondblclick="CCAMSelector.init()" size="10" value="" onblur="oCcamField.add(this.form._codeCCAM.value,true)" />
      <button class="tick notext" type="button" onclick="oCcamField.add(this.form._codeCCAM.value,true)">{{tr}}Add{{/tr}}</button>
    </td>
    <td class="button">
      <button type="button" class="search" onclick="CCAMSelector.init()">{{tr}}button-CCodeCCAM-choix{{/tr}}</button>
      <script type="text/javascript">
        CCAMSelector.init = function(){
        this.sForm  = "editOp";
        this.sView  = "_codeCCAM";
        this.sChir  = "chir_id";
        this.sClass = "_class_name";
        this.pop();
      }
      </script>
    </td>
  </tr>
  <tr>
    <th>
      Liste des codes CCAM
      {{mb_field object=$op field="codes_ccam" onchange="refreshListCCAM('expert');" hidden=1 prop=""}}
    </th>
    <td colspan="2" class="text" id="listCodesCcam">
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$op field="libelle"}}</th>
    <td colspan="2">{{mb_field object=$op field="libelle"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$op field="cote"}}</th>
    <td colspan="2">
      {{mb_field object=$op field="cote" defaultOption="&mdash; Choisir un côté" onchange="Value.synchronize(this);"}}
    </td>
  </tr> 

  <tr>
    <th>
      {{mb_label object=$op field="_hour_op"}}
    </th>
    <td>
      <select name="_hour_op" class="notNull num">
      {{foreach from=$hours_duree|smarty:nodefaults item=hour}}
        <option value="{{$hour}}" {{if (!$op && $hour == 1) || $op->_hour_op == $hour}} selected="selected" {{/if}}>{{$hour}}</option>
      {{/foreach}}
      </select> h
      <select name="_min_op">
      {{foreach from=$mins_duree|smarty:nodefaults item=min}}
        <option value="{{$min}}" {{if (!$op && $min == 0) || $op->_min_op == $min}} selected="selected" {{/if}}>{{$min}}</option>
      {{/foreach}}
      </select> mn
    </td>
    <td id="timeEst">
    </td>
  </tr>

  <tr>
    {{if $modurgence}}
    <th>
      {{mb_label object=$op field="date"}}
    </th>
    <td>
      <input type="hidden" name="plageop_id" value="" />
      <input type="hidden" name="_date" value="" />
      <input type="hidden" name="_datestr" value="" />
      <select name="date" onchange="{{if !$op->operation_id}}updateEntreePrevue();{{/if}} Value.synchronize(this); modifSejour()">
        {{if $op->operation_id}}
        <option value="{{$op->date}}" selected="selected">
          Inchangée ({{$op->date|date_format:"%d/%m/%Y"}} )
        </option>
        {{/if}}
        <option value="{{$today}}">
          {{$today|date_format:"%d/%m/%Y"}} (aujourd'hui)
        </option>
        <option value="{{$tomorow}}">
          {{$tomorow|date_format:"%d/%m/%Y"}} (demain)
        </option>
      </select>
    </td>
    <td>
      à
      <select name="_hour_urgence" onchange="Value.synchronize(this)">
      {{foreach from=$hours_urgence|smarty:nodefaults item=hour}}
        <option value="{{$hour}}" {{if $op->_hour_urgence == $hour || (!$op->operation_id && $hour == "8")}} selected="selected" {{/if}}>{{$hour}}</option>
      {{/foreach}}
      </select> h
      <select name="_min_urgence" onchange="Value.synchronize(this);">
      {{foreach from=$mins_duree|smarty:nodefaults item=min}}
        <option value="{{$min}}" {{if $op->_min_urgence == $min}}selected="selected"{{/if}}>{{$min}}</option>
      {{/foreach}}
      </select> mn
    </td>
    {{else}}
    <th>
      <input type="hidden" name="plageop_id" class="notNull {{$op->_props.plageop_id}}" ondblclick="PlageOpSelector.init()" value="{{$plage->plageop_id}}" />
      {{mb_label object=$op field="plageop_id"}}
      <input type="hidden" name="date" value="" />
      <input type="hidden" name="_date" value="{{$plage->date}}" />
    </th>
    <td class="readonly">
      <input type="text" name="_datestr" readonly="readonly" size="10" ondblclick="PlageOpSelector.init()" value="{{$plage->date|date_format:"%d/%m/%Y"}}" />
    </td>
    <td class="button">
      <button type="button" class="search" onclick="PlageOpSelector.init()">Choisir une date</button>
 
      <script type="text/javascript">
      
      PlageOpSelector.init = function(){
        if(!(checkChir() && checkDuree())) {
          return;
        }

        var oOpForm     = document.editOp;
        var oSejourForm = document.editSejour;
        
        this.sPlage_id      = "plageop_id";
        this.sPlage_id_easy = "plageop_id";
        this.sDate         = "_datestr";
        this.sDate_easy    = "_datestr";
        
        this.s_hour_entree_prevue = "_hour_entree_prevue";
        this.s_min_entree_prevue  = "_min_entree_prevue";
        this.s_date_entree_prevue = "_date_entree_prevue";
        
        this.heure_entree_veille = "{{$heure_entree_veille}}";
        this.heure_entree_jour   = "{{$heure_entree_jour}}";   
        this.pop(oOpForm.chir_id.value, oOpForm._hour_op.value,
                 oOpForm._min_op.value, oSejourForm.group_id.value,
                 oOpForm.operation_id.value);
      } 
      
      </script>
      
    </td>
    {{/if}}
  </tr>

  <tr>
    <td class="text">{{mb_label object=$op field="examen"}}</td>
    <td class="text">{{mb_label object=$op field="materiel"}}</td>
    <td class="text">{{mb_label object=$op field="rques"}}</td>
  </tr>

  <tr>
    <td>{{mb_field object=$op field="examen" rows="3"}}</td>
    <td>{{mb_field object=$op field="materiel" rows="3"}}</td>
    <td>{{mb_field object=$op field="rques" rows="3"}}</td>
  </tr>
  
  <tr>
    <td class="text">{{mb_label object=$op field="depassement"}}</td>
    <td class="text">{{mb_label object=$op field="forfait"}}</td>
    <td class="text">{{mb_label object=$op field="fournitures"}}</td>
  </tr>

  <tr>
    <td>{{mb_field object=$op field="depassement" size="4"}}</td>
    <td>{{mb_field object=$op field="forfait" size="4"}}</td>
    <td>{{mb_field object=$op field="fournitures" size="4"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$op field="info"}}</th>
    <td colspan="2">
      {{mb_field object=$op field="info"}}
    </td>
  </tr>

</table>

</form>
