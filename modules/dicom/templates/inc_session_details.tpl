{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(function () {
    Control.Tabs.create('tabs-sessions-dicom', true);
  });
</script>

<ul id="tabs-sessions-dicom" class="control_tabs">
  {{foreach from=$session->_messages key=_type item=_pdu}}
    <li>
      <a href="#{{$_type}}">
        {{$_type}}
      </a>
    </li>
  {{/foreach}}
</ul>

<hr class="control_tabs"/>

{{foreach from=$session->_messages key=_type item=_pdu}}
  <div id="{{$_type}}" style="display: none;">
    <table class="tbl">
      <tr>
        <td>
          {{$_pdu->toString()}}
        </td>
      </tr>
    </table>
  </div>
{{/foreach}}