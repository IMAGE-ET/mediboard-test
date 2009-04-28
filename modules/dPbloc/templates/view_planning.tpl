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
	  <strong>Dr {{$curr_plageop->_ref_chir->_view}}</strong> -
	  <strong>{{$curr_plageop->_ref_salle->_view}}</strong>
	  de {{$curr_plageop->debut|date_format:$dPconfig.time}} à {{$curr_plageop->fin|date_format:$dPconfig.time}}
    le {{$curr_plageop->date|date_format:"%d/%m/%Y"}}
    
    {{if $curr_plageop->anesth_id}}
	    - Anesthesiste : <strong>Dr {{$curr_plageop->_ref_anesth->_view}}</strong>
	  {{/if}}
	  
	  {{assign var="plageOp_id" value=$curr_plageop->_id}}
	  {{if $affectations_plage.$plageOp_id.op}}
	    - Aide-opératoires:
	    {{foreach from=$affectations_plage.$plageOp_id.op item=_personnel}}
      {{$_personnel->_ref_personnel->_ref_user->_view}};
      {{/foreach}}
	  {{/if}}
	  
	  {{if $affectations_plage.$plageOp_id.op_panseuse}}
	    - Panseuses:
	    {{foreach from=$affectations_plage.$plageOp_id.op_panseuse item=_personnel}}
      {{$_personnel->_ref_personnel->_ref_user->_view}};
      {{/foreach}}	  
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
		{{foreach from=$curr_plageop->_ref_operations item=curr_op}}
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
</table>