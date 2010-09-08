{{if $selOp->_ref_sejour->_ref_consult_anesth->_id}}
{{assign var="consult_anesth" value=$selOp->_ref_sejour->_ref_consult_anesth}}

<form name="linkConsultAnesth" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_consult_anesth_aed" />
<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->_id}}" />
<input type="hidden" name="sejour_id" value="" />
<input type="hidden" name="operation_id" value="{{$selOp->_id}}" />
<table class="form">
  <tr>
    <td class="text">
      <div class="big-info">
        Une consultation d'anesthésie a été effectuée pour le séjour de ce patient
        le <strong>{{$consult_anesth->_date_consult|date_format:"%d/%m/%Y"}}</strong>
        par le <strong>Dr {{$consult_anesth->_ref_consultation->_ref_chir->_view}}</strong>.
        Vous devez <strong>relier cette consultation à l'intervention courante</strong> si vous désirez y accéder.
      </div>
    </td>
  </tr>
  <tr>
    <td class="button">
      <button type="submit" class="submit">Relier</button>
    </td>
  </tr>
</table>
</form>

{{else}}

<div class="big-info">
  Aucun dossier d'anesthésie n'a été associé à cette intervention ou ce séjour
  <br />
  Vour pouvez :
  <ul>
    <li>Soit <strong>associer un dossier d'anesthésie</strong> d'une consultation passée,</li>
    <li>Soit <strong>créer un nouveau dossier d'anesthésie</strong>.</li>
  </ul>
</div>


<table class="form">
  <tr>
    <th colspan="3" class="category">Associer un dossier existant</th>
  </tr>

  {{foreach from=$patient->_ref_consultations item=_consultation}}
  {{assign var=consult_anesth value=$_consultation->_ref_consult_anesth}}
  {{if $consult_anesth->_id}}
  <tr>
    <th>
      {{tr}}CConsultation{{/tr}} 
      du {{$_consultation->_date|date_format:$dPconfig.date}}
    </th>
    
    {{if $_consultation->annule}}
    <td colspan="2" class="cancelled">[Consultation annulée]</td>
    {{else}}    
    <td style="width: 1%;">
      Dr {{$_consultation->_ref_chir->_view}} 
    </td> 
    <td>
      {{if $consult_anesth->_ref_operation->_id}}
        Déjà associé :
        <strong>{{$consult_anesth->_ref_operation->_view}}</strong>
      {{elseif $consult_anesth->_ref_sejour->_id}}
        Déjà associé :
        <strong>{{$consult_anesth->_ref_sejour->_view}}</strong>
      {{else}}
      
      <form name="addOpFrm" action="?m={{$m}}" method="post" onsubmit="{{$onSubmit}}">

      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->_id}}" />
      <input type="hidden" name="operation_id" value="{{$selOp->_id}}" />

      <button class="tick">{{tr}}Associate{{/tr}}</button>
      
      </form>
      {{/if}}
    </td>
    {{/if}}
  </tr>
  {{/if}}
  {{foreachelse}}
  <tr>
    <td><em>Aucun dossier d'anesthésie existant pour ce patient</em></td>
  </tr>
  {{/foreach}}

  <tr>
    <th colspan="3" class="category">Créer un nouveau dossier</th>
  </tr>
  <tr>
    <td colspan="3" class="button">
      <form name="createConsult" action="?m={{$m}}" method="post" onsubmit="{{$onSubmit}}">
      
      <input type="hidden" name="dosql" value="do_consult_now" />
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="consultation_id" value="" />
      <input type="hidden" name="_operation_id" value="{{$selOp->_id}}" />
      <input type="hidden" name="_redirect" value="?" />
      <input type="hidden" name="patient_id" value="{{$selOp->_ref_sejour->patient_id}}" />
      <select name="prat_id">
        {{foreach from=$listAnesths item=curr_anesth}}
        <option value="{{$curr_anesth->user_id}}" {{if $selOp->_ref_anesth->user_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
          {{$curr_anesth->_view}}
        </option>
        {{/foreach}}
      </select>

      <button type="submit" class="new">{{tr}}Create{{/tr}}</button>

      </form>

    </td>
  </tr>
</table>
{{/if}}