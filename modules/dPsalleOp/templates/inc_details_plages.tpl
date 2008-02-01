{{foreach from=$plages item=curr_plage}}
<hr />

<form name="anesth{{$curr_plage->plageop_id}}" action="?" method="post">

<input type="hidden" name="m" value="dPbloc" />
<input type="hidden" name="otherm" value="{{$m}}" />
<input type="hidden" name="dosql" value="do_plagesop_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="_repeat" value="1" />
<input type="hidden" name="plageop_id" value="{{$curr_plage->_id}}" />
<input type="hidden" name="chir_id" value="{{$curr_plage->chir_id}}" />
<input type="hidden" name="spec_id" value="{{$curr_plage->spec_id}}" />

<table class="form">
  <tr>
    <th class="category{{if $vueReduite}} text{{/if}}" colspan="2">
      <a href="?m=dPbloc&amp;tab=vw_edit_interventions&amp;plageop_id={{$curr_plage->_id}}" title="Administrer la plage">
        Chir : Dr. {{$curr_plage->_ref_chir->_view}}
        {{if $vueReduite}}
          <br />
        {{else}}
          -
        {{/if}}
        {{$curr_plage->debut|date_format:"%Hh%M"}} à {{$curr_plage->fin|date_format:"%Hh%M"}}
      </a>
    </th>
  </tr>
  
  <tr>
    {{if $vueReduite}}
    <th class="category" colspan="2">
      {{if $curr_plage->anesth_id}}
        Anesth : Dr. {{$curr_plage->_ref_anesth->_view}}
      {{else}}
        -
      {{/if}}
    </th>
    {{else}}
    <th><label for="anesth_id" title="Anesthésiste associé à la plage d'opération">Anesthésiste</label></th>
    <td>
      <select name="anesth_id" onchange="submit()">
        <option value="">&mdash; Choisir un anesthésiste</option>
        {{foreach from=$listAnesths item=curr_anesth}}
        <option value="{{$curr_anesth->user_id}}" {{if $curr_plage->anesth_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
          {{$curr_anesth->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
    {{/if}}
  </tr>
  
</table>

</form>

 <table class="tbl">
  {{include file=inc_liste_operations.tpl operations=$curr_plage->_ref_operations}}

  {{if $curr_plage->_unordered_operations}}
  <tr>
    <th colspan="10">Non placées</th>
  </tr>
  {{include file=inc_liste_operations.tpl operations=$curr_plage->_unordered_operations}}
  {{/if}}
</table>
{{/foreach}}

{{if $urgences|@count}}

<hr />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      Urgences
    </th>
  </tr>        
</table>
<table class="tbl">
  {{include file=inc_liste_operations.tpl urgence=1 operations=$urgences}}
</table>
{{/if}}