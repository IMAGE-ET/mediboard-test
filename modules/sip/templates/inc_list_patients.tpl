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
    var form = getForm("find");
    form.elements.finder.disabled = '{{$pointer}}' ? "" : "disabled";
    $V(form.pointer, '{{$pointer}}');
  });
</script>

<table class="tbl">
  <tr>
    <th>{{tr}}CPatient{{/tr}}</th>
    <th class="narrow">{{tr}}CPatient-naissance-court{{/tr}}</th>
    <th>{{tr}}CPatient-adresse{{/tr}}</th>
    <th class="narrow"></th>
  </tr>

  <tr>
    <th class="section" colspan="4">{{$patients|@count}} résultats </th>
  </tr>

  {{foreach from=$patients item=_patient}}
    <tr>
      <td>
        <div class="text noted">
          {{mb_value object=$_patient field="_view"}}
        </div>
      </td>
      <td>
        {{mb_value object=$_patient field="naissance"}}
      </td>
      <td class="text compact">
        <span style="white-space: nowrap;">{{$_patient->adresse|spancate:30}}</span>
        <span style="white-space: nowrap;">{{$_patient->cp}} {{$_patient->ville|spancate:20}}</span>
      </td>
      <td>
        <a class="button search notext" href="#" title="Afficher le dossier complet" style="margin: -1px;">
          {{tr}}Show{{/tr}}
        </a>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="100" class="empty">{{tr}}dPpatients-CPatient-no-exact-results{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>