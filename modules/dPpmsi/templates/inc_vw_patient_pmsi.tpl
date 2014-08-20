{{*
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage dPpmsi
 * @author     SARL OpenXtrem
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 *}}

<table class="main layout">
  <tr>
    <td class="halfPane">
      {{mb_include module=patients template=inc_vw_identite_patient}}
    </td>
    <td class="halfPane" rowspan="2">
      {{if $sejour->_id}}
        {{mb_include module=patients template=inc_vw_dossier_sejour object=$sejour}}
      {{else}}
        <div class="big-info">
          Vous devez séléctionner un séjour pour accéder au dossier
        </div>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td style="vertical-align: top; height: 100%;">
        <table class="form">
          {{if ($conf.dPpmsi.systeme_facturation == "siemens") && $patient->_ref_IPP}}
            <tr>
              <th id="IPP">{{mb_include module=pmsi template=inc_ipp_form}}</th>
            </tr>
          {{/if}}

          {{if ($conf.dPpmsi.systeme_facturation == "siemens") && $sejour->_id}}
            <tr>
              <td id="Numdos{{$sejour->sejour_id}}" class="text">
                {{mb_include module=pmsi template=inc_numdos_form}}
              </td>
            </tr>
          {{/if}}
        </table>
    </td>
  </tr>
</table>