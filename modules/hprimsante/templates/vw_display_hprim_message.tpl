{{*
 * $Id$
 *
 * @category hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  highlightMessage = function(form) {
    return Url.update(form, "highlighted");
  };
  
  {{if $message}}
    Main.add(function(){
      highlightMessage(getForm("hpr-input-form"));
    });
  {{/if}}
</script>

<form name="hpr-input-form" action="?m=hprimsante&a=ajax_display_hprim_message"
      method="post" class="prepared" onsubmit="return highlightMessage(this)">
  <pre style="padding: 0; max-height: none;"><textarea name="message" rows="12" style="width: 100%; border: none; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; margin: 0; resize: vertical;">{{$message}}</textarea></pre>
  <button class="change" type="submit">{{tr}}Validate{{/tr}}</button>
</form>

<div id="highlighted"></div>
