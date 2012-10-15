{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create("tabs_correspondants", true);
    var url = new Url("dPpatients", "vw_medecins");
    {{if $medecin_id}}
      url.addParam("medecin_id", "{{$medecin_id}}");
    {{/if}}
    {{if $medecin_nom}}
      url.addParam("medecin_nom", "{{$medecin_nom}}");
    {{/if}}
    {{if $medecin_prenom}}
      url.addParam("medecin_prenom", "{{$medecin_prenom}}");
    {{/if}}
    {{if $medecin_cp}}
      url.addParam("medecin_cp", "{{$medecin_cp}}");
    {{/if}}
    {{if $medecin_type}}
      url.addParam("medecin_type", "{{$medecin_type}}");
    {{/if}}
    url.addParam("order_way", "{{$order_way}}");
    url.addParam("order_col", "{{$order_col}}");
    url.requestUpdate("medicaux");
  });
</script>

{{mb_script module=patients script=correspondant}}

<ul id="tabs_correspondants" class="control_tabs">
  <li>
    <a href="#medicaux">Médicaux</a>
  </li>
  <li>
    <a href="#autres">Autres</a>
  </li>
</ul>

<hr class="control_tabs" />

<div id="medicaux">
  
</div>

<div id="autres">
  {{mb_include module=patients template=vw_correspondants_modeles}}
</div>
