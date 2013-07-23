{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{if "didacticiel"|module_active}}
  {{mb_script module="didacticiel" script="permanence_didacticiel" ajax=true}}
  <a href="#1" onclick="PermanentDidacticiel.checkTutorials()">
    <img src="style/{{$uistyle}}/images/icons/help.png"  />
  </a>
{{elseif $portal.help}}
  <a href="{{$portal.help}}" title="{{tr}}portal-help{{/tr}}" target="_blank">
    <img src="style/{{$uistyle}}/images/icons/help.png" alt="{{tr}}portal-help{{/tr}}" />
  </a>
{{/if}}