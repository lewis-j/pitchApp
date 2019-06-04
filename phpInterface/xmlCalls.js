function storeData(PlayerData) {
	
	return new Promise((resolve, reject)=>{
	  
	PlayerData.credentials = {username :"testingNAme" , password: ""};    
    const jsonString = JSON.stringify(PlayerData);
         const xhr = new XMLHttpRequest();
xhr.onreadystatechange = function(){
if(xhr.readyState==4){
if(xhr.status==200){
  console.log("XHR OBJECT _____________:", xhr)
    console.log("response:", xhr.response);
    if(xhr.response != null && xhr.response == false ){
      reject(xhr.response);
    } else {
      resolve(xhr.response);
    }
    
} else{
    console.error("Ready State: " + xhr.readyState)
    console.error("Status: " + xhr.status)
    console.error("Status Text: " + xhr.statusText);
    alert("An error has occured making the request")
    reject();

}
}
}
        xhr.responseType = "json";
         xhr.open("POST", "phpInterface/server.php");
         xhr.setRequestHeader("Content-Type", "application/json");
         xhr.send(jsonString);
	    
	    
	
	    
	    
	});
	
}

function bulkStoreData(pitchesData) {
	
	return new Promise((resolve, reject)=>{
	    
    const jsonString = JSON.stringify(pitchesData);
         const xhr = new XMLHttpRequest();
xhr.onreadystatechange = function(){
if(xhr.readyState==4){
if(xhr.status==200){
    // document.getElementById("result").innerHTML=xhr.responseText; 
    console.log(xhr.response);
    resolve(xhr.response);
} else{
    console.error("Ready State: " + xhr.readyState)
    console.error("Status: " + xhr.status)
    console.error("Status Text: " + xhr.statusText);
    alert("An error has occured making the request")
    reject();

}
}
}
         xhr.open("POST", "phpInterface/bulk_insert.php");
         xhr.setRequestHeader("Content-Type", "application/json");
         xhr.send(jsonString);
	    
	    
	
	    
	    
	});
	
}

  function loadSQL(method){
  	
         return new Promise(function (resolve, reject){
         	
         	
       const xhr = new XMLHttpRequest();
xhr.onreadystatechange = function(){
if(xhr.readyState==4){
if(xhr.status==200){
  console.log(xhr.response);
  var obj = JSON.parse(xhr.response);
	resolve(obj);
    
} else{
    reject({
					status: xhr.status,
					statusText: xhr.statusText
				});

}
}
}
         xhr.open("POST", "phpInterface/server_get.php");
         //xhr.setRequestHeader("Content-Type", "application/json");
         xhr.send(method);
  	
  }); 
  }