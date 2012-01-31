{{include file=CMbObject_view.tpl}}

{{assign var=sejour value=$object}}

<script type="text/javascript">
  editPrestations = function (sejour_id) {
    var url = new Url("dPplanningOp", "ajax_vw_prestations");
    url.addParam("sejour_id", sejour_id);
    url.requestModal(800, 700);
  }
</script>

<table class="tbl tooltip">
  <tr>
    <th class="category {{if $sejour->sortie_reelle}}arretee{{/if}}" colspan="4">
      {{tr}}CSejour-_etat.{{$sejour->_etat}}{{/tr}}
      {{mb_include module=dPplanningOp template=inc_vw_numdos nda=$sejour->_NDA _doss_id=$sejour->_id}}
    </th>
  </tr>
  
  {{if $sejour->annule == 1}}
  <tr>
    <th class="category cancelled" colspan="4">
    {{tr}}CSejour-annule{{/tr}}
    </th>
  </tr>
  {{/if}}
  
  <tr>
    <td class="button">
      {{mb_script module=dPplanningOp script=sejour ajax=true}}

      {{if $can->edit}}
			<button type="button" class="edit" onclick="Sejour.edit('{{$sejour->_id}}');">
				{{tr}}Modify{{/tr}}
			</button>
      <button type="button" class="search" onclick="editPrestations('{{$sejour->_id}}')">Prestations</button>
      {{/if}}
      
      {{if $sejour->type != "urg" && @$modules.dPadmissions->_can->read}}
			<button type="button" class="search" onclick="Sejour.admission('{{$sejour->_date_entree_prevue}}');">
				{{tr}}Admission{{/tr}}
			</button>
      {{/if}}
			
			{{if $sejour->type != "urg" && $sejour->type != "ssr" && @$modules.soins->_can->read}}
      <button type="button" class="search" onclick="Sejour.showDossierSoins('{{$sejour->_id}}')">
        {{tr}}module-soins-court{{/tr}}
      </button>
      {{/if}}

      {{if $sejour->type == "ssr" && @$modules.ssr->_can->read}}
      <button type="button" class="search" onclick="Sejour.showSSR('{{$sejour->_id}}');">
        {{tr}}module-ssr-long{{/tr}}
      </button>
      {{/if}}

      {{if $sejour->type == "urg" && @$modules.dPurgences->_can->read}}
      <button type="button" class="search" onclick="Sejour.showUrgences('{{$sejour->_id}}');">
        {{tr}}module-dPurgences-long{{/tr}}
      </button>
      {{/if}}
	  
      {{if @$modules.brancardage->_can->read}}
        {{mb_script module=brancardage script=creation_brancardage ajax=true}}
        <button type="button" class="edit" onclick="CreationBrancard.edit('{{$sejour->_id}}');" style="width:80px;">
        {{tr}}module-Brancardage-long{{/tr}}
        </button>
      {{/if}}
			
    </td>
  </tr>
</table>

<table class="tbl tooltip">
  {{mb_include module=dPcabinet template=inc_list_actes_ccam subject=$sejour vue=view}}
</table>
