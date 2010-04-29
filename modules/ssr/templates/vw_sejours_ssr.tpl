{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=ssr script=sejours_ssr}}

<table id="sejours-ssr" class="main tbl">
	<tr>
		<th class="title" colspan="10">
			Séjours SSR du {{$date|date_format:$dPconfig.longdate}}
	    <form name="selDate" action="?" method="get">
	      <input type="hidden" name="m" value="{{$m}}" />
				<script type="text/javascript">
				Main.add(function () {
				  Calendar.regField(getForm("selDate").date, null, { noView: true} );
				});
				  
				</script>

	      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
	    </form>
	  </th>
	</tr>
	<tr>
    <th style="width: 20em;">{{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way url="?m=$m&tab=vw_sejours_ssr"}}</th>
    <th style="width:   1%;">
      <input type="text" size="6" onkeyup="SejoursSSR.filter(this)" id="filter-patient-name" />
    </th>
    <th style="width:  5em;">{{mb_colonne class="CSejour" field="entree"     order_col=$order_col order_way=$order_way url="?m=$m&tab=vw_sejours_ssr"}}</th>
    <th style="width:  5em;">{{mb_colonne class="CSejour" field="sortie"     order_col=$order_col order_way=$order_way url="?m=$m&tab=vw_sejours_ssr"}}</th>

		<th style="width:  5em;">{{mb_title class="CSejour" field="_num_dossier"}}</th>
    <th style="width: 20em;">{{mb_title class="CSejour" field="libelle"}}</th>
    <th style="width: 12em;">{{mb_title class="CBilanSSR" field="kine_id"}}</th>
	</tr>
	
	{{foreach from=$sejours key=sejour_id item=_sejour}}
	<tr>
		<td colspan="2" class="text">
			{{assign var=link value="?m=$m&tab=vw_aed_sejour_ssr&sejour_id=$sejour_id"}}
			{{mb_include template=inc_view_patient patient=$_sejour->_ref_patient}}
		</td>

    <td>{{mb_value object=$_sejour field=entree format=$dPconfig.date}}</td>
    <td>{{mb_value object=$_sejour field=sortie format=$dPconfig.date}}</td>
		
		<td>
      <a>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
         {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$_sejour->_num_dossier}}
        </span>
      </a>
		</td>
		
    <td class="text">
      {{mb_include module=system template=inc_get_notes_image object=$_sejour mode=view float=right}}
    	{{mb_value object=$_sejour field=libelle}}
		</td>
		
    {{assign var=bilan value=$_sejour->_ref_bilan_ssr}}
    <td class="text">
    	{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$bilan->_fwd.kine_id}}
		</td>
		
	</tr>
	{{foreachelse}}
	<tr>
		<td colspan="10">
			<em>{{tr}}CSejour.none{{/tr}}</em>
		</td>
	</tr>
	{{/foreach}}
</table>