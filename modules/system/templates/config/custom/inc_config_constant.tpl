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

{{if $is_last}}
  {{mb_script module=mediusers script=color_selector ajax=true}}

  <script type="text/javascript">
    ColorSelector.init = function(feature) {
      this.sForm  = "edit-configuration";
      this.sColor = feature + "-color";
      this.sColorView = feature + "-color";
      this.pop();
    }

    changeValues = function(feature) {
      var oForm = getForm("edit-configuration");
      var value = $V(oForm[feature + "-form"]) + '|' + $V(oForm[feature + "-graph"]) + '|'  + $V(oForm[feature + "-color"]);
      var input = $A(oForm.elements['c[' + feature + ']']).filter(function(element) {
        return !element.hasClassName('inherit-value');
      });
      $V(input[0], value);
    }

    Main.add(function() {
      var form = getForm("edit-configuration");
      form["{{$_feature}}-{{$components[0]}}"].addSpinner({type: 'num', min: 0, string:'num min|0'});
      form["{{$_feature}}-{{$components[1]}}"].addSpinner({type: 'num', min: 0, string:'num min|0'});
    });
  </script>

  <input type="hidden" name="c[{{$_feature}}]" value="{{'|'|implode:$value}}" {{if $is_inherited}} disabled {{/if}} />

  <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-form-desc{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-form{{/tr}} :</label>
  <input type="text" class="num" name="{{$_feature}}-{{$components[0]}}" value="{{$value[0]}}" {{if $is_inherited}} disabled {{/if}} size="2" onchange="changeValues('{{$_feature}}')"/>

  <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-graph-desc{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-graph{{/tr}} :</label>
  <input type="text" class="num" name="{{$_feature}}-{{$components[1]}}" value="{{$value[1]}}" {{if $is_inherited}} disabled {{/if}} size="2" onchange="changeValues('{{$_feature}}')"/>

  <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-color-desc{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-color{{/tr}} :</label>
  <input type="hidden" name="{{$_feature}}-{{$components[2]}}" value="{{$value[2]}}" onchange="changeValues('{{$_feature}}')"/>
  <button type="button" class="search" onclick="ColorSelector.init('{{$_feature}}')" {{if $is_inherited}} disabled {{/if}}>
    <span id="{{$_feature}}-{{$components[2]}}"
          style="display: inline-block; vertical-align: top; padding: 0; margin: 0; border: none; width: 16px; height: 16px; background-color: {{if $value[2]}}#{{$value[2]}}{{else}}transparent{{/if}}; ">
    </span>
  </button>
{{else}}
  <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-form{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-form{{/tr}} :</label> {{$value[0]}}
  <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-graph{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-graph{{/tr}} :</label> {{$value[1]}}
  <label title="{{tr}}config-dPpatient-CConstantesMedicales-selection-color{{/tr}}">{{tr}}config-dPpatient-CConstantesMedicales-selection-color{{/tr}} :</label>
  <span style="display: inline-block; vertical-align: top; padding: 0; margin: 0; border: none; width: 16px; height: 16px; background-color: {{if $value[2]}}#{{$value[2]}}{{else}}transparent{{/if}}; ">
 </span>
{{/if}}
