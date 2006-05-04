<!-- $Id$ -->

{literal}
<script type="text/javascript">
function checkForm() {
  var form = document.paramFrm;
    
  if (form.deb.value > form.fin.value) {
    alert("Date de début superieure à la date de fin");
    return false;
  }
  
  popMateriel();
}

function popMateriel() {
  form = document.paramFrm;
  var url = new Url;
  url.setModuleAction("dPbloc", "print_materiel");
  url.addElement(form.deb);
  url.addElement(form.fin);
  url.popup(700, 550, 'Materiel');
}

function pageMain() {
  regFieldCalendar("paramFrm", "deb");
  regFieldCalendar("paramFrm", "fin");
}

</script>
{/literal}

<table class="main">
  <tr>
    <td>
	  <table class="tbl">
	    <tr>
		  <th>Date</th>
		  <th>Chirurgien</th>
		  <th>Patient</th>
		  <th>Opération</th>
		  <th>Matériel à commander</th>
		  <th>Valider</th>
		</tr>
		{foreach from=$op item=curr_op}
		<tr>
		  <td>{$curr_op->_ref_plageop->date|date_format:"%a %d %b %Y"}{*$curr_op.date|date_format:"%a %d %b %Y"*}</td>
		  <td class="text">Dr. {$curr_op->_ref_chir->_view}</td>
		  <td class="text">{$curr_op->_ref_pat->_view}</td>
		  <td class="text">
        {foreach from=$curr_op->_ext_codes_ccam item=curr_code}
        {$curr_code->code} : <em>{$curr_code->libelleLong}</em><br />
        {/foreach}
        (Côté : {$curr_op->cote})
      </td>
		  <td class="text">{$curr_op->materiel|nl2br}</td>
		  <td>
  			<form name="editFrm{$curr_op->operation_id}" action="index.php" method="get">
          <input type="hidden" name="m" value="dPbloc" />
          <input type="hidden" name="a" value="do_edit_mat" />
          <input type="hidden" name="id" value="{$curr_op->operation_id}" />
          {if $typeAff}
          <input type="hidden" name="value" value="n" />
  		    <input type="submit" value="annulé" />
  		    {else}
          <input type="hidden" name="value" value="o" />
          <input type="submit" value="commandé" />
    			{/if}
  			</form>
		  </td>
		</tr>
		{/foreach}
	  </table>
	</td>
	<td>
    <form name="paramFrm" action="?m=dPbloc" method="post" onsubmit="return checkForm()">

	  <table class="form">
	    <tr>
        <th colspan="2" class="category">Imprimer l'historique</th>
      </tr>
      <tr>
        <th><label for="deb" title="Date de début de la recherche">Début:</label></th>
        <td class="date" colspan="2">
          <div id="paramFrm_deb_da">{$deb|date_format:"%d/%m/%Y"}</div>
          <input type="hidden" name="deb" value="{$deb}" />
          <img id="paramFrm_deb_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de début"/>
        </td>
      </tr>
      <tr>
        <th><label for="fin" title="Date de fin de la recherche">Fin:</label></th>
        <td class="date" colspan="2">
          <div id="paramFrm_fin_da">{$fin|date_format:"%d/%m/%Y"}</div>
          <input type="hidden" name="fin" value="{$fin}" />
          <img id="paramFrm_fin_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de fin"/>
        </td>
      </tr>
	    <tr>
	      <td colspan="2" class="button">
	        <input type="button" value="Afficher" onclick="checkForm()" />
	      </td>
	    </tr>
	  </table>
	  
	  </form>

	  <form name="typeVue" action="?m={$m}" method="get">
        <input type="hidden" name="m" value="{$m}" />
        <select name="typeAff" onchange="submit()">
          <option value="0" {if $typeAff == 0}selected="selected"{/if}>Matériel à commander</option>
          <option value="1" {if $typeAff == 1}selected="selected"{/if}>Matériel à annuler</option>
        </select>
      </form>
    </td>
  </tr>
</table>