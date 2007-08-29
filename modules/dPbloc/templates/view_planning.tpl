<!-- $Id$ -->

<script type="text/javascript">

function printAdmission(id) {
  var url = new Url;
  url.setModuleAction("dPadmissions", "print_admission");
  url.addParam("id", id);
  url.popup(700, 550, "Patient");
}

</script>


<table class="main">
  <tr>
    <th>
      <a href="#" onclick="window.print()">
        Planning du {{$filter->_date_min|date_format:"%d/%m/%Y"}}
        {{if $filter->_date_min != $filter->_date_max}}
        au {{$filter->_date_max|date_format:"%d/%m/%Y"}}
        {{/if}}
      </a>
    </th>
  </tr>
  {{foreach from=$plagesop item=curr_plageop}}
  <tr>
    <td class="text">
	  <b>Dr. {{$curr_plageop->_ref_chir->_view}} -
	  {{$curr_plageop->_ref_salle->nom}} de
	  {{$curr_plageop->debut|date_format:"%Hh%M"}} à {{$curr_plageop->fin|date_format:"%Hh%M"}}
    le {{$curr_plageop->date|date_format:"%d/%m/%Y"}}</b>
    {{if $curr_plageop->_ref_anesth->_view}}
	    <b> - Anesthesiste : Dr. {{$curr_plageop->_ref_anesth->_view}}</b>
	  {{/if}}
    {{if $curr_plageop->_ref_personnel}}
      <b> - Personnel : 
      {{foreach from=$curr_plageop->_ref_personnel item=_personnel}}
      {{foreach from=$_personnel->_ref_user item=_user}}
         {{$_user->_view}};
      {{/foreach}}
      {{/foreach}}
      </b>
    {{/if}}
	</td>
  </tr>
  <tr>
    <td>
	</td>
  </tr>
      
  <tr>
    <td>
	  <table class="tbl">
	    <tr>
		  <th colspan="7"><b>Intervention</b></th>
		  <th colspan="4"><b>Patient</b></th>
		</tr>
		<tr>
		  <th>Heure</th>
		  <th>Intervention</th>
		  <th>Coté</th>
      <th>Anesthésie</th>
      <th>Hospi</th>
		  <th>Remarques</th>
		  <th>Matériel</th>
		  <th>Nom - Prénom</th>
		  <th>Age</th>
		  <th>Chambre</th>
		</tr>
		{{foreach from=$curr_plageop->_ref_operations item=curr_op}}
		<tr>
		  {{if $curr_op->annulee}}
		  <td class="cancelled">ANNULEE</td>
		  {{else}}
		  <td>{{$curr_op->time_operation|date_format:"%Hh%M"}}</td>
		  {{/if}}
		  <td class="text">
        {{if $curr_op->libelle}}
        <em>[{{$curr_op->libelle}}]</em>
        <br />
        {{/if}}
        {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
        {{if !$curr_code->_code7}}<strong>{{/if}}
        {{$curr_code->libelleLong|truncate:60:"...":false}}
        <em>({{$curr_code->code}})</em>
        {{if !$curr_code->_code7}}</strong>{{/if}}
        <br/>
        {{/foreach}}
      </td>
		  <td>{{$curr_op->cote|truncate:1:""|capitalize}}</td>
          <td>{{if $curr_op->type_anesth != null}}{{$curr_op->_lu_type_anesth}}{{else}}Non Disponible{{/if}}</td>
          <td>{{$curr_op->_ref_sejour->type|truncate:1:""|capitalize}}</td>
		  <td class="text">{{$curr_op->rques|nl2br}}</td>
		  <td class="text">
		    {{if $curr_op->commande_mat == '0' && $curr_op->materiel != ''}}<em>Materiel manquant:</em>{{/if}}
		    {{$curr_op->materiel|nl2br}}
		  </td>
		  <td>
		    <a href="#" onclick="printAdmission({{$curr_op->_ref_sejour->sejour_id}})">
		      {{$curr_op->_ref_sejour->_ref_patient->_view}}
		    </a>
		  </td>
		  <td>
		    <a href="#" onclick="printAdmission({{$curr_op->_ref_sejour->sejour_id}})">
		      {{$curr_op->_ref_sejour->_ref_patient->_age}} ans
		    </a>
		  </td>
		  <td class="text">
        {{assign var="affectation" value=$curr_op->_ref_sejour->_ref_first_affectation}}
		    {{if $affectation->affectation_id}}
		    {{$affectation->_ref_lit->_view}}
		    {{/if}}
		  </td>
		</tr>
		{{/foreach}}
	  </table>
	</td>
  </tr>
  {{/foreach}}
</table>