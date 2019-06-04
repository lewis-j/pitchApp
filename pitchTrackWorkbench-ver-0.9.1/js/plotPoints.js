          function plotPoints(coords, colors, index){
          
  
          console.log("index", index);
          
           var svgNS = "http://www.w3.org/2000/svg";
          console.log(coords);
          var color;
      coords.forEach((coord)=>{
        if(coord.t == "FB"){
          color = colors[0];
        }else if(coord.t == "CB"){
         color = colors[1]; 
        }else if(coord.t == "CH"){
            color = colors[2];
        }else if(coord.t == "SL"){
           color = colors[3];
        }else if(coord.t == "other"){
            color = colors[4];
        }
        
      myCircle = document.createElementNS(svgNS, "circle"); //to create a circle. for rectangle use "rectangle"
      myCircle.setAttributeNS(null, "class", "mycircle");
      myCircle.setAttributeNS(null, "cx", coord.x+"%");
      myCircle.setAttributeNS(null, "cy", coord.y+"%");
      myCircle.setAttributeNS(null, "r", 8);
      myCircle.setAttributeNS(null, "fill", color);
      myCircle.savedToGraph = false;

      document.getElementById("mySVG"+index).appendChild(myCircle);
      });
          }