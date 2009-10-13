{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<h1>Configuration du module {{tr}}{{$m}}{{/tr}}</h1>
<hr />
<script type="text/javascript">
function doAction(sAction) {
  var url = new Url;
  url.setModuleAction("hprimxml", "ajax_do_cfg_action");
  url.addParam("action", sAction);
  url.requestUpdate(sAction);
}
</script>
<table class="tbl">
  <tr>
    <th class="category" colspan="10">Installation des schémas HPRIM XML</th>
  </tr>
  <tr>
    <th class="category">Action</th>
    <th class="category">Status</th>
  </tr>
  <tr>
    <td onclick="doAction('extractFiles');">
      <button class="tick">Installation HPRIM 'EvementPatient'</button>
    </td>
    <td class="text" id="extractFiles" />
  </tr>
</table>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form"> 
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<hr />
