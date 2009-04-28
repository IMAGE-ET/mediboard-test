{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="bookCode">
  <tr>
    <th colspan="4">
      <form action="?" name="selection" method="get">
      {{include file="inc_select_lang.tpl"}}

      <input type="hidden" name="m" value="dPcim10" />
      <input type="hidden" name="tab" value="vw_idx_favoris" />
      Codes favoris
      </form>
    </th>
  </tr>
  
  {{foreach from=$fusionCim item=curr_code key=curr_key name="fusion"}}
  {{if $smarty.foreach.fusion.index % 3 == 0}}
  <tr>
  {{/if}}
  
    <td>
    <strong>
        {{if $curr_code->occ==0}}
      <span style="float:right">Favoris</span>
      {{else}}
      <span style="float:right">{{$curr_code->occ}} acte(s)</span>
      {{/if}}
      
        <a href="?m={{$m}}&amp;tab=vw_full_code&amp;code={{$curr_code->code}}">{{$curr_code->code}}</a>
      </strong>
      <br />

      {{$curr_code->libelle}}
      {{if $can->edit}}
      <br />

      <form name="delFavoris-{{$curr_key}}" action="?m={{$m}}" method="post">
      
      <input type="hidden" name="dosql" value="do_favoris_aed" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="favoris_id" value="{{$curr_code->_favoris_id}}" />
    {{if $curr_code->_favoris_id}}
	  <button class="trash" type="submit" name="btnFuseAction">
	  	Retirer de mes favoris
	  </button>
	  {{/if}}
	  </form>
	  {{/if}}
    </td>
  {{if $smarty.foreach.fusion.index % 3 == 4}}
  </tr>
  {{/if}}
  {{/foreach}}
</table>