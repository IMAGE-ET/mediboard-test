<table class="bookCode">
  <tr>
    <th colspan="4">
      <form action="index.php" name="selection" method="get">

      <select name="lang" style="float:right;" onchange="this.form.submit()">
        <option value="{{$cim10|const:'LANG_FR'}}" {{if $lang == $cim10|const:'LANG_FR'}}selected="selected"{{/if}}>
          Français
        </option>
        <option value="{{$cim10|const:'LANG_EN'}}" {{if $lang == $cim10|const:'LANG_EN'}}selected="selected"{{/if}}>
          English
        </option>
        <option value="{{$cim10|const:'LANG_DE'}}" {{if $lang == $cim10|const:'LANG_DE'}}selected="selected"{{/if}}>
          Deutsch
        </option>
      </select>

      <input type="hidden" name="m" value="dPcim10" />
      <input type="hidden" name="tab" value="vw_idx_favoris" />
      Codes favoris
      </form>
    </th>
  </tr>
  
  {{foreach from=$codes item=curr_code key=curr_key}}
  {{if $curr_key is div by 4}}
  <tr>
  {{/if}}
    <td>
      <strong>
        <a href="?m={{$m}}&amp;tab=vw_full_code&amp;code={{$curr_code->code}}">{{$curr_code->code}}</a>
      </strong>
      <br />

      {{$curr_code->libelle}}
      {{if $canEdit}}
      <br />

      <form name="delFavoris" action="?m={{$m}}" method="post">
      
      <input type="hidden" name="dosql" value="do_favoris_aed" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="favoris_id" value="{{$curr_code->_favoris_id}}" />
      
	  <button class="trash" type="submit" name="btnFuseAction">
	  	Retirer de mes favoris
	  </button>
	  
	  </form>
	  {{/if}}
    </td>
  {{if $curr_key+1 is div by 4 or $curr_key+1 == $codes|@count}}
  </tr>
  {{/if}}
  {{/foreach}}
</table>