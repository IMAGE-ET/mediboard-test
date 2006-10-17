    <table class="tbl">
      <tr>
        <th class="text">Fréquences</th>
        {{foreach from=$bilan key=frequence item=pertes}}
          <th>{{$frequence}}</th>
        {{/foreach}}
      </tr>
      <tr>
        <th />
        <th colspan="8">Conduction aérienne</th>
      </tr>
      <tr class="moyenne">
        <th class="text">
          Moyenne droite
        </th>
        <td colspan="2" />
        <td class="aerien" colspan="4">{{$exam_audio->_moyenne_droite_aerien}}dB</td>
        <td colspan="2" />
      </tr>
      <tr class="moyenne">
        <th class="text">
          Moyenne gauche
        </th>
        <td colspan="2" />
        <td class="aerien" colspan="4">{{$exam_audio->_moyenne_gauche_aerien}}dB</td>
        <td colspan="2" />
      </tr>
      <tr>
        <th class="text">
          Comparaison<br />
          (droite / gauche)
        </th>
        {{foreach from=$bilan item=pertes}}
        <td>
          {{$pertes.aerien.droite}}dB / {{$pertes.aerien.gauche}}dB<br />
          {{assign var="delta" value=$pertes.aerien.delta}}
          {{if $delta lt -20}}&lt;&lt;
          {{elseif $delta lt 0}}&lt;=
          {{elseif $delta eq 0}}==
          {{elseif $delta lt 20}}=&gt;
          {{else}}&gt;&gt;
          {{/if}}
        </td>
        {{/foreach}}
      </tr>
      <tr>
        <th />
        <th colspan="8">Conduction osseuse</th>
      </tr>
      <tr class="moyenne">
        <th class="text">
          Moyenne droite
        </th>
        <td colspan="2" />
        <td class="osseux" colspan="4">{{$exam_audio->_moyenne_droite_osseux}}dB</td>
        <td colspan="2" />
      </tr>
      <tr class="moyenne">
        <th class="text">
          Moyenne gauche
        </th>
        <td colspan="2" />
        <td class="osseux" colspan="4">{{$exam_audio->_moyenne_gauche_osseux}}dB</td>
        <td colspan="2" />
      </tr>
      <tr>
        <th class="text">
          Comparaison<br />
          (droite / gauche)
        </th>
        {{foreach from=$bilan item=pertes}}
        <td>
          {{$pertes.osseux.droite}}dB / {{$pertes.osseux.gauche}}dB<br />
          {{assign var="delta" value=$pertes.osseux.delta}}
          {{if $delta lt -20}}&lt;&lt;
          {{elseif $delta lt 0}}&lt;=
          {{elseif $delta eq 0}}==
          {{elseif $delta lt 20}}=&gt;
          {{else}}&gt;&gt;
          {{/if}}
        </td>
        {{/foreach}}
      </tr>
    </table>