{{*
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="main tbl">
  <tr>
    <th colspan="7" class="category">
      {{tr}}CHL7Transformation.list{{/tr}}
    </th>
  </tr>
  <tr>
    <th class="section"> {{mb_title class=CHL7Transformation field=profil}} </th>
    <th class="section"> {{mb_title class=CHL7Transformation field=message}} </th>
    <th class="section"> {{mb_title class=CHL7Transformation field=version}} </th>
    <th class="section"> {{mb_title class=CHL7Transformation field=extension}} </th>
    <th class="section"> {{mb_title class=CHL7Transformation field=component}} </th>
    <th class="section"> {{mb_title class=CHL7Transformation field=action}} </th>
  </tr>

  {{foreach from=$actor->_ref_hl7_transformations item=_hl7_transformation}}
    <tr>
      <td>{{mb_value object=$_hl7_transformation field="profil"}}</td>
      <td>{{mb_value object=$_hl7_transformation field="message"}}</td>
      <td>{{mb_value object=$_hl7_transformation field="version"}}</td>
      <td>{{mb_value object=$_hl7_transformation field="extension"}}</td>
      <td class="text compact">{{mb_value object=$_hl7_transformation field="component"}}</td>
      <td class="narrow button">
        <span class="hl7-transformation-{{$_hl7_transformation->action}}" title="{{mb_value object=$_hl7_transformation field="action"}}"></span>
      </td>
    </tr>
  {{/foreach}}
</table>