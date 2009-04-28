{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function checkFormPrint() {
  var form = document.paramFrm;
    
  if(!(checkForm(form))){
    return false;
  }
  
  popMateriel();
}

function popMateriel() {
  form = document.paramFrm;
  var url = new Url;
  url.setModuleAction("dPbloc", "print_materiel");
  url.addElement(form._date_min);
  url.addElement(form._date_max);
  url.popup(700, 550, 'Materiel');
}
</script>


<table class="main">
  <tr>
    <td>
	  <table class="tbl">
	    <tr>
		  <th>Date</th>
		  <th>Chirurgien</th>
		  <th>Patient</th>
		  <th>Intervention</th>
		  <th>Matériel à commander</th>
		  <th>Valider</th>
		</tr>
		{{foreach from=$op item=curr_op}}
		<tr>
		  <td>{{$curr_op->_datetime|date_format:"%a %d %b %Y"}}</td>
		  <td class="text">Dr {{$curr_op->_ref_chir->_view}}</td>
		  <td class="text">{{$curr_op->_ref_sejour->_ref_patient->_view}}</td>
		  <td class="text">
        {{if $curr_op->libelle}}
        <em>[{{$curr_op->libelle}}]</em>
        <br />
        {{/if}}
        {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
        {{$curr_code->code}} : <em>{{$curr_code->libelleLong}}</em><br />
        {{/foreach}}
        (Côté : {{tr}}COperation.cote.{{$curr_op->cote}}{{/tr}})
      </td>
		  <td class="text">{{$curr_op->materiel|nl2br}}</td>
		  <td>
  			<form name="editFrm{{$curr_op->operation_id}}" action="?m=dPbloc" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$curr_op->_id}}" />
          {{if $typeAff}}
          <input type="hidden" name="commande_mat" value="0" />
  		    <button type="submit" class="cancel">annulé</button>
  		    {{else}}
          <input type="hidden" name="commande_mat" value="1" />
          <button type="submit" class="submit">commandé</button>
    			{{/if}}
  			</form>
		  </td>
		</tr>
		{{/foreach}}
	  </table>
	</td>
	<td>
    <form name="paramFrm" action="?m=dPbloc" method="post" onsubmit="return checkFormPrint()">

	  <table class="form">
	    <tr>
        <th colspan="2" class="category">Imprimer l'historique</th>
      </tr>
      <tr>
        <td>{{mb_label object=$filter field="_date_min"}}</td>
        <td class="date">{{mb_field object=$filter field="_date_min" form="paramFrm" canNull="false" register=true}} </td>
      </tr>
      <tr>
        <td>{{mb_label object=$filter field="_date_max"}}</td>
        <td class="date" >{{mb_field object=$filter field="_date_max" form="paramFrm" canNull="false" register=true}}</td>
      </tr>
  	  <tr>
        <td colspan="2" class="button">
          <button type="button" onclick="checkFormPrint()" class="search">Afficher</button>
        </td>
	    </tr>
	  </table>
	  
	  </form>

	  <form name="typeVue" action="?m={{$m}}" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <select name="typeAff" onchange="submit()">
          <option value="0" {{if $typeAff == 0}}selected="selected"{{/if}}>Matériel à commander</option>
          <option value="1" {{if $typeAff == 1}}selected="selected"{{/if}}>Matériel à annuler</option>
        </select>
      </form>
    </td>
  </tr>
</table>