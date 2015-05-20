{{* $Id: vw_idx_rpu.tpl 7671 2009-12-19 08:42:21Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7671 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(function () {
    $('encrypt_rpu').disabled = true;
    $('transmit_rpu').disabled = true;

    {{if array_key_exists('urg', $types)}}
      $('encrypt_urg').disabled = true;
      $('transmit_urg').disabled = true;
    {{/if}}

    {{if array_key_exists('uhcd', $types)}}
    $('encrypt_uhcd').disabled = true;
    $('transmit_uhcd').disabled = true;
    {{/if}}
  });

  Main.add(Control.Tabs.create.curry('tabs-extract', true));
</script>

<ul id="tabs-extract" class="control_tabs">
  {{if array_key_exists('rpu', $types)}}
    <li><a href="#RPU">{{tr}}extract-rpu{{/tr}}</a></li>
  {{/if}}
  {{if array_key_exists('uhcd', $types)}}
    <li><a href="#UHCD">{{tr}}extract-uhcd{{/tr}}</a></li>
  {{/if}}
  {{if array_key_exists('urg', $types)}}
    <li><a href="#URG">{{tr}}extract-urg{{/tr}}</a></li>
  {{/if}}
  {{if array_key_exists('activite', $types)}}
    <li><a href="#ACTIVITE">{{tr}}extract-activite{{/tr}}</a></li>
  {{/if}}
</ul>

<hr class="control_tabs" />

{{if array_key_exists('rpu', $types)}}
<div id="RPU" style="display: none;">
  {{mb_include template=inc_extract type="rpu"}}
</div>
{{/if}}

{{if array_key_exists('uhcd', $types)}}
  <div id="UHCD" style="display: none;">
    {{mb_include template=inc_extract type="uhcd"}}
  </div>
{{/if}}

{{if array_key_exists('urg', $types)}}
<div id="URG" style="display: none;">
  {{mb_include template=inc_extract type="urg"}}
</div>
{{/if}}

{{if array_key_exists('activite', $types)}}
  <div id="ACTIVITE" style="display: none;">
    {{mb_include template=inc_extract type="activite"}}
  </div>
{{/if}}