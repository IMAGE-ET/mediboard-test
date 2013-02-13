{{*
  * Choix d'un service destinataire pour placer le patient
  *  
  * @category dPhospi
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

{{math equation=x+1 x=$secteurs|@count assign=colspan}}

<script type="text/javascript">
  mapService = function(service_id) {
    var form = getForm("cutAffectation");
    $V(form.service_id, service_id);
    form.onsubmit();
    loadNonPlaces();
  }
</script>

<div style="overflow-x: auto;">
  <form name="selectService" method="get">
    <table class="tbl">
      <tr>
        <th colspan="{{$colspan}}">
          {{tr}}CService-title-selection{{/tr}}
        </th>
      </tr>
      <tr>
      <tr>
        {{assign var=i value=0}}
        {{foreach from=$secteurs item=_secteur}}
        {{if $i == 6}}
        {{assign var=i value=0}}
      </tr>
      <tr>
        {{/if}}
        <td style="vertical-align: top;">
          <strong>{{mb_value object=$_secteur field=nom}}</strong>
          {{foreach from=$_secteur->_ref_services item=_service}}
            <p class="secteur_{{$_secteur->_id}}">
              <label>
                <input style="margin-left: 1em;" type="radio" name="service_id" value="{{$_service->_id}}"
                       {{if !in_array($_service->_id, array_keys($services_allowed))}}disabled="disabled"{{/if}} class="service"/> {{$_service}}
              </label>
            </p>
          {{/foreach}}
        </td>
        {{math equation=x+1 x=$i assign=i}}
        {{/foreach}}
        <td style="vertical-align: top;" colspan="{{math equation=x-y x=$secteurs|@count y=$i}}">
          <strong>Hors secteur</strong>
          {{foreach from=$all_services item=_service}}
            <p>
              <label>
                <input type="radio" name="service_id" value="{{$_service->_id}}" class="service"
                       {{if !in_array($_service->_id, array_keys($services_allowed))}}disabled="disabled"{{/if}} /> {{$_service}}
              </label>
            </p>
          {{/foreach}}
        </td>
      </tr>
      <tr>
        <td class="button" colspan="{{$colspan}}">
          <button type="button" class="tick" onclick="mapService($V(this.form.service_id)); Control.Modal.close();">{{tr}}Validate{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
</div>