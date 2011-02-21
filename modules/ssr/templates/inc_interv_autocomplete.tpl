<ul style="text-align: left;">
  {{foreach from=$mediusers item=_mediuser}}
    <li>
      <span class="interv">{{$_mediuser->_view}}</span>
    </li>
  {{/foreach}}
</ul>