<?php
include "conn.inc.php";
?>
<script>
	firebase.auth().signOut();
	window.location="admin_login.php";
</script>