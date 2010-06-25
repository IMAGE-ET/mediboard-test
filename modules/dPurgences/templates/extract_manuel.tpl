{{* $Id: vw_idx_rpu.tpl 7671 2009-12-19 08:42:21Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7671 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  var extract_passages_id;
  
  function extractRPU(form) {
    if (!checkForm(form)) {
      return;
    }
    var url = new Url("dPurgences", "ajax_extract_passages_rpu");
    url.addParam("debut_selection", $V(form.debut_selection));
    url.addParam("fin_selection", $V(form.fin_selection));
    url.requestUpdate('td_extract_rpu', { onComplete: function(){
      if (!$('td_extract_rpu').select('.error, .warning').length) {
         $('encrypt_rpu').disabled = false;
      }
     }});
  }

  {{if array_key_exists('urg', $types)}}
  function extractURG(form) {
    if (!checkForm(form)) {
      return;
    }
    var url = new Url("dPurgences", "ajax_extract_passages_urg");
    url.addParam("debut_selection", $V(form.debut_selection));
    url.addParam("fin_selection", $V(form.fin_selection));
    url.requestUpdate('td_extract_urg', { onComplete: function(){
      if (!$('td_extract_urg').select('.error, .warning').length) {
         $('encrypt_urg').disabled = false;
      }
     }});
  }
  {{/if}}
  
  function encrypt(type) {
    var url = new Url("dPurgences", "ajax_encrypt_passages");
    url.addParam("extract_passages_id", extract_passages_id);
    url.requestUpdate('td_encrypt_'+type, { onComplete: function(){
      if (!$('td_encrypt_'+type).select('.error, .warning').length) {
         $('transmit_'+type).disabled = false;
      }
     }});
  }
  
  function transmit(type) {
    var url = new Url("dPurgences", "ajax_transmit_passages");
    url.addParam("extract_passages_id", extract_passages_id);
    url.requestUpdate('td_transmit_'+type);
  }
  
  Main.add(function () {
    $('encrypt_rpu').disabled = true;
    $('transmit_rpu').disabled = true;
    {{if array_key_exists('urg', $types)}}
    $('encrypt_urg').disabled = true;
    $('transmit_urg').disabled = true;
    {{/if}}
  });

  Main.add(Control.Tabs.create.curry('tabs-extract', true));
</script>

<ul id="tabs-extract" class="control_tabs">
  {{if array_key_exists('rpu', $types)}}
    <li><a href="#RPU">{{tr}}extract-rpu{{/tr}}</a></li>
  {{/if}}
  {{if array_key_exists('urg', $types)}}
    <li><a href="#URG">{{tr}}extract-urg{{/tr}}</a></li>
  {{/if}}
</ul>

<hr class="control_tabs" />

{{if array_key_exists('rpu', $types)}}
<div id="RPU" style="display: none;">
  {{mb_include template=inc_extract_rpu}}
</div>
{{/if}}

{{if array_key_exists('urg', $types)}}
<div id="URG" style="display: none;">
  {{mb_include template=inc_extract_urg}}
</div>
{{/if}}