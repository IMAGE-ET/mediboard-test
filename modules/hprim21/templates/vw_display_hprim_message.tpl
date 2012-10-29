{{* $Id: configure.tpl 10085 2010-09-16 09:20:46Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision: 10085 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  highlightHPR = function(form) {
    var url = new Url("hprim21", "ajax_display_hprim_message");
    url.addElement(form.message);
    url.requestUpdate("highlighted");
    return false;
  }
  
  {{if $message}}
    Main.add(function(){
      highlightHPR(getForm("hpr-input-form"));
    });
  {{/if}}
</script>

<form name="hpr-input-form" action="?" onsubmit="return highlightHPR(this)" method="get" class="prepared">
  <pre style="padding: 0; max-height: none;"><textarea name="message" rows="12" style="width: 100%; border: none; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; margin: 0; resize: vertical;">{{$message}}</textarea></pre>
  <button class="change">Valider</button>
</form>

<div id="highlighted"></div>
