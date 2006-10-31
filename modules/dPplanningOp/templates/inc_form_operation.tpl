<!-- $Id: $ -->

<form name="editOp" action="?m={{$m}}" method="post" onsubmit="return checkFormOperation()">

<input type="hidden" name="dosql" value="do_planning_aed" />
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="operation_id" value="{{$op->operation_id}}" />
<input type="hidden" name="sejour_id" value="{{$op->sejour_id}}" />
<input type="hidden" name="commande_mat" value="{{$op->commande_mat}}" />
<input type="hidden" name="rank" value="{{$op->rank}}" />
<input type="hidden" name="annulee" value="{{$op->annulee}}" />
<input type="hidden" name="_group_id" value="{{$sejour->group_id}}" />
     
<table class="form">
  <tr>
    <th class="category" colspan="3">
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
      <input type="text" name="_codeCCAM" ondblclick="popCode('ccam')" size="10" value="" onblur="putCCAM(this.form._codeCCAM.value)" />
      <button class="tick notext" type="button" onclick="putCCAM(this.form._codeCCAM.value)"></button>
    </td>
    <td class="button">
      <button type="button" class="search" onclick="popCode('ccam')">Choisir un code</button>
    </td>
  </tr>

  <tr>
    <th>
      Liste des codes CCAM
      <input name="codes_ccam" type="hidden" value="{{$op->codes_ccam}}" onchange="refreshListCCAM()" />
    </th>
    <td colspan="2" class="text" id="listCodesCcam">
    </td>
  </tr>
  
  <tr>
    <th><label for="libelle" title="Libellé facultatif d'intervention">Libellé</label></th>
    <td colspan="2"><input type="text" name="libelle" title="{{$op->_props.libelle}}" size="50" value="{{$op->libelle}}"/></td>
  </tr>
  
  <tr>
    <th><label for="cote" title="Côté concerné par l'intervention">Côté</label></th>
    <td colspan="2">
      <select name="cote" title="{{$op->_props.cote}}" onchange="modifOp()">
        <option value="" {{if !$op->operation_id}} selected="selected" {{/if}}>&mdash; Choisir un côté</option>
        {{html_options options=$op->_enumsTrans.cote selected=$op->cote}}
      </select>
    </td>
  </tr> 

  <tr>
    <th>
      <label for="_hour_op" title="Durée de l'intervention. Obligatoire">Temps opératoire</label>
    </th>
    <td>
      <select name="_hour_op" title="notNull|num">
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
        <option value="{{$today}}" {{if $op->date == $today}}selected="selected"{{/if}}>
          {{$today|date_format:"%d/%m/%Y"}} (ajourd'hui)
        </option>
        <option value="{{$tomorow}}" {{if $op->date == $tomorow}}selected="selected"{{/if}}>
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
      <input type="hidden" name="plageop_id" title="{{$op->_props.plageop_id}}|notNull" ondblclick="popPlage()" value="{{$plage->plageop_id}}" />
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
    <td><textarea name="examen" title="{{$op->_props.examen}}" rows="3">{{$op->examen}}</textarea></td>
    <td><textarea name="materiel" title="{{$op->_props.materiel}}" rows="3">{{$op->materiel}}</textarea></td>
    <td><textarea name="rques" title="{{$op->_props.rques}}" rows="3">{{$op->rques}}</textarea></td>
  </tr>
  
  <tr>
    <th><label for="depassement"title="Valeur du dépassement d'honoraire éventuel">Dépassement d'honoraire</label></th>
    <td colspan="2"><input name="depassement" title="{{$op->_props.depassement}}" type="text" size="4" value="{{$op->depassement}}" /> €</td>
  </tr>
  
  <tr>
    <th><label for="info_0">Information du patient</label></th>
    <td colspan="2">
      <input name="info" value="1" type="radio" {{if $op->info == "1"}} checked="checked" {{/if}}/>
      <label for="info_1">Oui</label>
      <input name="info" value="0" type="radio" {{if !$op->operation_id || $op->info == "0"}} checked="checked" {{/if}}/>
      <label for="info_0">Non</label>
    </td>
  </tr>

  <tr>
    <th class="category" colspan="3">RDV d'anesthésie</th>
  </tr>

  <tr>
    <th>
      <label for="date_anesth" title="Choisir une date rendez-vous d'anesthésie">Date de RdV</label>
    </th>
    <td class="date">
      <div id="editOp_date_anesth_da">{{$op->date_anesth|date_format:"%d/%m/%Y"}}</div>
      <input type="hidden" name="date_anesth" title="{{$op->_props.date_anesth}}" value="{{$op->date_anesth}}" onchange="modifOp()" />
      <img id="editOp_date_anesth_trigger" src="./images/calendar.gif" alt="calendar"/>
    </td>
    <td colspan="2">
      à
      <select name="_hour_anesth">
      {{foreach from=$hours|smarty:nodefaults item=hour}}
        <option {{if $op->_hour_anesth == $hour}} selected="selected" {{/if}}>{{$hour}}</option>
      {{/foreach}}
      </select> h      <select name="_min_anesth">
      {{foreach from=$mins|smarty:nodefaults item=min}}
        <option {{if $op->_min_anesth == $min}} selected="selected" {{/if}}>{{$min}}</option>
      {{/foreach}}
      </select> mn
    </td>
  </tr>
</table>

</form>
