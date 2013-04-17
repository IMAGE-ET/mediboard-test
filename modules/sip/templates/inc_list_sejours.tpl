{{*
  * List patient
  *
  * @category sip
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  * @version  SVN: $Id:$
  * @link     http://www.mediboard.org
*}}

<script>
  Main.add(function(){
    var form = getForm("find_candidates");
    form.elements.finder.disabled = '{{$pointer}}' ? "" : "disabled";
    $V(form.pointer, '{{$pointer}}');
  });
</script>

<table class="tbl">
  <tr>
    <th>{{tr}}CPatient{{/tr}}</th>
    <th class="narrow">{{tr}}CSejour-entree-court{{/tr}}</th>
    <th class="narrow">{{tr}}CSejour-sortie-court{{/tr}}</th>
    <th>{{tr}}CSejour-service_id-court{{/tr}}</th>
    <th>{{tr}}CSejour-_NDA{{/tr}}</th>
    <th>OID</th>
  </tr>

  <tr>
    <th class="section" colspan="100">{{$objects|@count}} résultats </th>
  </tr>

  {{foreach from=$objects item=_sejour}}
    <tr>
      <td>
        {{$_sejour->_ref_patient->_view}}
      </td>
      <td>
        {{mb_value object=$_sejour field="entree"}}
      </td>
      <td>
        {{mb_value object=$_sejour field="sortie"}}
      </td>
      <td>
        {{$_sejour->_service}}
      </td>
      <td>
        {{mb_value object=$_sejour field="_NDA"}}
      </td>
      <td>
        {{$_sejour->_OID}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="100" class="empty">{{tr}}No result{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>