{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="duplicate_sejour" method="post" action="?">
	<input type="hidden" name="m" value="ssr" />
	<input type="hidden" name="dosql" value="do_duplicate_sejour_ssr_aed" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="sejour_id" value="" />
	<input type="hidden" name="original_sejour_id" value="" />
</form>

<script type="text/javascript">
	
duplicateSejour = function(sejour_id, original_sejour_id){
  var oForm = getForm("duplicate_sejour");
	$V(oForm.sejour_id, sejour_id);
	$V(oForm.original_sejour_id, original_sejour_id);
  oForm.submit();
}
	
</script>

{{if $sejours|@count}}
	<table class="tbl">
		<tr>
			<th class="title" colspan="6">
				Séjours SSR antérieurs
			</th>
		</tr>
		
		<tr>
      <th style="width:  5em;">{{mb_title class="CSejour" field="entree"}}</th>
      <th style="width:  5em;">{{mb_title class="CSejour" field="sortie"}}</th>
      <th style="width: 20em;">{{mb_title class="CSejour" field="libelle"}}</th>
      <th style="width: 12em;">{{mb_title class=CSejour field=praticien_id}}</th>
      <th class="narrow"><label title="Evénements planifiés pour ce patient (ce jour - pendant tout le séjour)">Evt.</label></th>
			<th></th>
    </tr>
		
	  {{foreach from=$sejours item=_sejour}}
	  <tr>
	  	<td>
        {{mb_value object=$_sejour field=entree format=$conf.date}}
      </td>
		  <td>
        {{mb_value object=$_sejour field=entree format=$conf.date}}
      </td>
			<td class="text">
        {{mb_include module=system template=inc_get_notes_image object=$_sejour mode=view float=right}}
        {{mb_value object=$_sejour field=libelle}}
        {{assign var=libelle value=$_sejour->libelle|upper}}
        {{assign var=color value=$colors.$libelle}}
        {{if $color->color}}
          <div class="motif-color" style="background-color: #{{$color->color}};" />
        {{/if}}
      </td>
		
		  <td class="text">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
        {{assign var=prat_demandeur value=$_sejour->_ref_bilan_ssr->_ref_prat_demandeur}}
        {{if $prat_demandeur->_id}} 
        <br />{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$prat_demandeur}}
        {{/if}}
      </td>
			
			<td style="text-align: center;">
        {{assign var=prescription value=$_sejour->_ref_prescription_sejour}}
        {{if $prescription->_id}}
          {{if $prescription->_count_recent_modif_presc}}
            <img src="images/icons/ampoule.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
          {{else}}
            <img src="images/icons/ampoule_grey.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
          {{/if}}
        {{/if}}
      </td>
			
			<td class="button">
				{{if !$sejour->_ref_prescription_sejour->_count.prescription_line_element}}
				  <button type="button" class="tick notext" onclick="duplicateSejour('{{$sejour->_id}}', '{{$_sejour->_id}}');"></button>
				{{/if}}
			</td>
	  </tr>
	{{/foreach}}
  </table>
{{/if}}