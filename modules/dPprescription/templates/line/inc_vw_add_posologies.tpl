{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  // Affichage du type de posologie Moment par defaut
	$('ChoixPrise-{{$line->_id}}_typePrise_moment{{$type}}').onclick();
});
</script>

<strong>
	
<form name="ChoixPrise-{{$line->_id}}" action="?" method="get">
	{{assign var=line_id value=$line->_id}}
	<label title="Moment de la journée">
		<input name="typePrise" type="radio" value="moment{{$type}}"  onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" checked="checked" />
		Moment
	</label>
	<label title="x fois par y"> 
	  <input name="typePrise" type="radio" value="foisPar{{$type}}" onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" />
	  Fréquence
	</label>
	<label title="Tous les x y">
	  <input name="typePrise" type="radio" value="tousLes{{$type}}" onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" />
		Répétition
	</label>
	
	{{if $type != "mode_grille"}}
	<label title="Evenement" style="display : none;">
	  <input name="typePrise" type="radio" value="evenement{{$type}}" onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" />
	  Evènement
	</label>
	{{/if}}
	
	{{if $type != "mode_grille" && $line->_ref_prescription->object_id && $line->_most_used_poso|@count}}
	<label title="Stats">
	  <input name="typePrise" type="radio" value="stats{{$type}}" onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" />
	  Stats
	</label>
	{{/if}}
	  
	{{if $type != "mode_grille" && $line->_protocole && $line->_ref_prescription->type!="externe"}}
	<label>
	  <input name="typePrise" type="radio" value="decalage_intervention{{$type}}"  onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" /> 
	  I + x heures
	</label>
	{{/if}}

</form>

</strong>
<br />

<form name="addPrise{{$type}}{{$line->_id}}" action="?" method="post" style="display: none;" onsubmit="testPharma({{$line->_id}}); return onSubmitPrise(this,'{{$typeDate}}');">
  <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prise_posologie_id" value="" />
  <input type="hidden" name="object_id" value="{{$line->_id}}" />
  <input type="hidden" name="object_class" value="{{$line->_class_name}}" />
  
	<span id="view_quantity_{{$line->_id}}">
  {{mb_field object=$prise_posologie field=quantite size=3 increment=1 min=1 form=addPrise$type$line_id}}
  {{if $line->_class_name == "CPrescriptionLineMedicament" && $type != "mode_grille"}}
  <select name="unite_prise" style="width: 80px;">
    {{foreach from=$line->_unites_prise item=_unite}}
      <option value="{{$_unite}}">{{$_unite}}</option>
    {{/foreach}}
  </select>
  {{/if}}
  {{if $line->_class_name == "CPrescriptionLineElement"}}
    {{$line->_unite_prise}}
  {{/if}}
  </span>
	
  <!-- Emplacement du formulaire de prises pour l'affichage dans le mode Tous Les -->
  <span id="tous_les_{{$type}}_{{$line->_id}}"></span>
  
  <span id="moment{{$type}}{{$line->_id}}" style="display: none; clear: both;">
    {{if $type != "mode_grille"}}
		
		{{foreach from=$conf.dPprescription.CMomentUnitaire.poso_lite key=_moment_unitaire item=_show_moment}}
		  {{if $_show_moment}}
			<input type="checkbox" name="{{$_moment_unitaire}}" class="moment_poso_lite" />
			<label for="{{$_moment_unitaire}}">
				{{tr}}config-dPprescription-CMomentUnitaire-poso_lite-{{$_moment_unitaire}}{{/tr}}
			</label>
			{{/if}}
    {{/foreach}}
	  
    <input type="checkbox" name="_urgent" onchange="changeUrgence(this.form, this.checked);" /><label for="_urgent"> Urg.</label>
	  {{/if}}
  </span>

	<select name="moment_unitaire_id" style="width: 80px;" onmousedown="completeSelect(this,'{{$line->_id}}','{{$type}}');">
	  <!-- Laisser ces &nbsp; a cause de IE qui va elargir la liste quand on clique dessus et elle va passer a la ligne (desolé!) -->
    <option value="">&mdash; Moment &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </option>
	</select>
  
  <span id="foisPar{{$type}}{{$line->_id}}" style="display: none;">
    {{mb_field object=$prise_posologie field=nb_fois size=3 increment=1 min=1 form=addPrise$type$line_id}} fois par jour
		<input type="hidden" name="unite_fois" value="" />  
	</span>
  
  <span id="tousLes{{$type}}{{$line->_id}}" style="display: none;">
    <br />tous les
    {{mb_field object=$prise_posologie field=nb_tous_les size=3 increment=1 min=1 form=addPrise$type$line_id}}          
    {{mb_field object=$prise_posologie field=unite_tous_les}}
    (J+{{mb_field object=$prise_posologie field=decalage_prise size=1 increment=1 min="0" form=addPrise$type$line_id}})
  </span>
  
	 <span id="evenement{{$type}}{{$line->_id}}" style="display: none;">
    {{mb_field object=$prise_posologie field=condition}}
  </span>
	
  {{if $line->_protocole && $line->_ref_prescription->type!="externe"}}
  <span id="decalage_intervention{{$type}}{{$line->_id}}" style="display: none;">
	  à I {{mb_field object=$prise_posologie showPlus="1" field=decalage_intervention size=3 increment=1 form=addPrise$type$line_id}} 
		{{mb_field object=$prise_posologie field=unite_decalage_intervention}}
  </span>
  {{/if}}

  {{if $line->_id}}
    <button id="add_button_{{$line->_id}}" type="button" class="add notext" onclick="this.form.onsubmit(); refreshCheckbox(this.form);">{{tr}}Save{{/tr}}</button>
  {{/if}}
  
  <span id="moment_{{$type}}_{{$line->_id}}"></span>
</form>

<span id="stats{{$type}}{{$line->_id}}" style="display: none;">
  <!-- Selection des posologies statistiques -->
  {{if $type != "mode_grille" && $line->_ref_prescription->object_id}}
    {{include file="../../dPprescription/templates/line/inc_vw_form_select_poso.tpl"}}
  {{/if}}   
</span>