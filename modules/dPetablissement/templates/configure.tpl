{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  function refreshConfigClasses() {
    var url = new Url("system", "ajax_config_classes");
    url.addParam("module", "{{$m}}");
    url.requestUpdate("object-config");
  }
   
  Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#CGroup">{{tr}}CGroups{{/tr}}</a></li>
  <li onmousedown="refreshConfigClasses();">
    <a href="#object-config">{{tr}}config-dPetablissement-object-config{{/tr}}</a>
  </li>
  <li>
    <a href="#CEtabExterne-import">Import {{tr}}CEtabExterne{{/tr}}</a>
  </li>
</ul>
<hr class="control_tabs" />

<div id="CGroup" style="display: none;">
  {{mb_include module=etablissement template=CGroup_configure}}
</div>

<div id="object-config" style="display: none;">
  <div class="small-info">{{tr}}config-dPetablissement-object-config-classes{{/tr}}</div>
</div>

<div id="CEtabExterne-import" style="display: none;">
  {{mb_include module=etablissement template=inc_import_etab_externe}}
</div>
