Event.observe( window , 'load', init );

function init(){
	
	//Tous les éléments draggables
	elements=$('divGauche').getElementsByTagName('td');
	for (var i = 0; i < elements.length; i++) { 
	   new Draggable( elements[i].id, {revert:true});
	}
	
	elements=$('divDroite').getElementsByTagName('td');
	for (var i = 0; i < elements.length; i++) { 
		if(!elements[i].id[4]){new Draggable( elements[i].id, {revert:true});}
	}
	
	//Toutes les zones droppables
	Droppables.add('divGauche',{onDrop:TraiterDrop});
	
	elements=document.getElementById('divDroite').getElementsByTagName('td');
	for (var i = 0; i < elements.length; i++) { 
		Droppables.add(elements[i].id,{onDrop:TraiterDrop});
	}
}

function TraiterDrop(element, zoneDrop)
{
	if(element.parentNode.tagName!="TD" && element.parentNode.parentNode.parentNode.parentNode.id!="divGauche"){
		var child=document.createElement('td');
		child.style.backgroundColor="white";
		child.id="zone"+element.id;
		element.parentNode.insertBefore(child,element);
	}
	if(zoneDrop.id=="divGauche"){
		var child=document.createElement('tr');
		child.appendChild(element);
		var idd=element.getElementsByTagName('div').item(0).id;
		element.id=idd;
		zoneDrop.getElementsByTagName('table').item(0).appendChild(child); }
	else{

		if(!element.getElementsByTagName('div').item(0)){
			var child=document.createElement('div');
			child.id=element.id;
			element.appendChild(child);
		}
		zoneDrop.appendChild(element);
	}// Ajouter un fils à 'zoneDrop'
	
	init();//Afin de permettre le draggage des nouvelles balises TD
	savePlan();// sauvegarde automatique à chaque déplacement
}

function savePlan(){
	//Enregistrement des salles non placées
	elements=$('divGauche').getElementsByTagName('td');
	for (var i = 0; i < elements.length; i++) { 

		var url=new Url("dPhospi", "ajax_modif_plan_etage");
		url.addParam("chambre_id",elements[i].id);
		url.addParam("zone",null);
		url.requestUpdate('id');
	}
	

	//Enregistrement des salles placées sur le plan déplacées ou nouvellement placées
	elements=$('divDroite').getElementsByTagName('div');
	for (var i = 0; i < elements.length; i++) { 
		var url=new Url("dPhospi", "ajax_modif_plan_etage");
		url.addParam("chambre_id",elements[i].id);
		if(elements[i].parentNode.parentNode.id[4]!=null){
			url.addParam("zone",elements[i].parentNode.parentNode.id[4]+elements[i].parentNode.parentNode.id[5]);
		}
		url.requestUpdate('id');
	}

}