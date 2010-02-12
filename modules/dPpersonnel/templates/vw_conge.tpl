<script type="text/javascript">
loadUser=function(user_id){
  
    var url = new Url("dPpersonnel", "ajax_plage_vac");
    url.addParam("user_id", user_id);
    url.requestUpdate("vw_user");
  
}

editPlageVac = function(plage_id, user_id){
  
    var url = new Url("dPpersonnel", "ajax_edit_plage_vac");
    url.addParam("plage_id", plage_id);
    url.addParam("user_id", user_id);
    url.requestUpdate("edit_plage"); 
}

rechPlanning=function(){
  
    var url = new Url("dPpersonnel", "vw_planning_vacances");
    url.addParam("user_id", user_id);
		url.addParam("type_affichage",1);
    url.requestUpdate("rech_planning");
}
Main.add(function(){
  loadUser('{{$user->user_id}}');
	editPlageVac('',{{$user->user_id}});
});
</script>
<table class="main">
	{{assign var=affiche_nom value=false}}
	<tr>
		<td id = "vw_user">
			<script type="text/javascript">
				loadUser('{{$user->user_id}}');
			</script>
		</td>	
		<td id = "edit_plage">
			<script type="text/javascript">
				editPlage('',{{$user->user_id}});
			</script>
		</td>
	</tr>
	<tr>
  	<td id="planning"></td>
  </tr>
</table>