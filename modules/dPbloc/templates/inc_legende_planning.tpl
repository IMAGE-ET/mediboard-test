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
  Calendar.regField(getForm("changeDate").date, null, {noView: true, inline: true, container: null});
});
</script>

<form name="changeDate" action="" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
</form>

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
