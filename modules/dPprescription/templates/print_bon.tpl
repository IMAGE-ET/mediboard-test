{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


{{assign var=patient value=$prescription->_ref_patient}}
{{assign var=sejour value=$prescription->_ref_object}}
{{if $sejour->_ref_curr_affectation->_id}}
{{assign var=chambre value=$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre}}
{{else}}
{{assign var=chambre value=""}}
{{/if}}


<script type="text/javascript">

Main.add( function(){
  var oForm = getForm("filtreBons");
  
  dates = {
    limit: {
      start: "{{$sejour->_entree}}",
      stop: "{{$sejour->_sortie}}"
    },
    spots: []
  };
  Calendar.regField(oForm.debut, dates);
});
</script>  
  
<!-- Fermeture du tableau pour faire fonctionner le page-break -->
    </td>
  </tr>
</table>

<style type="text/css">
{{include file=../../dPcompteRendu/css/print.css header=4 footer=0 nodebug=true}}

/* decalage du header pour permettre l'insertion de filtres */
@media screen {
	div.header {
	  top: 4em;
	  border-bottom-width: 1px;
	}
}


</style>

<form name="filtreBons" method="get">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="a" value="print_bon" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
  <input type="hidden" name="dialog" value="1" />
	<table class="form">
	  <tr>
	    <th colspan="3" class="category">Filtres</th>
	  </tr>
	  <tr>
	    <td>
	      <button type="button" class="print" onclick="window.print();">Imprimer les bons</button>
	    </td>
	    <td>
	      Chapitre
		    <select name="sel_chapitre" onchange=this.form.submit();>
		      <option value="">&mdash; Tous</option>
		      {{foreach from=$chapitres item=_chapitre}}
		        <option value="{{$_chapitre}}" {{if $_chapitre == $sel_chapitre}}selected="selected"{{/if}}>{{tr}}CPrescription._chapitres.{{$_chapitre}}{{/tr}}</option>
		      {{/foreach}}
		    </select>
	    </td>
	    <td>
	      Date {{mb_field object=$filter_line field=debut class=notNull onchange=this.form.submit()}}
	    </td>
	  </tr>
	</table> 
</form>


<div class="header">
  {{if $chambre}}
  <span style="float: left">
    <strong>Chambre {{$chambre->_view}}</strong>
  </span>
  {{/if}}
  <span style="float: right">
    DE: {{$sejour->_entree|date_format:"%d/%m/%Y"}}<br />
    DS:  {{$sejour->_sortie|date_format:"%d/%m/%Y"}}
  </span>
  <div style="text-align: center">
    <strong>{{$patient->_view}}</strong>
    Né(e) le {{mb_value object=$patient field=naissance}} - ({{$patient->_age}} ans) - ({{$patient->_ref_constantes_medicales->poids}} kg)
    <br />
    {{assign var=operation value=$sejour->_ref_last_operation}}
    Intervention: {{$operation->libelle}} le {{$operation->_ref_plageop->date|date_format:"%d/%m/%Y"}}
    <strong>(I{{if $operation->_compteur_jour >=0}}+{{/if}}{{$operation->_compteur_jour}})</strong>
  </div>
</div>

<div class="footer">
</div>

{{foreach from=$bons key=chapitre item=_bons_by_hour name=foreach_chap}}
  {{foreach from=$_bons_by_hour key=hour item=_bons name=foreach_hour}}
    <div class="{{if $smarty.foreach.foreach_chap.last && $smarty.foreach.foreach_hour.last}}bodyWithoutPageBreak{{else}}body{{/if}}">
	    <table class="tbl">
			  <tr>
			    <th colspan="2">{{tr}}CPrescription._chapitres.{{$chapitre}}{{/tr}} - Examens demandés pour le {{$debut|date_format:$dPconfig.date}} à {{$hour}} h</th>
			  </tr>
		    {{foreach from=$_bons key=line_id item=_bon}}
		      {{assign var=line value=$lines.$line_id}}
		      <tr>
			      <td>
			      	{{$_bon}} {{$line->_unite_prise}} {{$line->_view}}
			      </td>
			      <td style="width: 20%">
			        Prescripteur: {{$line->_ref_praticien->_view}}
			      </td>
		      </tr>
		    {{/foreach}}
	    </table>
    </div>
  {{/foreach}}
{{/foreach}}

<!-- re-ouverture du tableau -->
<table>
  <tr>
    <td>