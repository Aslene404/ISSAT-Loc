const locations=document.getElementById('locations');

if(locations){
    locations.addEventListener('click', e=>{
        if(e.target.className === 'btn btn-danger delete-location'){
            var msg="Confirmer la suppression de "+e.target.getAttribute("data-name")+" ?"; 
            if(confirm(msg)){
                const id =e.target.getAttribute("data-id");
                fetch(`/location/delete/${id}`,{
                    method:'DELETE'
                }).then(res => window.location.reload());
                

            }
        }
    });
}
