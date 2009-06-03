{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Calendar.regField(getForm("selection").date, null, {noView: true});
</script>
  
<form action="?" name="selection" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="vw_bloodSalvage_sspi" />

  <table class="form">
    <tr>
      <th class="category" colspan="2">
        <div style="float: right;">{{$hour|date_format:$dPconfig.time}}</div>
        {{$date|date_format:$dPconfig.longdate}}
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </th>
    </tr>
  </table>
</form>


<table class="tbl">
	<tr>
	  <th>Salle</th>
	  <th>Praticien</th>
	  <th>Patient</th>
	  <th>Entrée réveil</th>
	</tr>
	
	{{foreach from=$listReveil item=rspo}}
		<tr class="hoverable">
		  <td class="text">
  		  <a href="?m=bloodSalvage&amp;tab=vw_bloodSalvage_sspi&amp;op={{$rspo->_id}}" title="Gérer le Cell Saver">
  		    {{$rspo->_ref_salle->_view}}
        </a>
		  </td>
		  <td class="text">
  		  <a href="?m=bloodSalvage&amp;tab=vw_bloodSalvage_sspi&amp;op={{$rspo->_id}}" title="Gérer le Cell Saver">
  		    Dr {{$rspo->_ref_chir->_view}}
        </a>
		  </td>
		  <td class="text">
  		  <a href="?m=bloodSalvage&amp;tab=vw_bloodSalvage_sspi&amp;op={{$rspo->_id}}" title="Gérer le Cell Saver">  
  		    {{$rspo->_ref_sejour->_ref_patient->_view}}
        </a>
		  </td>
		  <td class="text">{{$rspo->entree_reveil|date_format:$dPconfig.time}}</td>
		</tr>
	{{foreachelse}}
	<tr>
	  <td colspan="4">{{tr}}CBloodSalvage.none{{/tr}}</td>
	</tr>
	{{/foreach}}
</table>
