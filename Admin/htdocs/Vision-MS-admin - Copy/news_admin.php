<!DOCTYPE html>
<html lang="en">

<?php
include "conn.inc.php";
?>  


<script>
    function applyNews(key){
        var up={};
        up['/news/'+key+'/applied']=true;
        var dt=new Date();
        var appliedTime= ''+((dt.getDate()<10)?'0':'')+dt.getDate()+((dt.getMonth()<9)?'0':'')+(dt.getMonth()+1)+dt.getFullYear().toString().substring(2) +((dt.getHours()<10)?'0':'')+dt.getHours()+((dt.getMinutes()<10)?'0':'')+dt.getMinutes()+((dt.getSeconds()<10)?'0':'')+dt.getSeconds();
        up['/news/'+key+'/time']=appliedTime;
        firebase.database().ref().update(up).catch(function(err){console.log(error);});
    }

</script>

    
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=1024">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>VISION 2K18</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sidebar.css" rel="stylesheet">
    <link href="css/table.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    
        <style>
    #balance{
        color: #f6f8f6;
        font-size: 23px;
        background-color: #004D40;
        padding-bottom: 6px;
        padding-top: 6px;
        margin: 8px;
        }
            body{
                font-family: 'Montserrat', sans-serif;
            }
    
    </style>

</head>

<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li id="balance">
                    VISION 2K18
                </li>
                
                <li>
                    <a href="admin.php">Session</a>
                </li>
                <li>
                    <a href="companies_admin.php">Companies</a>
                </li>
                <li>
                    <a href="news_admin.php"  class="active">News</a>
                </li>
                <li>
                    <a href="users.php">Users</a>
                </li>
                <li>
                    <a href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12"><br><br>
                        <a href="new_news.php" class="btn btn-primary btn-md">Add a news</a><br><br>
                        <table id="allnews" class="table-fill">
                             <tbody class="table-hover">
                             <script>
                                var table=document.getElementById('allnews');
                                firebaseRef=firebase.database().ref("news").orderByChild('seq');;
                                firebaseRef.on('value',function(dataSnap){
                                    if(dataSnap.exists()){
                                        table.innerHTML='<thead><tr> <th>No.</th> <th>Heading</th> <th>Description</th> <th>Status</th> <th>Edit</th> <th>Action</th></tr> </thead>';
                                        var arrValue = new Array();
                                        var appliedTime = "";
                                        dataSnap.forEach(function(data){
                                            var val=data.val();
                                            var th = document.createElement('th');
                                            arrValue.push([data.key, val.seq,  val.heading, val.desc.substring(0,40)+'...', ((val.applied&&val.effect)?'AE':((val.applied)?'A':''))]);
                                            if(val.applied&&!val.effect){
                                                appliedTime=val.time;
                                            }
                                        });
                                        for (var c = 0; c <= arrValue.length - 1; c++) {
                                            tr = table.insertRow(-1);
                                            tr.setAttribute('id', c);
                                            for (var j = 1; j <= 4; j++) {
                                                var td = document.createElement('td');
                                                td = tr.insertCell(-1);
                                                td.innerHTML = arrValue[c][j];
                                            }
                                            //Add edit button
                                            var td = document.createElement('td');
                                            td = tr.insertCell(-1);
                                            td.innerHTML+="<form action='news_edit.php' method = 'post'><input type='text' value="+ arrValue[c][0] +" name='edit_id' hidden><input type='submit' class='btn btn-sm btn-primary' value='Edit'></form>"
                                            //Add Apply button
                                            var td1 = document.createElement('td');
                                            td1 = tr.insertCell(-1);
                                            td1.innerHTML="<button type='button' onClick='applyNews(\""+ arrValue[c][0] +"\")'"+((arrValue[c][4]=='')?'':'disabled')+" class='btn btn-success'>Apply</button>";
                                            
                                        }
                                    }
                                    else
                                        table.innerHTML='No news added';
                                    });
                                 
                            </script>
                                
                            
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    
       
</body>

</html>


<?php




?>
