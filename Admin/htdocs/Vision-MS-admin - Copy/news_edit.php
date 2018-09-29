<!DOCTYPE html>
<html lang="en">

<?php
include "conn.inc.php";
if(!isset($_POST['edit_id']) || empty($_POST['edit_id']))
{
    header("Location:admin.php");
}
?>
<script src="js/jquery.js"></script>

<script>
    function updateNews(){
        var head = document.getElementById("newHeading").value;
        var no = document.getElementById("newNo").value;
        var des = document.getElementById("newDesc").value;
        var updates = {};
        updates["news/"+<?php echo json_encode($_POST['edit_id']);?>+"/seq"] = (+no);
        updates["news/"+<?php echo json_encode($_POST['edit_id']);?>+"/heading"] = head;
        updates["news/"+<?php echo json_encode($_POST['edit_id']);?>+"/desc"] = des;
        firebase.database().ref().update(updates).catch(function(err){console.log(error);});
        document.location.reload(); 
	}

    function addComp(){
        var e = document.getElementById("menu");
        var comp = e.options[e.selectedIndex].value;
        var radios = document.getElementsByName('radio');
        var amount=11;
        var i=0;
        for (i = 0, length = radios.length; i < length; i++) {
            if (radios[i].checked) {
                amount=radios[i].value;
                break;
            }
        }
        if(i!=radios.length){
            var updates = {};
            updates["news/"+<?php echo json_encode($_POST['edit_id']);?>+"/affectedC/"+comp] = amount;
            firebase.database().ref().update(updates).catch(function(err){console.log(error);});       
        }
        else{
            console.log('Select Radio first');
        }
    }

    function delComp(comp){
        firebase.database().ref("news/"+<?php echo json_encode($_POST['edit_id']);?>+"/affectedC/"+comp).remove();
    }
</script>



    
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=1024">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Game Of Shares</title>

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
                    Admin
                </li>
                <li>
                    <a href="admin.php">Session</a>
                </li>
                <li>
                    <a href="companies_admin.php">Companies</a>
                </li>
                <li>
                    <a href="news_admin.php" class="active">News</a>
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
                    <div class="col-lg-12" id='newsEdit'>
    					<script>
                            var arrValue = new Array();
    						firebaseRef=firebase.database().ref("news/"+<?php echo json_encode($_POST['edit_id']);?>);
    						firebaseRef.on('value',function(dataSnap){
    							if(dataSnap.exists()){
    								var val=dataSnap.val();
    								arrValue.push([dataSnap.key, val.seq, val.heading, val.desc, ((val.applied&&val.effect)?'AE':((val.applied)?'A':''))]);
    								
                                    document.getElementById('newsEdit').innerHTML="<form action='news_edit.php' method = 'post'><input type='text' value="+ <?php echo json_encode($_POST['edit_id']);?> +" name='edit_id' hidden><div class='form-group'>\
                                    <label for='name'>No:</label>  <textarea class='form-control' rows = '1' name='no' id='newNo'>" + arrValue[0][1]+"</textarea><br><label for='name'>Heading:</label>	<textarea class='form-control' rows = '1' name='heading' id='newHeading'>" + arrValue[0][2] +
                                     "</textarea><br><div class='form-group'><label for='name'>Description:</label>	<textarea class='form-control' rows = '6' name='n_description' id='newDesc'>"+ 
                                     arrValue[0][3] +"</textarea></div> <button type='button' onclick='updateNews()' class='btn btn-success'>Update</button><br><br></form>"
    						    }
    						});
    				    </script>
                    </div>    			          
						<div class='form-group'>
    						<label for='acompany'>Affected Company:</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    						<select name='affectedCompany' id='menu'>
                            <script>
                                var content='';
                                firebaseRef=firebase.database().ref("CompPrice");
                                firebaseRef.on('value',function(dataSnap){
                                    if(dataSnap.exists()){
                                        dataSnap.forEach(function(data){
                                            content+="<option value='"+ data.key +"'>" + data.key + "</option>";
                                        });
                                    }
                                    $('#menu').html(content);
                                });
                            </script>
                            </select>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    						<input type='radio' name='radio' value=01 > 01 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    						<input type='radio' name='radio' value=00> 00 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    						<input type='radio' name='radio' value=10> 10 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    						<input type='radio' name='radio' value=11> 11
                            <br><br>
                            <button type='button' onClick='addComp()' class='btn btn-success'>Add Affected Company</button><br><br>
						</div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
		
		<div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12"><br><br>
                        <table id="affectedC" class="table-fill">
                        <script>        
                           firebaseRef=firebase.database().ref("news/"+<?php echo json_encode($_POST['edit_id']);?>+"/affectedC");
                                firebaseRef.on('value',function(dataSnap){
                                    if(dataSnap.exists()){
                                        var i=1;
                                        var content = '<thead><tr> <th>No. </th> <th>Abbr</th> <th>Effect</th> <th>Delete</th> </tr> </thead> <tbody class="table-hover">';
                                        dataSnap.forEach(function(data){
                                            var val = data.val();
                                            content +="<tr>";
                                            content += '<td>' + i + '</td>';
                                            content += '<td>' + data.key + '</td>';
                                            content += '<td>' + val + '</td>';
                                            content += "<td><button type='button' onClick='delComp(\""+data.key+"\")' class='btn btn-success'>Delete</button></td>";
                                            content += '</tr>';
                                            i+=1;
                                        });
                                        $('#affectedC').html(content);
                                    }
                                    else{
                                        $('#affectedC').html('No Companies to show');
                                    }
                                });
                            </script>    
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
        -->


    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>