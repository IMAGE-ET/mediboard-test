{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
var graphs = {{$graphs|@json}};
Main.add(function(){
	graphs.each(function(g, i){
		Flotr.draw($('graph-'+i), g.series, g.options);
	});
});
</script>

<form name="hospitalisation" action="?" method="get" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="dPstats" />

<table class="main form">
  <tr>
    <th>{{mb_label object=$filter field="_date_min_stat"}}</th>
    <td class="date">{{mb_field object=$filter field="_date_min_stat" form="hospitalisation" canNull="false" register=true}} </td>

    <th>{{mb_label object=$filter field="_service"}}</th>
    <td>
      <select name="service_id">
        <option value="0">&mdash; Tous les services</option>
        {{foreach from=$listServices item=curr_service}}
        <option value="{{$curr_service->service_id}}" {{if $curr_service->service_id == $filter->_service}}selected="selected"{{/if}}>
          {{$curr_service->nom}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field="_date_max_stat"}}</th>
    <td class="date">{{mb_field object=$filter field="_date_max_stat" form="hospitalisation" canNull="false" register=true}} </td>

    <th>{{mb_label object=$filter field="praticien_id"}}</th>
    <td>
      <select name="prat_id">
        <option value="0">&mdash; Tous les praticiens</option>
        {{foreach from=$listPrats item=curr_prat}}
        <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $filter->praticien_id}}selected="selected"{{/if}}>
          {{$curr_prat->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field="type"}}</th>
    <td>
      <select name="type">
        <option value="">&mdash; Tous les types d'hospi</option>
        <option value="1" {{if $filter->type == "1"}}selected="selected"{{/if}}>Hospi complètes + ambu</option>
        {{foreach from=$filter->_specs.type->_locales key=key_hospi item=curr_hospi}}
        <option value="{{$key_hospi}}" {{if $key_hospi == $filter->type}}selected="selected"{{/if}}>
          {{$curr_hospi}}
        </option>
        {{/foreach}}
      </select>
    </td>
   
    <th>{{mb_label object=$filter field="_specialite"}}</th>
    <td>
      <select name="discipline_id">
        <option value="0">&mdash; Toutes les spécialités</option>
        {{foreach from=$listDisciplines item=curr_disc}}
        <option value="{{$curr_disc->discipline_id}}" {{if $curr_disc->discipline_id == $filter->_specialite }}selected="selected"{{/if}}>
          {{$curr_disc->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th><label for="type_data" title="Type de données prises en compte">Type de données</th>
    <td>
      <select name="type_data">
        <option value="prevue" {{if $type_data == "prevue"}}selected="selected"{{/if}}>Prévues</option>
        <option value="reelle" {{if $type_data == "reelle"}}selected="selected"{{/if}}>Réelles</option>
      </select>
    </td>
    <th />
    <td />
  </tr>

  <tr>
    <td colspan="4" class="button"><button type="submit" class="search">Afficher</button></td>
  </tr>
</table>

</form>

<table class="tbl">
  <tr>
    <th colspan="2">Qualité de l'information</th>
  </tr>
  <tr>
    <td style="text-align: right;">
      <label title="Nombre total de séjours disponibles selon les filtres utilisés">Séjours disponibles</label>
    </td>
    <td style="width: 100%;">{{$qualite.total}} séjours</td>
  </tr>
  <tr>
    <td style="text-align: right;">
      <label title="Les séjours non placés n'apparaitront pas dans les graphiques 'par service'">Séjours comportant un placement dans un lit</label>
    </td>
    <td>{{$qualite.places.total}} séjours ({{$qualite.places.pct|string_format:"%.2f"}} %)</td>
  </tr>
  <tr>
    <td style="text-align: right;">
      <label title="Ce facteur sera pris en compte selon le type de données choisi">Séjours comportant une entrée et une sortie réelle</label>
    </td>
    <td>{{$qualite.reels.total}} séjours ({{$qualite.reels.pct|string_format:"%.2f"}} %)</td>
  </tr>
</table>

{{foreach from=$graphs item=graph key=key}}
	<div style="width: 480px; height: 350px; float: left; margin: 1em;" id="graph-{{$key}}"></div>
{{/foreach}}