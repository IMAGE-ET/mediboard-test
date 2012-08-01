{{* $Id: vw_placement_patients.tpl $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 11962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=urgences script=drag_patient}}
<script>
Main.add(function () {
  Rafraichissement.start(60);//rafraichissement toutes les minutes
  PairEffect.initGroup("serviceEffect");
  Control.Tabs.create('tabs-urgences', true);
});

Rafraichissement = {  
  init: function() {
    var url = new Url("dPurgences", "vw_placement_patients", "tab");
    url.redirect();
  },
  
  start: function(delay) {
    this.init.delay(delay);
  }
}
</script>

<ul id="tabs-urgences" class="control_tabs">
  <li><a href="#urgences">Urgence</a></li>
  <li><a href="#uhcds">   UHCD   </a></li>
  <li style="width: 20em; text-align: center">
    <script>
    Main.add(function() {
      Calendar.regField(getForm("changeDate").date, null, { noView: true } );
    } );
    </script>
    <strong><big>{{$date|date_format:$conf.longdate}}</big></strong>
    
    <form action="#" name="changeDate" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
    </form>
  </li>
</ul>

<hr class="control_tabs" />

<div id="urgences" style="display: none;" class='vue_topologique'>
  {{mb_include module=dPurgences template=inc_vw_plan_urgences name_grille="urgence"}}
</div>

<div id="uhcds" style="display: none;" class='vue_topologique'>
  {{mb_include module=dPurgences template=inc_vw_plan_urgences name_grille="uhcd"}}
</div>