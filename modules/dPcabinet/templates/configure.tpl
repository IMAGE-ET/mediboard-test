{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="cabinet" script="consultation"}}

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-configure', true);
});
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#RDV">{{tr}}RDV-config{{/tr}}</a></li>
  <li><a href="#CConsultation">{{tr}}CConsultation{{/tr}}</a></li>
  <li><a href="#CConsultAnesth">{{tr}}CConsultAnesth{{/tr}}</a></li>
  <li><a href="#CPlageconsult">{{tr}}CPlageconsult{{/tr}}</a></li>
  <li><a href="#CPrescription">{{tr}}CPrescription{{/tr}}</a></li>
  {{if $user->user_username == "admin"}}
    <li><a href="#compta">{{tr}}compta-config{{/tr}}</a></li>
  {{/if}}
  <li><a href="#tag">{{tr}}tag-config{{/tr}}</a></li>
  <li><a href="#actions">Autres actions</a></li>
  <li><a href="#offline">Mode offline</a></li>
</ul>

<hr class="control_tabs" />

<!-- Prise de rendez-vous --> 
<div id="RDV" style="display: none;">
  {{mb_include template=inc_config_rdv}} 
</div>
  
<!-- CConsultation -->
<div id="CConsultation" style="display: none;">
  {{mb_include template=CConsultation_config}}
</div>

<!-- CConsultAnesth -->  
<div id="CConsultAnesth" style="display: none;">
  {{mb_include template=CConsultAnesth_config}}
</div>

<!-- CPlageconsult -->  
<div id="CPlageconsult" style="display: none;">
  {{mb_include template=CPlageconsult_config}}
</div>

<!-- CPrescription -->  
<div id="CPrescription" style="display: none;">
  {{mb_include template=CPrescription_config}}
</div>

{{if $user->user_username == "admin"}}
<div id="compta" style="display: none;">
 {{mb_include template=inc_config_compta}}
</div>
{{/if}}

<div id="tag" style="display: none;">
 {{mb_include template=inc_config_tag}}
</div>

<div id="actions" style="display: none;">
 {{mb_include template=inc_configure_actions}}
</div>

<div id="offline" style="display: none;">
  <table class="main tbl">
    <tr>
      <td>
        <a class="button search" href="?m=dPcabinet&amp;a=offline_programme_consult&amp;dialog=1&amp;_aio=1">
          {{tr}}mod-dPcabinet-tab-offline_programme_consult{{/tr}}
        </a>
        <a class="button search" href="?m=dPcabinet&amp;a=vw_journee&amp;dialog=1&amp;offline=1&amp;_aio=1">
          {{tr}}mod-dPcabinet-tab-vw_journee{{/tr}}
        </a>
      </td>
    </tr>
  </table>
</div>