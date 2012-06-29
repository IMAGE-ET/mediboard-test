{{* $Id: configure.tpl 7993 2010-02-03 16:55:27Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7993 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<script type="text/javascript">
Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#CBilanSSR">{{tr}}CBilanSSR{{/tr}}</a></li>
  <li><a href="#CCdARRObject">{{tr}}CCdARRObject{{/tr}}</a></li>
  <li><a href="#CReplacement">{{tr}}CReplacement{{/tr}}</a></li>
  <li><a href="#gui">{{tr}}GUI{{/tr}}</a></li>
  <li><a href="#offline">{{tr}}Offline{{/tr}}</a></li>
  <li><a href="#CPrescription">{{tr}}CPrescription{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="CBilanSSR" style="display: none;">
  {{mb_include template=CBilanSSR_configure}}
</div>

<div id="CCdARRObject" style="display: none;">
  {{mb_include template=CCdARRObject_configure}}
</div>

<div id="CReplacement" style="display: none;">
  {{mb_include template=CReplacement_configure}}
</div>

<div id="gui" style="display: none;">
  {{mb_include template=inc_configure_gui}}
</div>

<div id="offline" style="display: none;">
  {{mb_include template=inc_configure_offline}}
</div>

<div id="CPrescription" style="display: none">
  {{mb_include template=CPrescription_configure}}
</div>