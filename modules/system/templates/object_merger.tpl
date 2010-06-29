{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  if (window.opener && window.opener.onMergeComplete) {
    window.opener.onMergeComplete();
  }
} );
</script>

{{if !$dialog}}
  {{mb_include module=system template=inc_form_merger}}
{{/if}}

{{if $result}}

{{mb_include_script module=system script=mb_object}}
{{mb_include_script module=system script=object_merger}}

<h2>Fusion de {{tr}}{{$result->_class_name}}{{/tr}}</h2>

{{if $checkMerge}}
<div class="big-warning">
  <p>
  	La fusion de ces deux objets <strong>n'est pas possible</strong> à cause des problèmes suivants :<br />
    {{$checkMerge}}
	</p>
  Veuillez corriger ces problèmes avant toute fusion.
</div>
{{/if}}

{{if $alternative_mode}}
  <div class="small-info">
    Vous êtes en mode alternatif, vous ne pourrez fusionner que deux objets au maximum.
  </div>
{{/if}}

<table>
  <tr>
    <th>Afficher les champs</th>
    <td>
      <label>
        <input type="checkbox" onclick="$$('tr.duplicate').invoke('setVisible', $V(this));" />
        avec des valeurs identiques
        <strong>({{$counts.duplicate}} valeurs)</strong>
      </label>
      <label>
        <input type="checkbox" onclick="$$('tr.unique').invoke('setVisible', $V(this));" />
        avec une valeur unique
        <em>({{$counts.unique}} valeurs)</em>
      </label>
      <label>
        <input type="checkbox" onclick="$$('tr.none').invoke('setVisible', $V(this));" />
        sans valeurs
        <em>({{$counts.none}} valeurs)</em>
      </label>
    </td>
  </tr>
</table>

<form name="form-merge" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}" method="post" onsubmit="{{if $checkMerge}}false{{else}}return checkForm(this){{/if}}">
  <input type="hidden" name="dosql" value="do_object_merge" />
  <input type="hidden" name="m" value="system" />
  {{if $dialog}}
    <input type="hidden" name="postRedirect" value="m=system&amp;{{$actionType}}=object_merger&amp;dialog={{$dialog}}" />
  {{/if}}
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="fast" value="0" />
  {{foreach from=$objects item=object name=object}}
  <input type="hidden" name="_merging[{{$object->_id}}]" value="{{$object->_id}}" />
  <input type="hidden" name="_objects_id[{{$smarty.foreach.object.index}}]" value="{{$object->_id}}" />
  {{/foreach}}
  <input type="hidden" name="_objects_class" value="{{$result->_class_name}}" />
  
  {{math equation="100/(count+1)" count=$objects|@count assign=width}}
  <table class="form merger">
    <tr>
      <th class="category" style="width: 1%;">
      </th>
      <th class="category" style="width: {{$width}}%;">Résultat</th>

      {{foreach from=$objects item=object name=object}}
      <th class="category" style="width: {{$width}}%;">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}')">
					{{tr}}{{$object->_class_name}}{{/tr}} 
					{{$smarty.foreach.object.iteration}}
					<br/>
          {{$object}}
				</span>

        {{if $alternative_mode}}
				<br />
        <label style="font-weight: normal;">
          <input type="radio" name="_base_object_id" value="{{$object->_id}}" {{if $smarty.foreach.object.first}}checked="checked"{{/if}} />
          Utiliser comme base [#{{$object->_id}}]
        </label>
        {{/if}} 
      </th>
      {{/foreach}}
    </tr>
    
    {{foreach from=$result->_specs item=spec name=spec}}
      {{if $spec->fieldName != $result->_spec->key && ($spec->fieldName|substr:0:1 != '_' || $spec->reported)}}
        {{mb_include module=system template=inc_merge_field field=$spec->fieldName}}
      {{/if}}
    {{/foreach}}

	  <tr>
	  	<td colspan="100" class="button">
	  		<script type="text/javascript">var objects_id = {{$objects_id|@json}};</script>
        <button type="button" class="search" onclick="MbObject.viewBackRefs('{{$result->_class_name}}', objects_id);">
          {{tr}}CMbObject-merge-moreinfo{{/tr}}
        </button>
      </td>
		</tr>

		<tr>
      <td colspan="100" class="text">
			  {{if !$dialog}}
				<div class="big-warning">
	    	 Vous êtes sur le point d'effectuer une fusion d'objets.
				 <br />
  				 <strong>Cette opération est irréversible, il est donc impératif d'utiliser cette fonction avec une extrême prudence !</strong>
  				 <br />
				
				 {{if $alternative_mode}}
				 La<strong>procédure alternative est sélectionnée</strong>, elle limite la fusion à 2 objets et se déroule en trois phases :
				 <ol>
				   <li>Modification d'un des deux objets avec les propriétés choisies ci-dessus</li>
				   <li>Transfert des relations depuis l'autre objet</li>
				   <li>Suppression de l'autre objet</li>
				 </ol>
				 {{else}}
				 La <strong>procédure normale</strong> de fusion se passe en trois phases :
				 <ol>
				   <li>Création d'un nouvel objet, avec les propriétés choisies ci-dessus</li>
				   <li>Transfert des relations depuis les anciens objets vers le nouveau</li>
				   <li>Suppression des anciens objets</li>
				 </ol>
         {{/if}}
       </div>
			 {{/if}}
			
       <div id="confirm-0" style="display: none; text-align: left;">
         Vous êtes sur le point d'éffectuer une <strong>fusion standard</strong>. 
				 <br />Ce processus :
         <ul>
           <li>effectue des vérifications d'intégrité, au risque d'échouer dans certaines circonstances</li>
           <li>journalise tous les transferts d'objet</li>
           <li>peut être lent, si le nombre d'objet liés est important</li>
         </ul>
         <br/>Voulez-vous <strong>confirmer cette action</strong> ?
			 </div>

       <div id="confirm-1" style="display: none; text-align: left;">
         Vous êtes sur le point d'éffectuer une <strong>fusion de masse</strong>. 
         <br />Ce processus :
         <ul>
	         <li>n'effectue aucune vérification d'intégrité</li>
	         <li>ne journalise que la création du nouvel objet et l'opération de fusion</li>
	         <li>est très rapide</li>
    	   </ul> 
         <br/>Voulez-vous <strong>confirmer cette action</strong> ?
    	 </div>
			 
      </td>
    </tr>

    <tr>
	  	<td colspan="100" class="button">
		    <button type="submit" class="merge" onclick="return ObjectMerger.confirm('0')">
		      {{tr}}Merge{{/tr}}
		    </button>
				{{if $modules.system->_can->admin}}
		    <button type="submit" class="merge" onclick="return ObjectMerger.confirm('1');">
		      {{tr}}Merge{{/tr}} {{tr}}massively{{/tr}}
		    </button>
				{{/if}}
	    </td>
	  </tr>
  </table>
</form>

{{else}}
<div class="small-info">
  Veuillez choisir des objets existants à fusionner.
</div>
{{/if}}