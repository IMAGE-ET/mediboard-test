{{*
 * $Id$
 *  
 * @category Style
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_default var=show value=false}}
{{mb_default var=show_img value=true}}
{{mb_default var=root value=false}}

{{if "didacticiel"|module_active}}
  {{mb_script module="didacticiel" script="PermanentDidacticiel" ajax=true}}
  <a href="#1" title="{{tr}}portal-help{{/tr}}" onclick="PermanentDidacticiel.checkTutorials()">
    {{if $show_img}}
      <img src="style/{{if $root}}mediboard{{else}}{{$uistyle}}{{/if}}/images/icons/help.png"/>
    {{/if}}
    {{if $show}}
      {{tr}}portal-help{{/tr}}
    {{/if}}
  </a>

{{elseif "support"|module_active}}
  <a href="#1" title="{{tr}}portal-help{{/tr}}" onclick="Support.showHelp()">
    {{if $show_img}}
      <img src="style/{{$uistyle}}/images/icons/help.png" alt="{{tr}}portal-help{{/tr}}" />
    {{/if}}
    {{if $show}}
      {{tr}}portal-help{{/tr}}
    {{/if}}
  </a>

{{elseif $portal.help}}
  <a href="{{$portal.help}}" title="{{tr}}portal-help{{/tr}}" target="_blank">
    {{if $show_img}}
      <img src="style/{{$uistyle}}/images/icons/help.png" alt="{{tr}}portal-help{{/tr}}" />
    {{/if}}
    {{if $show}}
      {{tr}}portal-help{{/tr}}
    {{/if}}
  </a>
{{/if}}
