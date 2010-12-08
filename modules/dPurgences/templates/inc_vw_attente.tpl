{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{unique_id var=change_heure}}
<td {{if $_sejour->sortie_reelle}}class="opacity-60"{{/if}}>
  {{if $rpu->$debut}}
	  <form name="editHeure{{$change_heure}}" method="post" action="?">
	    {{mb_key object=$rpu}}
	    <input type="hidden" name="m" value="dPurgences" />
	    <input type="hidden" name="dosql" value="do_rpu_aed" />
	    <input type="hidden" name="ajax" value="1" />
	    <input type="hidden" name="{{$debut}}" value="" />
	    <input type="text" name="_debut_da" value="{{$rpu->$debut|date_format:$conf.time}}" class="time" readonly="readonly"/>
	    <input type="hidden" name="_debut" autocomplete="off" id="editHeure{{$change_heure}}_debut" value="{{$rpu->$debut|date_format:'%H:%M:%S'}}" class="time"
	    onchange="$V(this.form.{{$debut}}, '{{$rpu->$debut|date_format:'%Y-%m-%d'}} ' + $V(this.form._debut)); onSubmitFormAjax(this.form, {onComplete:refreshAttente.curry('{{$debut}}', '{{$fin}}', '{{$rpu->_id}}')})"></input> 
	    <button class="edit notext" type="button" onclick="Calendar.regField(this.form._debut); $(this).remove()">
	      Modifier l'heure
	    </button>
			{{if $isImedsInstalled && ($debut == "bio_depart")}}
			  {{mb_include module=dPImeds template=inc_sejour_labo sejour=$_sejour link="$rpu_link#Imeds"}}
		  {{/if}}
		</form>
		{{/if}}
</td>
<td id="{{$fin}}-{{$rpu->_id}}" {{if $_sejour->sortie_reelle}}class="opacity-60"{{/if}}>
  {{mb_include module=dPurgences template=inc_vw_fin_attente}}
</td>