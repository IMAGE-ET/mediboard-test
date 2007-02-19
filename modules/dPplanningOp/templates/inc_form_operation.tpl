<!-- $Id: $ -->

<form name="editOp" action="?m={{$m}}" method="post" onsubmit="return checkFormOperation()">

<input type="hidden" name="dosql" value="do_planning_aed" />
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="del" value="0" />
{{mb_field object=$op field="operation_id" type="hidden" spec=""}}
{{mb_field object=$op field="sejour_id" type="hidden" spec=""}}
{{mb_field object=$op field="commande_mat" type="hidden" spec=""}}
{{mb_field object=$op field="rank" type="hidden" spec=""}}
<input type="hidden" name="annulee" value="{{$op->annulee|default:"0"}}" />
<input type="hidden" name="_group_id" value="{{$sejour->group_id}}" />
     
<table class="form">
  <tr>
    <th class="category" colspan="3">
      {{if $op->operation_id}}
        {{if $canReadSante400}}
        <a style="float:right;" href="#" onclick="view_idsante400('COperation',{{$op->operation_id}})">
          <img src="images/icons/sante400.gif" alt="Sante400" title="Identifiant sante 400"/>
        </a>
        {{/if}}
        <a style="float:right;" href="#" onclick="view_log('COperation',{{$op->operation_id}})">
          <img src="images/icons/history.gif" alt="historique" />
        </a>
      {{/if}}
      Informations concernant l'opération
    </th>
  </tr>
  
  {{if $op->annulee == 1}}
  <tr>
    <th class="category" colspan="3" style="background: #f00;">
    OPERATION ANNULEE
    </th>
  </tr>
  {{/if}}

  <tr>
    <th>
      <label for="chir_id" title="Chirurgien Responsable. Obligatoire">Chirurgien</label>
    </th>
    <td colspan="2">
      <select name="chir_id" title="{{$op->_props.chir_id}}" onchange="synchroPrat();removePlageOp(true);">
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
      <label for="_codeCCAM" title="Codes CCAM d'intervention">Ajout de codes CCAM</label>
    </th>
    <td>
      <input type="text" name="_codeCCAM" ondblclick="popCode('ccam')" size="10" value="" onblur="oCcamField.add(this.form._codeCCAM.value,true)" />
      <button class="tick notext" type="button" onclick="oCcamField.add(this.form._codeCCAM.value,true)"></button>
    </td>
    <td class="button">
      <button type="button" class="search" onclick="popCode('ccam')">Choisir un code</button>
    </td>
  </tr>

  <tr>
    <th>
      Liste des codes CCAM
      {{mb_field object=$op field="codes_ccam" onchange="refreshListCCAM()" type="hidden" spec=""}}
    </th>
    <td colspan="2" class="text" id="listCodesCcam">
    </td>
  </tr>
  
  <tr>
    <th><label for="libelle" title="Libellé facultatif d'intervention">Libellé</label></th>
    <td colspan="2">{{mb_field object=$op field="libelle" size="50"}}</td>
  </tr>
  
  <tr>
    <th><label for="cote" title="Côté concerné par l'intervention">Côté</label></th>
    <td colspan="2">
      {{mb_field object=$op field="cote" defaultOption="&mdash; Choisir un côté" onchange="modifOp()"}}
    </td>
  </tr> 

  <tr>
    <th>
      <label for="_hour_op" title="Durée de l'intervention. Obligatoire">Temps opératoire</label>
    </th>
    <td>
      <select name="_hour_op" title="notNull num">
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
      <label for="date" title="Choisir une date d'intervention">Date de l'intervention</label>
    </th>
    <td>
      <input type="hidden" name="plageop_id" value="" />
      <input type="hidden" name="_date" value="" />
      <input type="hidden" name="_datestr" value="" />
      <select name="date" onchange="{{if !$op->operation_id}}updateEntreePrevue();{{/if}} modifSejour()">
        {{if $op->operation_id}}
        <option value="{{$op->date}}" selected="selected">
          Inchangée ({{$op->date|date_format:"%d/%m/%Y"}} )
        </option>
        {{/if}}
        <option value="{{$today}}">
          {{$today|date_format:"%d/%m/%Y"}} (ajourd'hui)
        </option>
        <option value="{{$tomorow}}">
          {{$tomorow|date_format:"%d/%m/%Y"}} (demain)
        </option>
      </select>
    </td>
    <td>
      à
      <select name="_hour_urgence">
      {{foreach from=$hours_urgence|smarty:nodefaults item=hour}}
        <option value="{{$hour}}" {{if $op->_hour_urgence == $hour || (!$op->operation_id && $hour == "8")}} selected="selected" {{/if}}>{{$hour}}</option>
      {{/foreach}}
      </select> h
      <select name="_min_urgence">
      {{foreach from=$mins_duree|smarty:nodefaults item=min}}
        <option value="{{$min}}" {{if $op->_min_urgence == $min}}selected="selected"{{/if}}>{{$min}}</option>
      {{/foreach}}
      </select> mn
    </td>
    {{else}}
    <th>
      <label for="plageop_id" title="Date de l'intervention. Obligatoire">Date de l'intervention</label>
      <input type="hidden" name="date" value="" />
      <input type="hidden" name="_date" value="{{$plage->date}}" />
      <input type="hidden" name="plageop_id" title="notNull {{$op->_props.plageop_id}}" ondblclick="popPlage()" value="{{$plage->plageop_id}}" />
    </th>
    <td class="readonly">
      <input type="text" name="_datestr" readonly="readonly" size="10" ondblclick="popPlage()" value="{{$plage->date|date_format:"%d/%m/%Y"}}" />
    </td>
    <td class="button">
      <button type="button" class="search" onclick="popPlage()">Choisir une date</button>
    </td>
    {{/if}}
  </tr>

  <tr>
    <td class="text"><label for="examen" title="Bilan pré-opératoire">Bilan pré-op</label></td>
    <td class="text"><label for="materiel" title="Matériel à prévoir / examens per-opératoire">Matériel à prévoir / examens per-op</label></td>
    <td class="text"><label for="rques" title="Remarques sur l'intervention">Remarques sur l'intervention</label></td>
  </tr>

  <tr>
    <td>{{mb_field object=$op field="examen" rows="3"}}</td>
    <td>{{mb_field object=$op field="materiel" rows="3"}}</td>
    <td>{{mb_field object=$op field="rques" rows="3"}}</td>
  </tr>
  
  <tr>
    <th><label for="depassement"title="Valeur du dépassement d'honoraire éventuel">Dépassement d'honoraire</label></th>
    <td colspan="2">{{mb_field object=$op field="depassement" size="4"}} €</td>
  </tr>
  
  <tr>
    <th><label for="info_0">Information du patient</label></th>
    <td colspan="2">
      {{mb_field object=$op field="info"}}
    </td>
  </tr>

</table>

</form>
