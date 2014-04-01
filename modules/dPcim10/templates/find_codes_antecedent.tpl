{{if $cim10->_levelsInf|@count}}
  <td>
    <label>
      <input type="radio" name="codecim" value="{{$cim10->code}}"/>
      <strong>{{$cim10->code}}</strong>: <span title="{{$cim10->libelle}}">{{$cim10->libelle|truncate:40}}</span>
    </label><br/>
    {{foreach from=$cim10->_levelsInf item=curr_level}}
      {{if $curr_level->sid != 0}}
        <label>
          <input type="radio" name="codecim" value="{{$curr_level->code}}"/>
          <strong>{{$curr_level->code}}</strong>: <span title="{{$curr_level->libelle}}">{{$curr_level->libelle|truncate:40}}</span>
        </label><br/>
      {{/if}}
    {{/foreach}}
  </td>
{{/if}}