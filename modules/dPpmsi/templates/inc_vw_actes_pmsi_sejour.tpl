{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage pmsi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<table class="main layout">
  <tr>
    <td class="halfPane">
      <fieldset>
        <legend>Diagnostics PMSI</legend>
        <div id="diags_pmsi"></div>
      </fieldset>
    </td>
    <td>
      <fieldset>
        <legend>Diagnostics du dossier</legend>
        <div id="diags_dossier"></div>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td>
      <fieldset>
        <legend>Antécédents</legend>
        {{mb_include module=pmsi template=inc_vw_actes_pmsi_ant}}
      </fieldset>
    </td>
    <td>
      <fieldset>
        <legend>Traitements personnels</legend>
        {{mb_include module=pmsi template=inc_vw_actes_pmsi_trait}}
      </fieldset>
    </td>
  </tr>
  <tr>
    <td>
      <div id="export_CSejour_{{$sejour->_id}}"></div>
    </td>
  </tr>
</table>
{{mb_include module=pmsi template=inc_codage_actes subject=$sejour}}