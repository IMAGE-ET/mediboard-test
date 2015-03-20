{{*
 * $Id$
 *  
 * @category Dossier de soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="main layout">
  <tr>
    <td style="width: 33%;">
      <fieldset>
        <legend>
          Transmissions et observations importantes
          <button class="search notext compact" type="button" onclick="PlanSoins.showModalAllTrans('{{$sejour->_id}}')"></button>
        </legend>
        <div id="dossier_suivi_lite" style="height: 140px; overflow-y: auto;"></div>
      </fieldset>
    </td>

    {{if "forms"|module_active}}
      <td style="width: 33%;">
        <div id="{{$unique_id_widget_forms}}_modal" style="width: 900px; height: 600px; display: none;"></div>

        <fieldset>
          <legend>
            Formulaires
            <button class="search notext compact" type="button"
                    onclick="ExObject.loadExObjects('{{$sejour->_class}}', '{{$sejour->_id}}', '{{$unique_id_widget_forms}}_modal', 0);
                      Modal.open('{{$unique_id_widget_forms}}_modal', {showClose: true})"></button>
          </legend>
          <div id="{{$unique_id_widget_forms}}" style="height: 140px; overflow-y: auto;"></div>
        </fieldset>
      </td>
    {{/if}}

    <td style="width: 33%;">
      <fieldset>
        <legend>
          Surveillance
          <button class="search notext compact" type="button" onclick="openSurveillanceTab();"></button>
        </legend>
        <div id="constantes-medicales-widget" style="height: 140px;"></div>
      </fieldset>
    </td>
  </tr>
</table>

<div id="dossier_traitement"></div>