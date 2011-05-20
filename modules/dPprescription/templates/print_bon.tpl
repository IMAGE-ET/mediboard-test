{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=patient value=$prescription->_ref_patient}}
{{assign var=sejour value=$prescription->_ref_object}}

<script type="text/javascript">
toggleAll = function(state) {
  $A(getForm("filtreBons").elements["list_bons[]"]).each(function(elt) {
    elt.checked = state;
  });
}


Main.add( function(){
  var oForm = getForm("filtreBons");
  
  dates = {
    limit: {
      start: "{{$sejour->_entree}}",
      stop: "{{$sejour->_sortie}}"
    },
    spots: []
  };
  Calendar.regField(oForm.debut, dates);
	
	{{if $print}}
	  window.print();
	{{/if}}
  
  // Cochage du bouton radio Tout cocher si tous les bons sont cochés
	if ($A(oForm.elements["list_bons[]"]).all(function(elt) { return elt.checked})) {
    oForm.check_all.checked = true;
	}
});
</script>  
  
<!-- Fermeture du tableau pour faire fonctionner le page-break -->
    </td>
  </tr>
</table>

<style type="text/css">
{{include file=../../dPcompteRendu/css/print.css header=8 footer=0 nodebug=true}}

/* decalage du header pour permettre l'insertion de filtres */
@media screen {
	div.header {
	  border-bottom-width: 1px;
	}
	div.header,
	div.footer {
	  position: relative; 
	  background: #ddd;
	  border: 0px solid #aaa;
	  width: 100%;
	  opacity: 0.9;
	  overflow: hidden;
	}
}

</style>

<form name="filtreBons" method="get" class="not-printable">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="a" value="print_bon" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
  <input type="hidden" name="dialog" value="1" />
	
	<table class="tbl">
	  <tr>
	    <th class="title" colspan="4">
	      Sélection des bons à imprimer
	    </th>
	  </tr> 
		
     <tr>
      <td colspan="2" style="text-align: center;">
        Chapitre
        <select name="sel_chapitre" onchange=this.form.submit();>
          <option value="">&mdash; Tous</option>
          {{foreach from=$chapitres item=_chapitre}}
            <option value="{{$_chapitre}}" {{if $_chapitre == $sel_chapitre}}selected="selected"{{/if}}>{{tr}}CPrescription._chapitres.{{$_chapitre}}{{/tr}}</option>
          {{/foreach}}
        </select>
      </td>
      <td colspan="2" style="text-align: center;">
        Date {{mb_field object=$filter_line field=debut class=notNull onchange=this.form.submit()}}
      </td>
    </tr>
	  <tr>
	    <th>Bon</th>
	    <th>Prescripteur</th>
	    <th>Heure</th>
	    <th><input type="checkbox" onchange="toggleAll(this.checked);" name="check_all" title="{{tr}}CPrescription.check_uncheck{{/tr}}"/></th>
		</tr>
		{{foreach from=$all_bons key=chapitre item=_bons_by_hour key=_chap name=foreach_chap}}
		  {{foreach from=$_bons_by_hour key=hour item=_bons_by_cat name=foreach_hour}}
		    {{foreach from=$_bons_by_cat item=_bons key=name_cat}}         
		      {{foreach from=$_bons key=line_id item=_bon}}
		        {{assign var=line value=$lines.$line_id}}
		        <tr>
		          <td>
		            <strong>{{$line->_view}}</strong>
		          </td>
		          <td>
		            {{mb_value object=$line field="praticien_id"}}
		          </td>
		          <td>
		            {{$hour}}h
		          </td>
		          <td class="narrow">
		          	{{assign var=key_bon value="$line_id-$hour"}}
		            <input type="checkbox" name="list_bons[]" {{if @in_array($key_bon, $list_bons) || !is_array($list_bons) || !$print}}checked="checked"{{/if}} value="{{$key_bon}}" />
		          </td>
		        </tr>               
		      {{/foreach}}
		    {{/foreach}}    
		  {{/foreach}}
		{{/foreach}}
		<tr>
			<td class="button" colspan="4">
				<input type="hidden" name="print" value="0" />
		    <button type="button" class="print" onclick="$V(this.form.print, '1'); this.form.submit();">Imprimer les bons sélectionnés</button>
    	</td>
		</tr>
	</table>
</form>
<br />

{{if $print}}
<div class="header">
  <span style="float: right">
    {{foreach from=$prescription->_ref_object->_ref_affectations item=_affectation}}
		   {{if $prescription->_ref_object->_ref_affectations|@count > 1}}
		   du {{$_affectation->entree|date_format:$conf.date}} au {{$_affectation->sortie|date_format:$conf.date}}
			 {{/if}}
			 <strong>{{$_affectation->_view}}</strong>
			<br />
		{{/foreach}}
		DE: {{$sejour->_entree|date_format:"%d/%m/%Y"}}<br />
    DS: {{$sejour->_sortie|date_format:"%d/%m/%Y"}}<br />
    {{if $sejour->_num_dossier}}
      Séjour [{{$sejour->_num_dossier}}]<br/>
    {{/if}}
    {{if $patient->_IPP}}
      IPP [{{$patient->_IPP}}]
    {{/if}}
  </span>
	
  <div>
    <strong>{{$patient->_view}}</strong>
    Né(e) le {{mb_value object=$patient field=naissance}} - ({{$patient->_age}} ans) - ({{$patient->_ref_constantes_medicales->poids}} kg)
    <br />
    {{assign var=operation value=$sejour->_ref_last_operation}}
    Intervention le {{$operation->_ref_plageop->date|date_format:"%d/%m/%Y"}}
    <strong>(I{{if $operation->_compteur_jour >=0}}+{{/if}}{{$operation->_compteur_jour}})</strong> - côté {{$operation->cote}}<br /><br />
		<strong>{{$operation->libelle}}</strong> 
    <div style="text-align: left">
		{{if !$operation->libelle}}
      {{foreach from=$operation->_ext_codes_ccam item=curr_ext_code}}
        <strong>{{$curr_ext_code->code}}</strong> :
        {{$curr_ext_code->libelleLong}}<br />
        {{/foreach}}
    {{/if}}
		</div>
	</div>
</div>

<div class="footer">
</div>

{{foreach from=$bons key=chapitre item=_bons_by_hour name=foreach_chap}}
	  {{foreach from=$_bons_by_hour key=hour item=_bons_by_cat name=foreach_hour}}
	    <div style="font-size: 11px; font-size: 1.0rem;"
			     class="{{if $smarty.foreach.foreach_chap.last && $smarty.foreach.foreach_hour.last}}bodyWithoutPageBreak{{else}}body{{/if}}">
		    <table class="tbl">
				  <tr>
				    <th colspan="2" class="title">
				    	{{tr}}CPrescription._chapitres.{{$chapitre}}{{/tr}} - Examens demandés pour le {{$debut|date_format:$conf.date}} à {{$hour}} h
						</th>
				  </tr>
				  {{foreach from=$_bons_by_cat item=_bons key=name_cat}}
				  <tr>
				    <th colspan="2" class="text">
				      {{assign var=category value=$categories.$chapitre.$name_cat}}
			        {{$category->nom}}
			        {{if $conf.dPprescription.CCategoryPrescription.show_header && $category->header}}, {{$category->header}}{{/if}}
			        {{if $conf.dPprescription.CCategoryPrescription.show_description && $category->description}}, {{$category->description}}{{/if}}
		        </th>
				  </tr>
			    {{foreach from=$_bons key=line_id item=_bon}}
			      {{assign var=line value=$lines.$line_id}}
			      <tr>
				      <td>
				      	{{$_bon}} {{$line->_unite_prise}} {{$line->_view}}
				      </td>
				      <td style="width: 20%">
				        Prescripteur: {{$line->_ref_praticien->_view}} ({{$line->_ref_praticien->adeli}})
				      </td>
			      </tr>
            {{if $line->commentaire}}
            <tr>
              <td colspan="2" style="text-indent: 1em">
                {{$line->commentaire}}
              </td>
            </tr>
            {{/if}}
						
            {{assign var=line_guid value=$line->_guid}}
            {{foreach from=$ex_objects.$line_guid item=_ex_object}}
			        <tr>
			          <td colspan="2">
			          	<strong>
									  Questionnaire - 
										<span style="text-decoration: underline;">{{$_ex_object->_ref_ex_class->name}}</span>
									</strong>
			            {{mb_include module=forms template=inc_vw_ex_object ex_object=$_ex_object}}
			          </td>
			        </tr>
            {{/foreach}}
			    {{/foreach}}
          
			    {{/foreach}}
		    </table>
	    </div>
	{{/foreach}}
{{/foreach}}
{{/if}}

<!-- re-ouverture du tableau -->
<table>
  <tr>
    <td>