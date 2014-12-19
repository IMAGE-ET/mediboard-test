{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script>
  Main.add(function() {
    var tabs = Control.Tabs.create('tabs-configure', true);

    var oform = getForm('guess_caesarean');
    Calendar.regField(oform.start);
    Calendar.regField(oform.end);

    reloadListCesar();
  });

  reloadListCesar = function() {
    var form = getForm('guess_caesarean');
    form.onsubmit();
  };
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#CGrossesse">{{tr}}CGrossesse{{/tr}}</a></li>
  <li><a href="#rattrapage_cesarienne">Rattrapage césariennes</a></li>
</ul>

<div id="CGrossesse" style="display: none">
  {{mb_include template=CGrossesse_configure}}
</div>

<div id="rattrapage_cesarienne" style="display: none;">
  <h2>Outil d'aide à la définition de césarienne sur des naissances</h2>
  <form method="get" name="guess_caesarean" onsubmit="return onSubmitFormAjax(this, null, 'result_guess')">
    <input type="hidden" name="m" value="maternite"/>
    <input type="hidden" name="a" value="ajax_list_guess_caesarean"/>

    <input type="text" name="start" value="{{$start}}" style="display: none;"/>
    <input type="text" name="end"   value="{{$end}}" style="display: none;"/>

    <button class="search"></button>
  </form>
  <div id="result_guess"></div>
</div>