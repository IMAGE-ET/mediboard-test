<!-- Plages -->
{{foreach from=$salle->_ref_plages item=_plage}}
<hr />

<form name="anesth{{$_plage->_id}}" action="?" method="post">

<input type="hidden" name="m" value="dPbloc" />
<input type="hidden" name="otherm" value="{{$m}}" />
<input type="hidden" name="dosql" value="do_plagesop_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="_repeat" value="1" />
<input type="hidden" name="plageop_id" value="{{$_plage->_id}}" />
<input type="hidden" name="chir_id" value="{{$_plage->chir_id}}" />
<input type="hidden" name="spec_id" value="{{$_plage->spec_id}}" />

<table class="form">
  <tr>
    <th class="category{{if $vueReduite}} text{{/if}}" colspan="2">
      <a href="?m=dPbloc&amp;tab=vw_edit_interventions&amp;plageop_id={{$_plage->_id}}" title="Administrer la plage">
        Chir : Dr {{$_plage->_ref_chir->_view}}
        {{if $vueReduite}}
          <br />
        {{else}}
          -
        {{/if}}
        {{$_plage->debut|date_format:$dPconfig.time}} à {{$_plage->fin|date_format:$dPconfig.time}}
      </a>
    </th>
  </tr>
  
  {{if $vueReduite}}
  <tr>
    <th class="category" colspan="2">
      {{if $_plage->anesth_id}}
        Anesth : Dr {{$_plage->_ref_anesth->_view}}
      {{else}}
        -
      {{/if}}
    </th>
  </tr>
  {{else}}
  <tr>
    <th><label for="anesth_id" title="Anesthésiste associé à la plage d'opération">Anesthésiste</label></th>
    <td>
      <select name="anesth_id" onchange="this.form.submit()">
        <option value="">&mdash; Choisir un anesthésiste</option>
        {{foreach from=$listAnesths item=curr_anesth}}
        <option value="{{$curr_anesth->user_id}}" {{if $_plage->anesth_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
          {{$curr_anesth->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  {{if $dPconfig.dPsalleOp.COperation.modif_actes == "button" && !$_plage->actes_locked}}
  <tr>
    <td class="button" colspan="2">
      <input type="hidden" name="actes_locked" value="{{$_plage->actes_locked}}" />
      <button class="submit" type="button" onclick="$V(this.form.actes_locked, 1); if(confirmeCloture()) {this.form.submit()};">Cloturer le codage</button>
    </td>
  </tr>
  {{elseif $_plage->actes_locked}}
  <tr>
    <th class="category" colspan="2">Codage cloturé</th>
  </tr>
  {{/if}}
  {{/if}}
  
</table>

</form>

 <table class="tbl">
  {{if $_plage->_ref_operations}}
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=0 operations=$_plage->_ref_operations}}
  {{/if}}

  {{if $_plage->_unordered_operations}}
  <tr>
    <th colspan="10">Non placées</th>
  </tr>
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=0 operations=$_plage->_unordered_operations}}
  {{/if}}
</table>
{{/foreach}}

<!-- Déplacées -->
{{if $salle->_ref_deplacees|@count}}
<hr />
<table class="form">
  <tr>
    <th class="category" colspan="2">
      Déplacées
    </th>
  </tr>
</table>
<table class="tbl">
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=1 operations=$salle->_ref_deplacees}}
</table>
{{/if}}

<!-- Urgences -->
{{if $salle->_ref_urgences|@count}}
<hr />
<table class="form">
  <tr>
    <th class="category" colspan="2">
      Urgences
    </th>
  </tr>        
</table>
<table class="tbl">
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=1 operations=$salle->_ref_urgences}}
</table>
{{/if}}