<!DOCTYPE html>
<html lang="en">

<?php
include "conn.inc.php";
?>

<script>
    function writeCompanyData() {
        var abbr = document.forms["addCompanyForm"]["new_abbr"].value;
		if(abbr==''){
			return false;
		}
        var name = document.forms["addCompanyForm"]["new_name"].value;
        var desc = document.forms["addCompanyForm"]["new_desc"].value;
        var ldesc = document.forms["addCompanyForm"]["new_ldesc"].value;
        var price = document.forms["addCompanyForm"]["new_price"].value;
        
        firebase.database().ref('Company/' + abbr).set({
          name: name,
          desc: desc,
          high: +price,
          low: +price
        });
        
        firebase.database().ref('CompPrice/' + abbr).set({
          price: price,
          p_price: price
        });

        var ldescription={};
        ldescription['/CompDesc/'+abbr]=ldesc;
        firebase.database().ref().update(ldescription);
        
        alert("New Company Added: "+name);
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
                    <a href="admin.php"  class="active">Companies</a>
                </li>
                <li>
                    <a href="news_admin.php">News</a>
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
                    <div class="col-lg-12"> 
                        <br>
                    <form name="addCompanyForm" onsubmit="return writeCompanyData()" action="companies_admin.php" method="post">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" name="new_name">
                            </div>
                            <div class="form-group">
                                <label for="abbr">Abbr:</label>
                                <input type="text" class="form-control" name="new_abbr">
                            </div>
                            <div class="form-group">
                               <label for="name">Short Description:</label>
                                <textarea class="form-control" rows = "6" name="new_desc"></textarea>
                            </div>
                            <div class="form-group">
                               <label for="name">Long Description:</label>
                                <textarea class="form-control" rows = "10" name="new_ldesc"></textarea>
                            </div>
                             <div class="form-group">
                                 <label for="price">Initial Price</label>
                                  <input type='text' class="form-control" name="new_price"> </div>
                            <input type="submit" class="btn btn-success">
                </form>                           
                                            
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