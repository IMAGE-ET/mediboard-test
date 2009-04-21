{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="form">
	<tr>
	  <th class="category">{{$fiche_ATC->code_ATC}} - {{$fiche_ATC->_libelle_ATC}} - {{$fiche_ATC->libelle}}</th>
	</tr>
	<tr>			 
	  <td style="height: 500px" colspan="2">{{mb_field object=$fiche_ATC field="description" id="htmlarea"}}</td>
	</tr>
</table>