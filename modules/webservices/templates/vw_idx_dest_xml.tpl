{{* $Id: vw_idx_echange_hprim.tpl 6287 2009-05-13 15:37:54Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(function () {
    {{if count($destinataire->_ref_exchanges_sources) > 0}}
      Control.Tabs.create('tabs-evenements-{{$destinataire->_guid}}', true);
    {{/if}}
  });
</script>

<table class="main">
  <tr>
    <td style="width:30%" rowspan="4">
      <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;destinataire_id=0">
        {{tr}}{{$destinataire->_class_name}}-title-create{{/tr}}
      </a>
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">{{tr}}{{$destinataire->_class_name}}{{/tr}}</th>
        </tr>
        <tr>
          <th>{{mb_title object=$destinataire field="nom"}}</th>
          <th>{{mb_title object=$destinataire field="group_id"}}</th>
          <th>{{mb_title object=$destinataire field="message"}}</th>
          <th>{{mb_title object=$destinataire field="actif"}}</th>
        </tr>
        {{foreach from=$listDestinataire item=_destinataire}}
        <tr {{if $_destinataire->_id == $destinataire->_id}}class="selected"{{/if}}>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;destinataire_id={{$_destinataire->_id}}" title="Modifier le destinataire XML">
              {{mb_value object=$_destinataire field="nom"}}
            </a>
          </td>
          <td>{{$_destinataire->_ref_group->_view}}</td>
          <td>{{mb_value object=$_destinataire field="message"}}</td>
          <td>{{mb_value object=$_destinataire field="actif"}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      {{if $can->edit}}
      <form name="edit{{$destinataire->_class_name}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <!-- mb_class object= -->
        {{if $destinataire->_class_name == "CPhastDestinataire"}}
          <input type="hidden" name="dosql" value="do_phast_destinataire_aed" />
        {{elseif $destinataire->_class_name == "CDestinataireHprim"}}
          <input type="hidden" name="dosql" value="do_destinataire_aed" />
        {{/if}}
        
        <input type="hidden" name="del" value="0" /> 
        
        {{mb_key object=$destinataire}}     
        
        <table class="form">
          <tr>
            {{if $destinataire->_id}}
            <th class="title modify text" colspan="2">
              {{mb_include module=system template=inc_object_idsante400 object=$destinataire}}
              {{mb_include module=system template=inc_object_history object=$destinataire}}
              
              {{tr}}{{$destinataire->_class_name}}-title-modify{{/tr}} '{{$destinataire}}'
            </th>
            {{else}}
            <th class="title" colspan="2">
             {{tr}}{{$destinataire->_class_name}}-title-create{{/tr}}
            </th>
            {{/if}}
          </tr>
          <tr>
            <th>{{mb_label object=$destinataire field="nom"}}</th>
            <td>{{mb_field object=$destinataire field="nom"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$destinataire field="libelle"}}</th>
            <td>{{mb_field object=$destinataire field="libelle"}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$destinataire field="group_id"}}</th>
            <td>{{mb_field object=$destinataire field="group_id" form="edit`$destinataire->_class_name`" autocomplete="true,1,50,true,true"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$destinataire field="message"}}</th>
            <td>{{mb_field object=$destinataire field="message"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$destinataire field="actif"}}</th>
            <td>{{mb_field object=$destinataire field="actif"}}</td>
          </tr>
          
          {{mb_include module=$destinataire->_ref_module->mod_name template="`$destinataire->_class_name`_inc"}}
          
          <tr>
            <td class="button" colspan="2">
              {{if $destinataire->_id}}
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$destinataire->_view|smarty:nodefaults|JSAttribute}}'})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{else}}
                 <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr>     
        </table>
      </form>
    </td>
  </tr>
  {{if count($destinataire->_ref_exchanges_sources) > 0}}
  <tr>
    <td>
      <table class="form">  
        <tr>
          <th class="title" colspan="2">
            {{tr}}config-exchange-source{{/tr}} '{{mb_value object=$destinataire field="message"}}'
          </th>
        </tr>
        <tr>
          <td colspan="2"> 
            <table class="form">  
              <tr>
                <td>
                  {{foreach from=$destinataire->_spec->messages key=_message item=_evenements}}
                    {{if $_message == $destinataire->message}}
                      <ul id="tabs-evenements-{{$destinataire->_guid}}" class="control_tabs">
                        {{foreach from=$_evenements item=_evenement}}
                          <li><a href="#{{$_evenement}}">{{tr}}{{$_evenement}}{{/tr}}</a></li>
                        {{/foreach}}
                      </ul>
                      
                      <hr class="control_tabs" />
                      
                      {{foreach from=$_evenements item=_evenement}}
                        <div id="{{$_evenement}}" style="display:none;">
                         {{mb_include module=system template=inc_config_exchange_source source=$destinataire->_ref_exchanges_sources.$_evenement}}
                        </div>
                      {{/foreach}}
                    {{/if}}
                  {{/foreach}}
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      {{/if}}
    </td>
  </tr>
  {{/if}}
</table>