/*global PouchDB*/
/*global storeData*/
/*global loadSQL*/

var pitchersDB = new PouchDB('pitchers');
var pitchesDB = new PouchDB('pitch');

function getPouchRosterList(){

const rostlistDB = new PouchDB('roster_list', { skip_setup: true });

 // rostlistDB.destroy();


return new Promise ((response,rej)=>{

  rostlistDB.info().then((res)=>{

  return new Promise((resolve, reject)=>{
    if(res.doc_count === 0){
        resolve();
    }else{
      rostlistDB.allDocs({include_docs: true ,startkey: '_', descending:true})
     .then((res)=>{
     response(res.rows);
     reject("returned from db.info() promise");
   });
  }
  });
})
  .then((res) => {
    return getRosterList();
  }).then((obj)=> {
      return rostlistDB.bulkDocs(obj);

  }).then((res)=>{
    return rostlistDB.allDocs({include_docs: true, descending:true});
  }).then((found)=>{
    response(found.rows);
  }).catch(e => {
    rej("Error in pouchDBTransfer: "+e);
  });
  });



}

function getPouchRoster(team_id){

const rostDB = new PouchDB('roster', { skip_setup: true });

// db.destroy();

return new Promise ((response,rej)=>{

  rostDB.info().then((res)=>{

  return new Promise((resolve, reject)=>{
    if(res.doc_count === 0){
        resolve();
    }else{
   rostDB.createIndex({
       index: {
         fields:['team_id']
       }
     }).then((res)=>{
          return rostDB.find({
      selector: {
        team_id: team_id
      }
    });
     }).then((found)=>{
      response(found.docs);
     reject("returned from db.info() promise");
   });
    }
  });
})
  .then((res) => {
    return getRoster();
  }).then((obj)=> {
     return rostDB.bulkDocs(obj);
  }).then(()=>{

  return rostDB.createIndex({
       index: {
         fields:['team_id']
       }
     });
   }).then((res)=>{
          return rostDB.find({
      selector: {
        team_id: team_id
      }
    });
     }).then((found)=>{
   response(found.docs)

  })
  .catch(e => {
    rej("Error in pouchDBTransfer: "+e);
  });
  });



}

function getPouchPitcher(){

      return pitchersDB.allDocs({include_docs: true, limit: 1, descending:true})
         .then((res)=>{
            return res.rows[0].doc;
    });
  }

  function getPouchPitchers(){

        return pitchersDB.allDocs({include_docs: true, descending:true})
           .then((res)=>{
              return res.rows;
      });
    }

function pouchHasPitcher(){
   return pitchersDB.allDocs({include_docs: true, limit: 1, descending:true})
         .then((res)=>{
            return res.total_rows != 0;
    });

}

function getPouchPitches(id){
  return pitchesDB.createIndex({
       index: {
         fields:['pitcher_id']
       }
     }).then((res)=>{

       return pitchesDB.find({
      selector: {
        pitcher_id: id
      }
    })


     });


}

function storePouch(data){
  console.log("data object: ", data.objType);
 switch(data.objType) {
   case "1": return pitchersDB.put(data);
   case "2": return pitchesDB.put(data);
  }
}

function transferPouchToSql(){

    // pitchesDB.allDocs({include_docs: true}).then((res)=>{
    //     console.log("returned docs: ", res);
    //   });
  return new Promise ((pass,fail)=>{
    pitchersDB.allDocs({include_docs: true})
  .then((res)=>{
    console.log(res.total_rows);

    if(res.total_rows === 0){
      pitchesDB.allDocs({include_docs: true}).then((res)=>{
        console.log("returned docs: ", res);
      });
    }

      return res.rows.reduce((promise, docItem)=>{
        console.log("docitem",docItem.doc);
        return promise.then((res)=>{
           return storeData(docItem.doc);

        }).then((res)=>{

          return pitchesDB.createIndex({
                      index: {
                      fields: ['pitcher_id']
                  }
                });


        }).then(function () {
          console.log("DOCITEM:", docItem.doc);
                return pitchesDB.find({
                        selector: {
                        pitcher_id: docItem.doc.pitcher_id,
                        }
                     });
                }).then((res)=>{
                  console.log("item for bulkStoreData:", res.docs);
           return bulkStoreData(res.docs).then((res)=>{
             console.log("response from bulk store:", res);
           });
        });


      }, Promise.resolve());


  }).then((res)=>{
    console.log("db destroyed");
    pitchersDB.destroy().then((res)=>{
    pitchersDB = new PouchDB('pitchers');
    });
    pitchesDB.destroy().then((res)=>{

 pitchesDB = new PouchDB('pitch');
    });
    pass("Success!");

  }).catch((error)=>{

    console.log("no pitchers saved!:",error);
    fail(error);
  });
  });
}
