{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function () {
  PairEffect.initGroup("ChapEffect", { 
    bStoreInCookie: false,
    sEffect: "appear"
  });
});


function modifClass(){
  var oForm = document.selClass;
  var url = new Url;
  url.setModuleTab("dPccam", "vw_idx_favoris");
  url.addParam("class", oForm.filter_class.value); 
  url.redirect();
}

</script>


<table class="bookCode">
  <tr>
    <th style="text-align: center;" colspan="2">
      Codes Favoris
    </th>
    <td>
    <form name="selClass" action="?">
    {{mb_field object=$favoris field="filter_class" selected=$favoris->object_class defaultOption="&mdash; Tous" onchange="modifClass()"}}
    </form>
    </td>
  </tr>
  
  {{foreach from=$fusion item=curr_chap key=key_chap}}
  <tr id="chap{{$key_chap}}-trigger">
    <th colspan="4">
      {{$curr_chap.nom}}
    </th>
  </tr>
  <tbody>
  {{foreach from=$curr_chap.codes item=curr_code key=key_code name="fusion"}}
 
  {{if $smarty.foreach.fusion.index % 3 == 0}}
  <tr>
  {{/if}}
    <td style="background-color: #{{$curr_code->couleur}};">
      <strong>
        {{if $curr_code->occ==0}}
      <span style="float:right">Favoris</span>
      {{else}}
      <span style="float:right">{{$curr_code->occ}} acte(s)</span>
      {{/if}}
      <a href="?m={{$m}}&amp;tab=vw_full_code&amp;codeacte={{$curr_code->code}}">{{$curr_code->code}}</a>
       
      <span style="float:right">
      {{tr}}CFavoriCCAM.filter_class.{{$curr_code->class}}{{/tr}}
      
      </span>
      </strong>
      <br /><br />

      {{$curr_code->libelleLong}}
      {{if $curr_code->favoris_id}}
      {{if $can->edit}}
      <br />
      <form name="FavorisDel-{{$curr_code->favoris_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="dosql" value="do_favoris_aed" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="favoris_id" value="{{$curr_code->favoris_id}}" />
      <button class="trash" type="submit" name="btnFuseAction">
        Retirer de mes favoris
      </button>
	  </form>
	  {{/if}}
	  {{/if}}
    </td>
  {{if $smarty.foreach.fusion.index % 3 == 4}}
  </tr>
  {{/if}}
  
  {{/foreach}}
  </tbody>
  {{/foreach}}
</table>