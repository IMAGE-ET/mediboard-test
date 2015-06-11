{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPsante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license OXOL, see http://www.mediboard.org/public/OXOL
*}}

<script type="text/javascript">
  Main.add(function () {
    var tabs = Control.Tabs.create('tabs-configure', true);
    if (tabs.activeLink.key == "CConfigEtab") {
      Configuration.edit('dPsante400', 'CGroups', $('CConfigEtab'));
    }
  });
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#CIdSante400">{{tr}}CIdSante400{{/tr}}</a></li>
  <li><a href="#CIncrementer">{{tr}}CIncrementer{{/tr}}</a></li>
  <li onmousedown="Configuration.edit('dPsante400', 'CGroups', $('CConfigEtab'))">
    <a href="#CConfigEtab">{{tr}}CConfigEtab{{/tr}}</a>
  </li>
  <li><a href="#mouvements">Mouvements</a></li>
</ul>

<hr class="control_tabs" />

<div id="CIdSante400" style="display: none;">
  {{mb_include template=CIdSante400_configure}}
</div>

<div id="CIncrementer" style="display: none;">
  {{mb_include template=CIncrementer_configure}}
</div>

<div id="CConfigEtab" style="display: none">
</div>

<div id="mouvements" style="display: none;">
  {{mb_include template=mouvements_configure}}
</div>