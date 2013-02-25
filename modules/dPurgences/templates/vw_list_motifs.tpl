<script>
  Main.add(function () {
    PairEffect.initGroup("serviceEffect");
  });
</script>
<table style="width: 100%;" class="tbl">
  <tr>
    <td colspan="4">
      <button type="button" class="new" onclick="Motif.edit(0)">
        {{tr}}CMotif-title-create{{/tr}}
      </button>
    </td>
  </tr>
  <tr>
    <th class="category">{{mb_title class=CMotif field=nom}}</th>
    <th class="category">{{mb_title class=CMotif field=code_diag}}</th>
    <th class="category">{{mb_title class=CMotif field=degre_min}}</th>
    <th class="category">{{mb_title class=CMotif field=degre_max}}</th>
  </tr>
  {{foreach from=$chapitres item=chapitre}}
    <tr id="{{$chapitre->_guid}}-trigger">
      <td colspan="4">{{$chapitre->nom}}</td>
    </tr>
    <tbody class="serviceEffect" id="{{$chapitre->_guid}}">
      {{foreach from=$chapitre->_ref_motifs item=motif}}
        <tr {{if $chapitre->_id == $chapitre_id}} class="selected" {{/if}} >
          <td>
            <a href="#{{$motif->_guid}}" onclick="Motif.edit('{{$motif->_id}}');">
              {{mb_value object=$motif field=nom}}
            </a>
          </td>
          <td>{{mb_value object=$motif field=code_diag}}</td>
          <td>{{mb_value object=$motif field=degre_min}}</td>
          <td>{{mb_value object=$motif field=degre_max}}</td>
        </tr>
      {{foreachelse}}
        <tr>
          <td class="empty" colspan="4">
            {{tr}}CMotif.none{{/tr}}
          </td>
        </tr>
      {{/foreach}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="4">
        {{tr}}CChapitreMotif.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>