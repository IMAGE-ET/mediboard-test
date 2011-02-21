{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=forms script=ex_class_editor}}
{{mb_include_script module=forms script=object_selector}}

{{main}}
ExClass.edit({{$ex_class->_id}});
ExClass.refreshList();
ExConcept.refreshList();
{{/main}}

<table class="main">
  <tr>
    <td id="exClassList" style="width: 20%;">
    	<!-- exClassList -->
    </td>
    <td id="exClassEditor" rowspan="2">
      <!-- exClassEditor -->
    </td>
  </tr>
	
	<tr>
		<td id="exConceptList">
      <!-- exConceptList -->
		</td>
	</tr>
</table>