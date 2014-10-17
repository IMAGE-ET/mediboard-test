{{*
 * Messages available
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<fieldset>
  <legend>{{tr}}{{$message}}{{/tr}} <span class="compact">({{tr}}{{$message}}-desc{{/tr}})</span></legend>
  
  <table class="tbl">
  {{foreach from=$messages_supported item=_message_supported}}
    <tr>
      <th class="narrow">{{tr}}{{$_message_supported->message}}{{/tr}}</th>
      <td class="text compact">
        {{tr}}{{$_message_supported->message}}-desc{{/tr}}

        {{if $_message_supported->_data_format instanceof CExchangeHL7v2}}
          <div>
            {{foreach from=$actor->_ref_hl7_transformations item=_hl7_transformation}}
              {{if $message == $_hl7_transformation->profil}}
                <!--<span class="circled">{{$_hl7_transformation->component}}</span>-->
              {{/if}}
            {{/foreach}}
          </div>

          {{mb_script module=hl7 script=hl7_transformation ajax=true}}

          <button style="float: right" title="{{tr}}CHL7v2Transformation{{/tr}}" class="target notext"
                  onclick="HL7_Transformation.viewSegments('{{$actor_guid}}', '{{$message}}', '{{$_message_supported->message}}')">
            {{tr}}CHL7v2Transformation{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  {{/foreach}}
  </table>

</fieldset>
