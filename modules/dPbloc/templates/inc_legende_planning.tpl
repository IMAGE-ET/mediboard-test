{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  Calendar.regRedirectFlat("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
});
</script>

<div id="calendar-container"></div>

<table class="tbl">
  <tr>
  	<th>Liste des spécialités</th>
  </tr>
  {{foreach from=$listSpec item=curr_spec}}
  <tr>
    <td class="text" style="background: #{{$curr_spec->color}};">{{$curr_spec->text}}</td>
  </tr>
  {{/foreach}}
</table>
