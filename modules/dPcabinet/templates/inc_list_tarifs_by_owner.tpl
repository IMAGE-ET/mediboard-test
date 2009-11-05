<tr>
  <th>{{mb_title class=CTarif field=description}}</th>
  <th>{{mb_title class=CTarif field=_has_mto}}</th>
  <th>{{mb_title class=CTarif field=secteur1}}</th>
  <th>{{mb_title class=CTarif field=secteur2}}</th>
  <th>{{mb_title class=CTarif field=_somme}}</th>
</tr>

{{foreach from=$tarifs item=_tarif}}
<tr {{if $_tarif->_id == $tarif->_id}}class="selected"{{/if}}>
  <td {{if $_tarif->_precode_ready}}class="checked"{{/if}}>
    <a href="?m={{$m}}&amp;tab={{$tab}}&amp;tarif_id={{$_tarif->_id}}">
    	{{mb_value object=$_tarif field=description}}
    </a>
  </td>
  <td>{{mb_value object=$_tarif field=_has_mto}}</td>
  <td style="text-align: right">{{mb_value object=$_tarif field=secteur1}}</td>
  <td style="text-align: right">{{mb_value object=$_tarif field=secteur2}}</td>
  <td style="text-align: right"><strong>{{mb_value object=$_tarif field=_somme}}</strong></td>
</tr>
{{/foreach}}
