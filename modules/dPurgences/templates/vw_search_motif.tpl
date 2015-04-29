<form name="searchMotif" action="#" method="get" onsubmit="return Motif.searchMotif();" class="prepared">
  <table class="form">
    <tr>
      <th colspan="2" class="title">Recherche de motif</th>
    </tr>
    <tr>
      <th>{{tr}}Search{{/tr}}</th>
      <td><input type="text" name="search" value="{{$search}}"/></td>
    </tr>

    <tr>
      <th>{{tr}}CChapitreMotif-nom{{/tr}}</th>
      <td>
        <select name="chapitre_id">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$chapitres item=chapitre}}
            <option value="{{$chapitre->_id}}" {{if $chapitre_id == $chapitre->_id || $chapitre_id == $chapitre->_id}}selected="selected"{{/if}}>
              {{$chapitre->_view}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>{{tr}}CChapitreMotif-see_hors_ccmu{{/tr}}</th>
      <td>
        <input name="see_hors_ccmu" type="hidden" value="{{$see_hors_ccmu}}"/>
        <input name="_see_hors_ccmu" type="checkbox" value="{{$see_hors_ccmu}}" {{if $see_hors_ccmu}}checked="checked" {{/if}} onclick="$V(this.form.see_hors_ccmu, this.checked?1:0)" />
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="button" class="search" onclick="Motif.searchMotif();">{{tr}}Search{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<div id="reload_search_motif">
  {{mb_include module=urgences template=vw_list_motifs chapitres=$chapitres_search readonly=true}}
</div>

<form name="choiceMotifRPU" action="#" method="post">
  <input type="hidden" name="m" value="dPurgences" />
  <input type="hidden" name="dosql" value="do_rpu_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
  <input type="hidden" name="code_diag" value="" />
</form>