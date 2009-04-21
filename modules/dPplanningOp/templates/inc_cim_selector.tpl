{{* $Id$
  * @param $fusion array|codes
  *}}

<tr>
{{foreach from=$fusion item=curr_code key=curr_key name=fusion}}
  <td>
    <strong><span style="float:left">{{$curr_code->code}}</span>
    {{if $curr_code->occ==0}}
    <span style="float:right">Favoris</span>
    {{else}}
    <span style="float:right">{{$curr_code->occ}}</span>
    {{/if}}
    </strong><br />
    {{$curr_code->libelle}}
    <br />
    <button class="tick" type="button" onclick="setClose('{{$curr_code->code}}', '{{$type}}' )">
      {{tr}}Select{{/tr}}
    </button>
  </td>  
{{if $smarty.foreach.fusion.index % 3 == 2}}
</tr><tr>
{{/if}}
{{foreachelse}}
   <td><em>Aucun code</em></td>
{{/foreach}}
</tr>
  