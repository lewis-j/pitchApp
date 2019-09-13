/*global PouchDB*/
/*global storeData*/
/*global loadSQL*/

var pitchersDB = new PouchDB('pitchers');
var pitchesDB = new PouchDB('pitch');

function getPouchRoster(){

const db = new PouchDB('roster', { skip_setup: true });
 var year, season;
// db.destroy();


return new Promise ((response,rej)=>{

  db.info().then((res)=>{

  return new Promise((resolve, reject)=>{
    if(res.doc_count === 0){
        resolve();
    }else{
      db.allDocs({include_docs: true ,startkey: '_', limit:1, descending:true})
  .then((res)=>{
    // response(res.rows);
    console.log("roster response: ", res);
   year = res.rows[0].doc.year;
   season = res.rows[0].doc.season;
  // reject("Returned from pouchDBTransfer");
  return db.createIndex({
       index: {
         fields:['year','season']
       }
     });
   }).then((res)=>{
          return db.find({
      selector: {
        year: year,
        season: season
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
    return loadSQL();
  }).then((obj)=> {
     return db.bulkDocs(obj);
  }).then(()=>{
      return db.allDocs({include_docs: true ,startkey: '_', limit:2, descending:true})
  }).then((res)=>{
    // response(res.rows);
   year = res.rows[0].doc.year;
   season = res.rows[0].doc.season;
  // reject("Returned from pouchDBTransfer");
  return db.createIndex({
       index: {
         fields:['year','season']
       }
     });
   }).then((res)=>{
          return db.find({
      selector: {
        year: year,
        season: season
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
  console.log(data.objType);
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
                return pitchesDB.find({
                        selector: {
                        pitcher_id: docItem.id,
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
