<!DOCTYPE html>
<html lang="en">

<?php
include "conn.inc.php";
?>


<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=1024">
    <meta name="description" content="" />
    <meta name="keywords" content="stock market, share market, game, learn stocks, begginer" />
    <meta name="author" content="Anish Joshi"/>
    <meta name="robots" content="index, follow" />

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
                    <a href="news_admin.php">News</a>
                </li>
                <li>
                    <a href="users.php"  class="active">Users</a>
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
                    <div class="col-lg-12">
                        <br><br>
                        <form action="add_user.php" method="post">
                             <div class="control-group">
                                <div class="controls">
                                    <button type="submit" name="add_user" class="btn btn-primary button-loading" data-loading-text="Loading...">Add User</button>
                                </div>
                            </div>
                        </form>
                        <br>
                        <table id="allusers" class="table-fill">
                            <script>
                                var arr=new Array();
                                firebaseRef=firebase.database().ref("User").orderByChild('eval');
                                firebaseRef.on('value',function(dataSnap){
                                    if(dataSnap.exists()){
                                        var content = '<thead><tr> <th>Rank</th> <th>Team ID</th> <th>Team Name</th> <th>Participant 1</th> <th>Participant 2</th> <th>Balance</th> <th>Evaluation</th> </tr> </thead> <tbody class="table-hover">';
                                        arr=new Array();
                                        dataSnap.forEach(function(data){
                                            var val = data.val();
                                            arr.push([data.key, val.teamName, val.part1, val.part2, val.balance, val.eval]);
                                        });
                                        arr.reverse();
                                        var i=1;
                                        arr.forEach(function(data){
                                            content +='<tr>';
                                            content += '<td>' + i + '</td>';
                                            content += '<td>' + data[0] + '</td>';
                                            content += '<td>' + data[1] + '</td>';
                                            content += '<td>' + data[2] + '</td>';
                                            content += '<td>' + data[3] + '</td>';
                                            content += '<td>' + data[4] + '</td>';
                                            content += '<td>' + data[5] + '</td>';
                                            content += '</tr>';
                                            i++;
                                        });
                                        $('#allusers').html(content);
                                    }
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
