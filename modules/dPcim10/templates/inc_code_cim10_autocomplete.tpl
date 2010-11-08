<ul style="text-align: left;">
  {{foreach from=$codes item=_code}}
    <li>
      <span class="code">{{$_code.code}}</span>
      <br/>
      <div style="margin-left: 15px; color: #888">
        {{$_code.text|spancate:40}}
      </div>
    </li>
  {{/foreach}}
</ul>
