{{*
 * View merge domains EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{if $checkMerge}}
  <div class="big-warning">
    <p>
      La fusion de ces deux objets <strong>n'est pas possible</strong> à cause des problèmes suivants :
      {{foreach from=$checkMerge item=_checkMerge}}
      <ul>
        <li> {{$_checkMerge}}</li>
      </ul>
      {{/foreach}}
    </p>
  </div>
{{else}}
  {{assign var=domain1 value=$domains.0}}
  {{assign var=domain2 value=$domains.1}}
  
  <form name="form-merge" action="?m=eai&amp;dosql=do_domain_merge" method="post" onsubmit="return onSubmitFormAjax(this, 
    { onComplete : function() { Control.Modal.close; Domain.refreshListDomains(); }})">
    <input type="hidden" name="dosql" value="do_domain_merge" />
    <input type="hidden" name="m" value="eai" />
    <input type="hidden" name="domain_1_id" value="{{$domain1->_id}}" />
    <input type="hidden" name="domain_2_id" value="{{$domain2->_id}}" />
    <input type="hidden" name="actor_class" value="" />
          
    <table class="form merger">
      <tr>
        <th class="category"></th>
        <th class="category">{{$domain1->_view}}</th>
        <th class="category">{{$domain2->_view}}</th>
      </tr>
      
      <tr>
        <th>{{mb_label object=$domain1 field="tag"}}</th>
        <td>
          <label>
            <input type="radio" name="tag" value="{{$domain1->tag}}" checked="checked" />
            {{$domain1->tag}}
          </label>  
        </td>
        <td>
          <label>
            <input type="radio" name="tag" value="{{$domain2->tag}}" />
            {{$domain2->tag}}
          </label>  
        </td>
      </tr>
      
      {{if !$domain1->derived_from_idex && !$domain2->derived_from_idex}}
      <tr>
        <th>{{mb_label object=$domain1 field="incrementer_id"}}</th>
        <td>
          <label>
            <input type="radio" name="incrementer_id" value="{{$domain1->incrementer_id}}" checked="checked" 
              {{if $domain1->incrementer_id}}onclick="$V(this.form.actor_id, '')"{{/if}} />
            <span onmouseover="ObjectTooltip.createEx(this, '{{$domain1->_ref_incrementer->_guid}}')">
              {{$domain1->_ref_incrementer->_view}}
            </span>
          </label>
        </td>
        <td>
          <label>
            <input type="radio" name="incrementer_id" value="{{$domain2->incrementer_id}}" 
              {{if $domain2->incrementer_id}}onclick="$V(this.form.actor_id, '')"{{/if}} />
            <span onmouseover="ObjectTooltip.createEx(this, '{{$domain2->_ref_incrementer->_guid}}')">
              {{$domain2->_ref_incrementer->_view}}
            </span>
          </label>
        </td>
      </tr>
      
      <tr>
        <th>{{mb_label object=$domain1 field="actor_id"}}</th>
        <td>
          <label>
            <input type="radio" name="actor_id" value="{{$domain1->actor_id}}" checked="checked" 
              onclick="$V(this.form.actor_class, '{{$domain1->actor_class}}'); 
              {{if $domain1->actor_id}}$V(this.form.incrementer_id, ''){{/if}} " />
            <span onmouseover="ObjectTooltip.createEx(this, '{{$domain1->_ref_actor->_guid}}')">
              {{$domain1->_ref_actor->_view}}
            </span>
          </label>
        </td>
        <td>
          <label>
            <input type="radio" name="actor_id" value="{{$domain2->actor_id}}"  
              onclick="$V(this.form.actor_class, '{{$domain2->actor_class}}'); 
              {{if $domain2->actor_id}}$V(this.form.incrementer_id, ''){{/if}} " />
            <span onmouseover="ObjectTooltip.createEx(this, '{{$domain2->_ref_actor->_guid}}')">
              {{$domain2->_ref_actor->_view}}
            </span>
          </label>
        </td>
      </tr>
      
      <tr>
        <th>{{mb_label object=$domain1 field="libelle"}}</th>
        <td>
          <label>
            <input type="radio" name="libelle" value="{{$domain1->libelle}}" checked="checked" />
            {{$domain1->libelle}}
          </label>  
        </td>
        <td>
          <label>
            <input type="radio" name="libelle" value="{{$domain2->libelle}}" />
            {{$domain2->libelle}}
          </label>  
        </td>
      </tr>
      {{/if}}
         
      <tr>
        <td colspan="100" class="text">
          <div class="big-warning">
            Vous êtes sur le point d'effectuer une fusion d'objets.
            <br />
            <strong>Cette opération est irréversible, il est donc impératif d'utiliser cette fonction avec une extrême prudence !</strong>
            <br />
            
            La <strong>procédure alternative est sélectionnée</strong>, elle limite la fusion à 2 objets et se déroule en trois phases :
            <ol>
              <li>modifie le tag des identifiants actuellement enregistrés</li>
              <li>fusionne les deux domaines</li>
              <li>peut être lent, si le nombre d'objet liés est important</li>
            </ol>
          </div>
          
          <div id="merge-confirm" style="display: none; text-align: left;">
            Vous êtes sur le point d'éffectuer une <strong>fusion standard</strong>. 
            <br />Ce processus :
            <ul>
              <li>modifie le tag des identifiants actuellement enregistrés</li>
              <li>fusionne les deux domaines</li>
              <li>peut être lent, si le nombre d'objet liés est important</li>
            </ul>
            <br/>Voulez-vous <strong>confirmer cette action</strong> ?
          </div>
        </td>
      </tr>
      
      <tr>
        <td colspan="100" class="button">
          <button type="submit" class="merge" onclick="return Domain.confirm()">
            {{tr}}Merge{{/tr}}
          </button>
        </td>
      </tr>
    </table>
  </form>
{{/if}}
