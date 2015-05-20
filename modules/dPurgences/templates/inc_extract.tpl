{{* $Id: vw_idx_urg.tpl 7671 2009-12-19 08:42:21Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7671 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=urgences script=rpu_sender ajax=true}}

<table class="main form">
  <tr>
    <th class="title" colspan="2">{{tr}}extract-{{$type}}-desc{{/tr}}</th>
  </tr>

  <tr>
    <th class="category narrow">{{tr}}Action{{/tr}}</th>
    <th class="category">{{tr}}Status{{/tr}}</th>
  </tr>

  <tr>
    <td>
      <form name="formExtraction_{{$type}}" action="?" method="get">
        <table class="form">
          <tr>
            <th>{{mb_label object=$extractPassages field="debut_selection"}}</th>
            <td>
              {{mb_field object=$extractPassages field="debut_selection" form="formExtraction_`$type`" register="true"}}
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$extractPassages field="fin_selection"}}</th>
            <td>{{mb_field object=$extractPassages field=fin_selection register=true form="formExtraction_`$type`" prop="dateTime"}}</td>
          </tr>
          <tr>
            <td colspan="2"><button class="tick" type="button" onclick="RPU_Sender.extract(this.form, '{{$type}}')">{{tr}}Extract{{/tr}}</button></td>
          </tr>
        </table>
      </form>
    </td>
    <td id="td_extract_{{$type}}"></td>
  </tr>

  <tr>
    <td>
      <button class="tick" type="button" id="encrypt_{{$type}}" onclick="RPU_Sender.encrypt('{{$type}}')">{{tr}}Encrypt{{/tr}}</button>
    </td>
    <td id="td_encrypt_{{$type}}"></td>
  </tr>

  <tr>
    <td>
      <button class="tick" type="button" id="transmit_{{$type}}" onclick="RPU_Sender.transmit('{{$type}}')">{{tr}}Transmit{{/tr}}</button>
    </td>
    <td id="td_transmit_{{$type}}"></td>
  </tr>
</table>