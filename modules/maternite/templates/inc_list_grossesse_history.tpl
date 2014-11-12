{{*
 * $Id$
 *  
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="main">
  <tr>
    <th class="title" colspan="2">{{tr}}History{{/tr}}</th>
  </tr>
  <tr>
    <th>{{tr}}CSejour{{/tr}}(s)</th>
    <th>{{tr}}CConsultation{{/tr}}s</th>
  </tr>
  <tr>
    <td>
      <div id="list_sejour_grossesse_histo">
        <table class="tbl">
          {{foreach from=$grossesse->_ref_sejours item=_sejour}}
            <tr>
              <td class="text compact">
                {{if !$_sejour->entree_reelle}}
                  <button onclick="admitForSejour('{{$_sejour->_id}}')" type="button" class="tick notext">{{tr}}CSejour-admit{{/tr}}</button>
                {{/if}}
                <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">{{$_sejour}}</span>
              </td>
            </tr>
          {{foreachelse}}
            <tr>
              <td class="empty">{{tr}}CSejour.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      </div>
      <script>
        ViewPort.SetAvlHeight('list_sejour_grossesse_histo', 1);
      </script>
    </td>
    <td>
      <div id="list_consult_grossesse_histo">
        <table class="tbl">
          {{foreach from=$grossesse->_ref_consultations item=_consultation}}
            <tr>
              <td class="text compact">
                <span onmouseover="ObjectTooltip.createEx(this, '{{$_consultation->_guid}}');">{{$_consultation}} {{if $_consultation->_ref_consult_anesth->_id}}<img src="images/icons/anesth.png" alt="" />{{/if}}</span>
              </td>
            </tr>
          {{foreachelse}}
            <tr>
              <td class="empty">{{tr}}CConsultation.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      </div>
      <script>
        ViewPort.SetAvlHeight('list_consult_grossesse_histo', 1);
      </script>
    </td>
  </tr>
</table>