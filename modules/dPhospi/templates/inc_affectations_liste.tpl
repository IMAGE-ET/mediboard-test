<table class="tbl">
  <tr>
    <th class="title">
      {{tr}}CSejour.groupe.{{$group_name}}{{/tr}}
    </th>
  </tr>
</table>

{{foreach from=$sejourNonAffectes item=_sejour}}
<form name="addAffectationsejour_{{$_sejour->_id}}" action="?m={{$m}}" method="post">

<input type="hidden" name="dosql" value="do_affectation_aed" />
<input type="hidden" name="lit_id" value="" />
<input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
<input type="hidden" name="entree" value="{{$_sejour->entree_prevue}}" />
<input type="hidden" name="sortie" value="{{$_sejour->sortie_prevue}}" />

</form>

<table class="sejourcollapse" id="sejour_{{$_sejour->_id}}">
  <tr>
    <td class="selectsejour" style="background:#{{$_sejour->_ref_praticien->_ref_function->color}}">
      {{if !$dPconfig.dPhospi.pathologies || $_sejour->pathologie}}  
      <input type="radio" id="hospitalisation{{$_sejour->_id}}" onclick="selectHospitalisation({{$_sejour->_id}})" />
      <script type="text/javascript">new Draggable('sejour_{{$_sejour->_id}}', {revert: true, scroll: window})</script>
      {{/if}}
    </td>
    <td class="patient" onclick="flipSejour({{$_sejour->_id}})">
      <strong {{if !$_sejour->entree_reelle}}class="patient-not-arrived"{{/if}} {{if $_sejour->septique}}class="septique"{{/if}}><a name="sejour{{$_sejour->_id}}">{{$_sejour->_ref_patient->_view}}</a></strong>
      {{if $_sejour->type != "ambu" && $_sejour->type != "exte"}}
      ({{$_sejour->_duree_prevue}}j - {{$_sejour->_ref_praticien->_shortview}})
      {{else}}
      ({{$_sejour->type|truncate:1:""|capitalize}} - {{$_sejour->_ref_praticien->_shortview}})
      {{/if}}
      {{if $_sejour->_couvert_cmu}}
      <div style="float: right;"><strong>CMU</strong></div>
      {{/if}}
    </td>
  </tr> 
  <tr>
    <td colspan="2">
      <a href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$_sejour->_id}}">
        <img style="float: right;" src="images/icons/planning.png" />
      </a>
      <strong>Entrée</strong> : {{$_sejour->entree_prevue|date_format:"%a %d %b %Hh%M"}}
      <br />
      <strong>Sortie</strong> : {{$_sejour->sortie_prevue|date_format:"%a %d %b %Hh%M"}}
    </td>
  </tr>
	
  <tr>
    <td colspan="2"><strong>Age</strong> : {{$_sejour->_ref_patient->_age}} ans ({{mb_value object=$_sejour->_ref_patient field=naissance}})
  </tr>
	
  <tr>
    <td colspan="2">
    	<strong>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
    	</strong>
    </td>
  </tr>
  
	{{if $_sejour->prestation_id}}
  <tr>
    <td colspan="2"><strong>Prestation: </strong>{{$_sejour->_ref_prestation->_view}}</td>
  </tr>
  {{/if}}
	
  <tr>
    <td colspan="2">
      {{foreach from=$_sejour->_ref_operations item=_operation}}
        {{mb_include module=dPplanningOp template=inc_vw_operation operation=$_operation}}
      {{/foreach}}
    </td>
  </tr>
	
  <tr>
    <td colspan="2">
    
    <form name="EditSejour{{$_sejour->_id}}" action="?m=dPhospi" method="post">

    <input type="hidden" name="m" value="dPplanningOp" />
    <input type="hidden" name="otherm" value="dPhospi" />
    <input type="hidden" name="dosql" value="do_sejour_aed" />
    <input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
    
    <strong>Pathologie:</strong>
    <select name="pathologie">
      <option value="">&mdash; Choisir</option>
      {{foreach from=$pathos->_specs.categorie->_locales item=_patho}}
      <option {{if $_patho == $_sejour->pathologie}}selected="selected"{{/if}}>
      {{$_patho}}
      </option>
      {{/foreach}}
    </select>
    <br />
    <input type="radio" name="septique" value="0" {{if $_sejour->septique == 0}} checked="checked" {{/if}} />
    <label for="septique_0" title="Intervention propre">Propre</label>
    <input type="radio" name="septique" value="1" {{if $_sejour->septique == 1}} checked="checked" {{/if}} />
    <label for="septique_1" title="Séjour septique">Septique</label>

    <button class="submit" onclick="submit()">Valider</button>
    
    </form>
    
    </td>
  </tr>
	
  {{if $_sejour->rques}}
  <tr>
    <td class="highlight" colspan="2">
      <strong>Séjour</strong>: {{$_sejour->rques|nl2br}}
    </td>
  </tr>
  {{/if}}
  {{foreach from=$_sejour->_ref_operations item=_operation}}
  {{if $_operation->rques}}
  <tr>
    <td class="highlight" colspan="2">
      <strong>Intervention</strong>: {{$_operation->rques|nl2br}}
    </td>
  </tr>
  {{/if}}
  {{/foreach}}
  {{if $_sejour->_ref_patient->rques}}
  <tr>
    <td class="highlight" colspan="2">
      <strong>Patient</strong>: {{$_sejour->_ref_patient->rques|nl2br}}
    </td>
  </tr>
  {{/if}}
  {{if $_sejour->chambre_seule}}
  <tr>
    <td class="highlight" colspan="2">
      <strong>Chambre seule</strong>
    </td>
  </tr>
  {{else}}
  <tr>
    <td colspan="2">
      <strong>Chambre double</strong>
    </td>
  </tr>
  {{/if}}
</table>

{{/foreach}}