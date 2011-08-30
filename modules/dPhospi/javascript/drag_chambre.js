Event.observe( window , 'load', init );

function init(){
	
	//Tous les éléments draggables: les div
	elements=$('divGauche').getElementsByTagName('div');
	for (var i = 0; i < elements.length; i++) { 
	   new Draggable( elements[i].id, {revert:true});
	}
	
	elements=$('divDroite').getElementsByTagName('div');
	for (var i = 0; i < elements.length; i++) { 
		if(!elements[i].id[4]){
			new Draggable( elements[i].id, {revert:true});
		}
	}
	
	//Toutes les zones droppables: les td
	Droppables.add('divGauche',{onDrop:TraiterDrop});
	
	elements=document.getElementById('divDroite').getElementsByTagName('td');
	for (var i = 0; i < elements.length; i++) { 
		if(elements[i].getElementsByTagName('div').item(0)==null){
			Droppables.add(elements[i].id,{onDrop:TraiterDrop});
		}
	}
}

function TraiterDrop(element, zoneDrop)
{
	if(zoneDrop.id=="divGauche"){
		var tr=document.createElement('tr');
		tr.style.border="1px blue dotted";
		tr.style.width="100%";
		tr.style.height="25px";
		tr.style.textAlign="center";
		tr.style.paddingTop="10px";
		
		var td=document.createElement('td');
		td.style.backgroundColor="white";
		td.style.width="75px";
		td.style.height="35px";
		td.appendChild(element);
		tr.appendChild(td);
		zoneDrop.getElementsByTagName('table').item(0).appendChild(tr);
	}
	else{
		zoneDrop.appendChild(element);// Ajouter un fils à 'zoneDrop'
	}
	savePlan();// sauvegarde automatique à chaque déplacement	
}

function savePlan(){
	//Enregistrement des salles non placées
	elements=$('divGauche').getElementsByTagName('div');
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
		url.addParam("zone",elements[i].parentNode.id[4]+elements[i].parentNode.id[5]);
		url.requestUpdate('id');
	}
}