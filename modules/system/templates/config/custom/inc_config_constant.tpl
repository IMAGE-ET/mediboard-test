{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage system
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{assign var=value value='|'|explode:$value}}
{{assign var=components value='|'|explode:$_prop.components}}
{{assign var=constant value=' '|explode:$_feature}}
{{math assign=index equation="x - 1" x=$constant|@count}}
{{assign var=constant value=$constant[$index]}}

{{if $is_last}}
  {{mb_script module=mediusers script=color_selector ajax=true}}

  <script type="text/javascript">
    ColorSelector.init = function(feature) {
      this.sForm  = "edit-configuration";
      this.sColor = feature + "-color";
      this.sColorView = feature + "-color";
      this.pop();
    }

    changeValuesConstants = function(feature) {
      var oForm = getForm("edit-configuration");
      var value = $V(oForm[feature + "-form"]) + '|' + $V(oForm[feature + "-graph"]) + '|'  + $V(oForm[feature + "-color"])
        + '|' + $V(oForm[feature + "-mode"]) + '|' + $V(oForm[feature + "-min"])  + '|' + $V(oForm[feature + "-max"])
        + '|' + $V(oForm[feature + "-norm_min"]) + '|' + $V(oForm[feature + "-norm_max"]);
      var input = $A(oForm.elements['c[' + feature + ']']).filter(function(element) {
        return !element.hasClassName('inherit-value');
      });
      $V(input[0], value);
    }

    changeValuesConstantsMode = function(feature) {
      var constant = feature.split(' ').last();
      var oForm = getForm("edit-configuration");
      var mode = $V(oForm[feature + "-mode"]);

      $('label_min_' + constant).title = $T('config-dPpatient-CConstantesMedicales-selection-min_' + mode + '-desc');
      $('label_max_' + constant).title = $T('config-dPpatient-CConstantesMedicales-selection-max_' + mode + '-desc');
      changeValuesConstants(feature);
    }

    Main.add(function() {
      var form = getForm("edit-configuration");
      form["{{$_feature}}-{{$components[0]}}"].addSpinner({type: 'num', min: -1, string:'num min|-1'});
      form["{{$_feature}}-{{$components[1]}}"].addSpinner({type: 'num', min: -1, string:'num min|-1'});
      form["{{$_feature}}-{{$components[4]}}"].addSpinner({type: 'num', min: 0, string:'num min|0'});
      form["{{$_feature}}-{{$components[5]}}"].addSpinner({type: 'num', min: 0, string:'num min|0'});
      form["{{$_feature}}-{{$components[6]}}"].addSpinner({type: 'num', min: 0, string:'num min|0'});
      form["{{$_feature}}-{{$components[7]}}"].addSpinner({type: 'num', min: 0, string:'num min|0'});
    });
  </script>

  <input type="hidden" name="c[{{$_feature}}]" value="{{'|'|implode:$value}}" {{if $is_inherited}} disabled {{/if}} />
  <table class="layout">
    <tr>
      <td>
        <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-form-desc{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-form{{/tr}} :</label>
      </td>
      <td>
        <input type="text" class="num" name="{{$_feature}}-{{$components[0]}}" value="{{$value[0]}}" {{if $is_inherited}} disabled {{/if}} size="2" onchange="changeValuesConstants('{{$_feature}}')"/>
      </td>
      <td>
        <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-graph-desc{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-graph{{/tr}} :</label>
      </td>
      <td>
        <input type="text" class="num" name="{{$_feature}}-{{$components[1]}}" value="{{$value[1]}}" {{if $is_inherited}} disabled {{/if}} size="2" onchange="changeValuesConstants('{{$_feature}}')"/>
      </td>
      <td>
        <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-color-desc{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-color{{/tr}} :</label>
      </td>
      <td>
        <input type="hidden" name="{{$_feature}}-{{$components[2]}}" value="{{$value[2]}}" onchange="changeValuesConstants('{{$_feature}}')"/>
        <button type="button" class="search" onclick="ColorSelector.init('{{$_feature}}')" {{if $is_inherited}} disabled {{/if}}>
          <span id="{{$_feature}}-{{$components[2]}}"
                style="display: inline-block; vertical-align: top; padding: 0; margin: 0; border: none; width: 16px; height: 16px; background-color: {{if $value[2]}}#{{$value[2]}}{{else}}transparent{{/if}}; ">
          </span>
        </button>
      </td>
    </tr>
    <tr>
      <td>
        <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-mode-desc{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-mode{{/tr}} :</label>
      </td>
      <td>
        <select name="{{$_feature}}-{{$components[3]}}" {{if $is_inherited}} disabled {{/if}} onchange="changeValuesConstantsMode('{{$_feature}}')">
          <option value="fixed" {{if array_key_exists(3, $value) && $value[3] == 'fixed'}}selected="selected" {{/if}}>{{tr}}config-dPpatient-CConstantesMedicales-selection-mode.fixed{{/tr}}</option>
          <option value="float" {{if array_key_exists(3, $value) && $value[3] == 'float'}}selected="selected" {{/if}}>{{tr}}config-dPpatient-CConstantesMedicales-selection-mode.float{{/tr}}</option>
        </select>
      </td>
      <td>
        <label id="label_min_{{$constant}}" title="{{tr}}config-dPpatient-CConstantesMedicales-selection-min{{if array_key_exists(7, $value)}}_{{$value[3]}}{{/if}}-desc{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-min{{/tr}} :</label>
      </td>
      <td>
        <input type="text" class="num" name="{{$_feature}}-{{$components[4]}}" value="{{if array_key_exists(4, $value)}}{{$value[4]}}{{/if}}" {{if $is_inherited}} disabled {{/if}} size="2" onchange="changeValuesConstants('{{$_feature}}')"/>
      </td>
      <td>
        <label id="label_max_{{$constant}}" title="{{tr}}config-dPpatient-CConstantesMedicales-selection-max{{if array_key_exists(7, $value)}}_{{$value[3]}}{{/if}}-desc{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-max{{/tr}} :</label>
      </td>
      <td>
        <input type="text" class="num" name="{{$_feature}}-{{$components[5]}}" value="{{if array_key_exists(5, $value)}}{{$value[5]}}{{/if}}" {{if $is_inherited}} disabled {{/if}} size="2" onchange="changeValuesConstants('{{$_feature}}')"/>
      </td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td>
        <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-norm_min-desc{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-norm_min{{/tr}} :</label>
      </td>
      <td>
        <input type="text" class="num" name="{{$_feature}}-{{$components[6]}}" value="{{if array_key_exists(6, $value)}}{{$value[6]}}{{/if}}" {{if $is_inherited}} disabled {{/if}} size="2" onchange="changeValuesConstants('{{$_feature}}')"/>
      </td>
      <td>
        <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-norm_max-desc{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-norm_max{{/tr}} :</label>
      </td>
      <td>
        <input type="text" class="num" name="{{$_feature}}-{{$components[7]}}" value="{{if array_key_exists(7, $value)}}{{$value[7]}}{{/if}}" {{if $is_inherited}} disabled {{/if}} size="2" onchange="changeValuesConstants('{{$_feature}}')"/>
      </td>
    </tr>
  </table>
{{else}}
  <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-form{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-form{{/tr}} :</label> {{$value[0]}}
  <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-graph{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-graph{{/tr}} :</label> {{$value[1]}}
  <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-color{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-color{{/tr}} :</label>
  <span style="display: inline-block; vertical-align: top; padding: 0; margin: 0; border: none; width: 16px; height: 16px; background-color: {{if $value[2]}}#{{$value[2]}}{{else}}transparent{{/if}}; "></span>
  {{if array_key_exists(3, $value)}}
    <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-mode{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-mode{{/tr}} :</label> {{tr}}config-dPpatient-CConstantesMedicales-selection-mode.{{$value[3]}}{{/tr}}
    <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-min{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-min{{/tr}} :</label> {{$value[4]}}
    <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-max{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-max{{/tr}} :</label> {{$value[5]}}
    <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-norm_min{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-norm_min{{/tr}} :</label> {{$value[6]}}
    <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-norm_max{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-norm_max{{/tr}} :</label> {{$value[7]}}
  {{/if}}
{{/if}}
