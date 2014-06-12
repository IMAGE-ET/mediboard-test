{{*
 * $Id$
 *  
 * @category dPsante400
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{assign var=ipp value=$patient->_ref_IPP}}
{{assign var=nda value=$sejour->_ref_NDA}}


<table class="form">
  <tr>
    <th class="title">Saisie manuelle</th>
  </tr>
  <tr>
    <td>
      {{mb_include module=dPsante400 template=inc_form_ipp_nda idex=$ipp object=$patient field=_IPP}}
    </td>
  </tr>
  <tr>
    <td>
      {{mb_include module=dPsante400 template=inc_form_ipp_nda idex=$nda object=$sejour field=_NDA}}
    </td>
  </tr>
  <tr>
    <td class="button"><button type="button" class="close" onclick="Control.Modal.close()">{{tr}}Close{{/tr}}</button></td>
  </tr>
</table>