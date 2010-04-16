{{* $Id: inc_vw_sorties.tpl 8332 2010-03-15 14:48:35Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 8332 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editSortieReelle" method="post" action="?m={{$m}}">
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="_modifier_sortie" value="0" />
  {{if $sejour->sortie_reelle && $rpu->sortie_autorisee}}
  	{{tr}}CRPU-sortie_assuree.1{{/tr}} à {{mb_field object=$sejour field="sortie_reelle" register=true form="editSortieReelle" onchange="submitFormAjax(this.form, 'systemMsg')"}}	
		<button class="cancel" type="button" onclick="getForm('editSortieAutorise').elements.sortie_autorisee.value=0; submitRPU();">
		  Annuler l'autorisation de sortie
		</button>
  {{else}}    
	  {{if $rpu->sortie_autorisee}}
	    <button class="cancel" type="button" onclick="getForm('editSortieAutorise').elements.sortie_autorisee.value=0;
          submitRPU();">Annuler l'autorisation de sortie</button>
		{{else}}
		  <input type="hidden" name="sortie_reelle" value="{{$now}}" />
      <button class="tick" type="button" onclick="getForm('editSortieAutorise').elements.sortie_autorisee.value=1;
          submitRPU();">{{mb_label object=$rpu field="sortie_autorisee"}}</button>
      <button class="tick" type="button" onclick="getForm('editSortieAutorise').elements.sortie_autorisee.value=1;
          this.form.elements._modifier_sortie.value=1; 
          validCotation(); 
          submitSejRpuConsult();">Autoriser et effectuer la sortie</button>
		{{/if}}
  {{/if}}
</form>

<form name="formValidCotation" action="" method="post"> 
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="consultation_id" value="{{$consult->_id}}" />
  <input type="hidden" name="valide" value="1" />
</form>
