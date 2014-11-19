{{*
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}
{{mb_script module=dPpatients script=ins ajax=true}}

<fieldset>
  <legend>{{tr}}common-Automatic test{{/tr}}</legend>
  <div class="small-info">{{tr}}CINSPatient-msg-Explanation of automatic test{{/tr}}</div>
  <button type="button" onclick="INS.testInsc('auto')" class="tick">{{tr}}Execute{{/tr}}</button>
</fieldset>
<fieldset>
  <legend>{{tr}}common-Test drive{{/tr}}</legend>
  <div class="small-info">{{tr}}CINSPatient-msg-Explanation of test drive{{/tr}}</div>
  <button type="button" onclick="INS.testInsc('manuel')" class="tick">{{tr}}Execute{{/tr}}</button>
</fieldset>
<fieldset>
  <legend>{{tr}}common-Test input{{/tr}}</legend>
  <div class="small-info">{{tr}}CINSPatient-msg-Explanation of test input{{/tr}}</div>
  <button type="button" onclick="INS.testInsc('saisi')" class="tick">{{tr}}Execute{{/tr}}</button>
</fieldset>
<br/>
{{if $app->user_prefs.LogicielLectureVitale == 'vitaleVision'}}
  {{include file="../../dPpatients/templates/inc_vitalevision.tpl" debug=false keepFiles=true}}
{{elseif $app->user_prefs.LogicielLectureVitale == "mbHost"}}
  {{mb_script module=mbHost script=mbHost ajax=true}}
  {{mb_script module=mbHost script=vitaleCard ajax=true}}
{{else}}
  <div class="warning">{{tr}}common-msg-No vital card reading software{{/tr}}</div>
{{/if}}
<div id="modal-display-message" style="display: none;">
</div>
<div id ="test_insc"></div>
