<h1>Analyse pour {{$module}} &gt; {{$action}}</h1>

<h2>Paramètres</h2>

{{mb_include module=system template=view_info props=$props}}

<h2>Plan de test</h2>

<table class="tbl">
  <tr>
    <th>Sample</th>
    <th>Status</th>
  </tr>


  {{foreach from=$plan item=_parts}}
    <tr>
      <td>
        m=<strong>{{$module}}</strong>
        a=<strong>{{$action}}</strong>
        {{foreach from=$_parts key=_param item=_value}}
          {{$_param}}=<strong><span class="ok">{{$_value}}</span></strong>
        {{/foreach}}
      </td>
    </tr>
  {{/foreach}}

</table>
