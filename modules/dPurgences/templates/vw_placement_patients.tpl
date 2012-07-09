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

<style type="text/css">
div.patient{
  padding: 1px;
  margin: 3px;
  background-color: rgba(255,255,255,0.8);
  width: 92%;
  box-shadow: 0 0 0 1px silver;
}

div.grille table{
  table-layout:fixed; 
  border-spacing: 13px;
  border-collapse:separate;/*A ne pas enlever util pr IE!!*/
}

div.grille td.chambre, div.grille td.pas-de-chambre{
  height:120px;
}

div.grille small{
  float: right;
  margin-top: -9px;
  border-radius: 2px;
  padding: 0 3px;
  text-shadow:  0 0 0 transparent,
              -1px  0  .0px rgba(255,255,255,.7),
               0   1px .0px rgba(255,255,255,.7),
               1px  0  .0px rgba(255,255,255,.7),
               0  -1px .0px rgba(255,255,255,.7);
}

div.ccmu-0{
  border-left: 5px solid rgba(255,255,255,0.8);
}
div.ccmu-1{
  border-left: 5px solid #0F0;
}
div.ccmu-2{
  border-left: 5px solid #9F0;
}
div.ccmu-3{
  border-left: 5px solid #FF0;
}
div.ccmu-4{
  border-left: 5px solid #FFCD00;
}
div.ccmu-5{
  border-left: 5px solid #F60;
}
div.ccmu-D{
  border-left: 5px solid #F00;
}
div.ccmu-P{
  border-left: 5px solid #0F0;
}
</style>

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

<div id="urgences" style="display: none;">
  {{mb_include module=dPurgences template=inc_vw_plan_urgences name_grille="urgence"}}
</div>

<div id="uhcds" style="display: none;">
  {{mb_include module=dPurgences template=inc_vw_plan_urgences name_grille="uhcd"}}
</div>