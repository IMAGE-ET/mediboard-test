<ul>
  {{foreach from=$correspondants item=corresp}}
    <li>
      <strong class="newcode" data-id="{{$corresp->_id}}">{{$corresp->nom}}</strong>
      <br />
      <small>{{$corresp->adresse}} {{$corresp->cp}} {{$corresp->ville}}</small>
      {{if $corresp->date_debut && $corresp->date_fin}}
        <br />
        <small>
          Du {{$corresp->date_debut|date_format:"%d/%m/%Y"}} au {{$corresp->date_fin|date_format:"%d/%m/%Y"}}
        </small>
      {{/if}}
    </li>
  {{/foreach}}
</ul>