{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}
{{mb_script module=pmsi script=PMSI}}
<script>
  Main.add(function () {
    var form = getForm("changeDate");
    window.calendar_date = Calendar.regField(form.date);
    form.onsubmit();
  });
</script>
<form method="get" name="changeDate" action="?m=dPpmsi&tab=vw_current_dossiers" class="watched prepared" onsubmit="return PMSI.loadCurrentDossiers(this);">
  <input type="hidden" name="page" value="0">
  <input type="hidden" name="pageOp" value="0">
  <input type="hidden" name="pageUrg" value="0">
  <table class="form">
    <tr>
      <th>Sélectionner le jour</th>
      <td>
        <input type="hidden" class="datetime" id="date" name="date" onchange="$V(this.form.page, '0'); $V(this.form.pageOp, '0'); $V(this.form.pageUrg, '0')" value="{{$date}}">
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<br/>
<div id="list-dossiers"></div>
