{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function () {
  var form = getForm("personnelSalle");
  Calendar.regField(form.deb_personnel);
  Calendar.regField(form.fin_personnel);
});

</script>

<form name="personnelSalle" action="?" method="get" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="dPstats" />
<table class="main form">
  <tr>
    <th colspan="6" class="category">
    	Bilan pour le personnel :
			{{tr}}CPersonnel.emplacement.op{{/tr}}
			</th>
  </tr>
  <tr>
    <th>Praticien</th>
    <td>
      <select name="prat_personnel">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$listPrats item=curr_prat}}
        <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $prat_personnel}}selected="selected"{{/if}}
				        class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};">
          {{$curr_prat->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
    <th><label for="deb_personnel" title="Date de début">Début</label></th>
    <td><input type="hidden" name="deb_personnel" class="notNull date" value="{{$deb_personnel}}" /></td>
    <th><label for="fin_personnel" title="Date de fin">Fin</label></th>
    <td><input type="hidden" name="fin_personnel" class="notNull date" value="{{$fin_personnel}}" /></td>
  </tr>
  <tr>
    <td class="button" colspan="6">
      <button class="search" type="submit">Afficher</button>
    </td>
  </tr>
</table>

</form>
{{if $prat_personnel}}
	<table class="main tbl">
	  <tr>
	    <th>Date</th>
	    <th>Salle</th>
	    <th>Nb interv.</th>
	    <th>Durée prévue</th>
	    <th>Durée première à la dernière</th>
	    <th>Durée totale interv. (interv. pris en compte)</th>
	    <th>Nb aides op.</th>
	    <th>Nb panseuses</th>
			<th>Nb IADE</th>
	  </tr>
	  {{foreach from=$listPlages item=curr_plage}}
	  <tr>
	    <td>{{$curr_plage->date|date_format:"%d/%m/%Y"}}</td>
	    <td>{{$curr_plage->_ref_salle->_view}}</td>
	    <td>{{$curr_plage->_ref_operations|@count}}</td>
	    <td>{{$curr_plage->_duree_prevue|date_format:$dPconfig.time}}</td>
	    <td>{{$curr_plage->_duree_first_to_last|date_format:$dPconfig.time}}</td>
	    <td>
	      {{$curr_plage->_duree_total_op|date_format:$dPconfig.time}}
	      ({{$curr_plage->_op_for_duree_totale}}/{{$curr_plage->_ref_operations|@count}})
	    </td>
	    <td>{{$curr_plage->_ref_affectations_personnel.op|@count}}</td>
	    <td>{{$curr_plage->_ref_affectations_personnel.op_panseuse|@count}}</td>
			<td>{{$curr_plage->_ref_affectations_personnel.iade|@count}}</td>
	  </tr>
	  {{/foreach}}
	  <tr>
	    <td colspan="3"></td>
	    <td>
	      <strong>
	        {{$total.days_duree_prevue}}j
	        {{$total.duree_prevue|date_format:$dPconfig.time}}
	      </strong>
	    </td>
	    <td>
	      <strong>
	        {{$total.days_duree_first_to_last}}j
	        {{$total.duree_first_to_last|date_format:$dPconfig.time}}
	      </strong>
	    </td>
	    <td>
	      <strong>
	        {{$total.days_duree_reelle}}j
	        {{$total.duree_reelle|date_format:$dPconfig.time}}
	      </strong>
	    </td>
	    <td colspan="3"></td>
	  </tr>
	</table>
{{/if}}