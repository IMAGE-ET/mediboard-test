{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function printAdmission(id) {
  var url = new Url("dPadmissions", "print_admission");
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
  {{foreach from=$listDates key=curr_date item=listPlages}}
  {{foreach from=$listPlages key=curr_plage_id item=curr_plageop}}
  {{if $curr_plage_id == "hors_plage"}}
  <tr>
    <td class="text">
      <strong>Interventions hors plage</strong>
      du {{$curr_date|date_format:"%d/%m/%Y"}}
    </td>
  </tr>
  {{else}}
  <tr>
    <td class="text">
      {{if $curr_plageop->chir_id}}
        <strong>Dr {{$curr_plageop->_ref_chir->_view}}</strong> -
      {{else}}
        <strong>{{$curr_plageop->_ref_spec->_view}}</strong> -
      {{/if}}
      <strong>{{$curr_plageop->_ref_salle->_view}}</strong>
      de {{$curr_plageop->debut|date_format:$dPconfig.time}} à {{$curr_plageop->fin|date_format:$dPconfig.time}}
      le {{$curr_plageop->date|date_format:"%d/%m/%Y"}}
    
      {{if $curr_plageop->anesth_id}}
        - Anesthesiste : <strong>Dr {{$curr_plageop->_ref_anesth->_view}}</strong>
      {{/if}}
			
      {{assign var="plageOp_id" value=$curr_plageop->_id}}
			<!-- Affichage du personnel prevu pour la plage operatoire -->
			{{foreach from=$affectations_plage.$plageOp_id key=type_affect item=_affectations}}
			  {{if $_affectations|@count}}
				  - {{tr}}CPersonnel.emplacement.{{$type_affect}}{{/tr}} :
	        {{foreach from=$_affectations item=_personnel}}
	          {{$_personnel->_ref_personnel->_ref_user->_view}};
	        {{/foreach}}
				{{/if}}
      {{/foreach}}
    </td>
  </tr>
  {{/if}}
  <tr>
    <td />
  </tr>
      
  <tr>
    <td>
      <table class="tbl">
        <tr>
        {{assign var="col1" value=$dPconfig.dPbloc.CPlageOp.planning.col1}}
        {{assign var="col2" value=$dPconfig.dPbloc.CPlageOp.planning.col2}}
        {{assign var="col3" value=$dPconfig.dPbloc.CPlageOp.planning.col3}}
     
        {{assign var=suffixe value="_title.tpl"}}
        {{include file=inc_planning/$col1$suffixe}}
        {{include file=inc_planning/$col2$suffixe}}
        {{include file=inc_planning/$col3$suffixe}}
        </tr>
        <tr>
        {{assign var=suffixe value="_header.tpl"}}
        {{include file=inc_planning/$col1$suffixe}}
        {{include file=inc_planning/$col2$suffixe}}
        {{include file=inc_planning/$col3$suffixe}}
        </tr>
        {{if $curr_plage_id == "hors_plage"}}
          {{assign var=listOperations value=$curr_plageop}}
        {{else}}
          {{assign var=listOperations value=$curr_plageop->_ref_operations}}
        {{/if}}
        {{foreach from=$listOperations item=curr_op}}
        <tr>
        {{assign var=sejour value=$curr_op->_ref_sejour}}
        {{assign var=patient value=$sejour->_ref_patient}}
   
        {{assign var=suffixe value="_content.tpl"}}
        {{include file=inc_planning/$col1$suffixe}}
        {{include file=inc_planning/$col2$suffixe}}
        {{include file=inc_planning/$col3$suffixe}}
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}
</table>