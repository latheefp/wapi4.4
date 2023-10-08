/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Document   : Auto Logout Script
 * Author     : josephtinsley
 * Description: Force a logout automatically after a certain amount of time using HTML/JQuery/PHP. 
 * http://twitter.com/josephtinsley 
*/


$(function()
{

    function timeChecker()
    {
        setInterval(function()
        {
            var storedTimeStamp = sessionStorage.getItem("lastTimeStamp");  
            timeCompare(storedTimeStamp);
        },30000);
    }


    function timeCompare(timeString)
    {
        //var maxMinutes  = 0;  //GREATER THEN 1 MIN.
        
        gettimeout();
        
        function ajaxCallBack(retString){
            maxMinutes = retString;
        }
        //console.log(maxMinutes +' is minutes');
        var currentTime = new Date();
        var pastTime    = new Date(timeString);
        var timeDiff    = currentTime - pastTime;
        var minPast     = Math.floor( (timeDiff/60000) ); 

        if( minPast > maxMinutes)
        {
            sessionStorage.removeItem("lastTimeStamp");
            window.location = "/login";
            return false;
        }else
        {
            //JUST ADDED AS A VISUAL CONFIRMATION
          //  console.log(currentTime +" - "+ pastTime+" - "+minPast+" min past");
        }
        
         function gettimeout(){
          $.ajax({
                url: "/ajaxes/gettimeout",
                method: "GET"
            })
            .done(function(timeout) {
                ajaxCallBack(timeout);	
              //  console.log(timeout);
            })
        }
        
        
    }

    if(typeof(Storage) !== "undefined") 
    {
        $(document).mousemove(function()
        {
            var timeStamp = new Date();
            sessionStorage.setItem("lastTimeStamp",timeStamp);
        });

        timeChecker();
    } 
    
   
});//END JQUERY


