<table class="tbl" style="text-align: center;">
  <tr>
    <th class="title">Type</th>
    <th class="title" colspan="6">Triggers</th>
    <th class="title" colspan="6">Marques</th>
  </tr>

  <tr>
    <th></th>
    <th colspan="2">Plus ancien</th>
    <th colspan="2">Plus récent</th>
    <th colspan="2">Traitable</th>
    <th colspan="2">Plus ancien</th>
    <th colspan="2">Plus récent</th>
    <th>Purgeable</th>
    <th>Obsolète</th>
  </tr>

  <tr>
    <th class="section"></th>
    <th class="section">numéro</th>
    <th class="section">horodatage</th>
    <th class="section">numéro</th>
    <th class="section">horodatage</th>
    <th class="section">A traiter</th>
    <th class="section">Avec erreur</th>
    <th class="section">numéro</th>
    <th class="section">horodatage</th>
    <th class="section">numéro</th>
    <th class="section">horodatage</th>
    <th class="section">total</th>
    <th class="section">deprecated</th>
  </tr>

  {{foreach from=$report key=_type item=_report}}
  <tr>
    <th>{{$_type}}</th>

    {{assign var=triggers value=$_report.triggers}}
    <td>{{$triggers.oldest->rec}}</td>
    <td>{{$triggers.oldest->when|date_format:$conf.datetime}}</td>
    <td>{{$triggers.latest->rec}}</td>
    <td>{{$triggers.latest->when|date_format:$conf.datetime}}</td>
    <td>{{$triggers.marked.0}}</td>
    <td>{{$triggers.marked.1}}</td>

    {{assign var=marks value=$_report.marks}}
    <td>{{$marks.oldest->trigger_number}}</td>
    <td>{{$marks.oldest->when|date_format:$conf.datetime}}</td>
    <td>{{$marks.latest->trigger_number}}</td>
    <td>{{$marks.latest->when|date_format:$conf.datetime}}</td>
    <td>{{$marks.purgeable}}</td>
    <td>{{$marks.obsolete }}</td>


  </tr>

  {{foreachelse}}
  <tr><td class="empty">No type to report</td></tr>
  {{/foreach}}

</table>

<div id="doBoard" style="height: 4em;">

</div>