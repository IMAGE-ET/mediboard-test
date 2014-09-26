{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Calendar.regField(getForm("selectPatient").date, null, {noView: true});
</script>
  
<form action="?" name="selectPatient" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="vw_bloodSalvage_sspi" />

  <table class="form">
    <tr>
      <th class="category" colspan="2">
        {{$date|date_format:$conf.longdate}}
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
    <th>Sortie réveil</th>
	</tr>
	
	{{foreach from=$listReveil item=rspo}}
		<tr class="hoverable">
		  <td class="text">
  		  <a href="?m=bloodSalvage&amp;tab=vw_bloodSalvage_sspi&amp;op={{$rspo->_id}}" title="Gérer le Cell Saver">
  		    {{$rspo->_ref_salle}}
        </a>
		  </td>
		  <td class="text">
  		  <a href="?m=bloodSalvage&amp;tab=vw_bloodSalvage_sspi&amp;op={{$rspo->_id}}" title="Gérer le Cell Saver">
  		    Dr {{$rspo->_ref_chir}}
        </a>
		  </td>
		  <td class="text">
  		  <a href="?m=bloodSalvage&amp;tab=vw_bloodSalvage_sspi&amp;op={{$rspo->_id}}" title="Gérer le Cell Saver">  
  		    {{$rspo->_ref_sejour->_ref_patient}}
        </a>
		  </td>
		  <td class="text">{{$rspo->entree_reveil|date_format:$conf.time}}</td>
      <td class="text">{{$rspo->sortie_reveil_reel|date_format:$conf.time}}</td>
		</tr>
	{{foreachelse}}
	<tr>
	  <td colspan="10" class="empty">{{tr}}CBloodSalvage.none{{/tr}}</td>
	</tr>
	{{/foreach}}
</table>
