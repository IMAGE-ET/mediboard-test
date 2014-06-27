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

{{if "CAppUI::conf"|static_call:"dPsante400 CIdSante400 add_ipp_nda_manually":"CGroups-$g"}}
  {{mb_script module=dPsante400 script=Idex ajax=1}}

  <button class="edit notext" title="{{tr}}CIdSante400-create-IPP-NDA{{/tr}}"
          onclick="Idex.edit_manually('{{$sejour->_guid}}', '{{$patient->_guid}}', {{$callback}})">{{tr}}Modify{{/tr}}</button>
{{/if}}