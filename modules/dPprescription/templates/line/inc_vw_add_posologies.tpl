{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add( function(){
  prepareForm('addPrise{{$type}}{{$line->_id}}');
} );

</script>

{{assign var=line_id value=$line->_id}}
<div style="margin-top: 5px; margin-bottom: -14px;">
  <form name="ChoixPrise-{{$line->_id}}" action="" method="post" onsubmit="return false">
	  <input name="typePrise" type="radio" value="moment{{$type}}"  onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" checked="checked" /><label for="typePrise_moment{{$type}}"> Moment</label>
	  <input name="typePrise" type="radio" value="foisPar{{$type}}" onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" /><label for="typePrise_foisPar{{$type}}"> x fois par y</label>
	  <input name="typePrise" type="radio" value="tousLes{{$type}}" onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" /><label for="typePrise_tousLes{{$type}}"> Tous les x y</label>
	  {{if $line->_protocole}}
	  <input name="typePrise" type="radio" value="decalage_intervention{{$type}}"  onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" /><label for="typePrise_decalage_intervention{{$type}}"> I + x heures</label>
	  {{/if}}
	</form>
</div>
<br />

<form name="addPrise{{$type}}{{$line->_id}}" action="?" method="post" style="display: none;" onsubmit="testPharma({{$line->_id}}); return onSubmitPrise(this,'{{$typeDate}}');">
  <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prise_posologie_id" value="" />
  <input type="hidden" name="object_id" value="{{$line->_id}}" />
  <input type="hidden" name="object_class" value="{{$line->_class_name}}" />
  
  {{mb_field object=$prise_posologie field=quantite size=3 increment=1 min=1 form=addPrise$type$line_id}}
  {{if $line->_class_name == "CPrescriptionLineMedicament" && $type != "mode_grille"}}
  <select name="unite_prise" style="width: 75px;">
    {{foreach from=$line->_unites_prise item=_unite}}
      <option value="{{$_unite}}">{{$_unite}}</option>
    {{/foreach}}
  </select>
  {{/if}}
  {{if $line->_class_name == "CPrescriptionLineElement"}}
    {{$line->_unite_prise}}
  {{/if}}
  
  <!-- Emplacement du formulaire de prises pour l'affichage dans le mode Tous Les -->
  <span id="tous_les_{{$type}}_{{$line->_id}}"></span>
  
  <span id="moment{{$type}}{{$line->_id}}" style="display: none; clear: both;">
    {{if $type != "mode_grille"}}
	  <input type="checkbox" name="matin" /><label for="matin"> Matin</label>
	  <input type="checkbox" name="midi" /><label for="midi"> Midi</label>
	  <input type="checkbox" name="soir" /><label for="soir"> Soir</label>
	  
    <input type="checkbox" name="_urgent" onchange="changeUrgence(this.form, this.checked);" /><label for="_urgent"> Urg.</label>
    
	  
	  
	  {{/if}}
  </span>
  
  
  
	<select name="moment_unitaire_id" style="width: 75px;" onclick="completeSelect(this,'{{$line->_id}}','{{$type}}');">
	  <option value="">&mdash; Moment</option>
	</select>
  
  <span id="foisPar{{$type}}{{$line->_id}}" style="display: none;">
    {{mb_field object=$prise_posologie field=nb_fois size=3 increment=1 min=1 form=addPrise$type$line_id}} fois par 
    {{mb_field object=$prise_posologie field=unite_fois}}
  </span>
  
  <span id="tousLes{{$type}}{{$line->_id}}" style="display: none;">
    <br />tous les
    {{mb_field object=$prise_posologie field=nb_tous_les size=3 increment=1 min=1 form=addPrise$type$line_id}}          
    {{mb_field object=$prise_posologie field=unite_tous_les}}
    (J+{{mb_field object=$prise_posologie field=decalage_prise size=1 increment=1 min="0" form=addPrise$type$line_id}})
  </span>
  
  {{if $line->_protocole}}
  <span id="decalage_intervention{{$type}}{{$line->_id}}" style="display: none;">
  à I {{mb_field object=$prise_posologie showPlus="1" field=decalage_intervention size=3 increment=1 form=addPrise$type$line_id}} heures
  </span>
  {{/if}}
  
  {{if $line->_id}}
    <button type="button" class="add notext" onclick="this.form.onsubmit(); refreshCheckbox(this.form);">{{tr}}Save{{/tr}}</button>
  {{/if}}
  
  <span id="moment_{{$type}}_{{$line->_id}}"></span>
</form>

<script type="text/javascript">
  var oFormChoixPrise = document.forms['ChoixPrise-{{$line->_id}}']
  prepareForm(oFormChoixPrise);
  
  // Affichage du type de posologie Moment par defaut
  $('ChoixPrise-{{$line->_id}}_typePrise_moment{{$type}}').onclick();
</script>