<?php include 'header.php'; ?>
<h2>Admin Login</h2>
<form id="admin-login-form" method="post" action="admin.php">
    <label for="admin_username">Username:</label>
    <input type="text" id="admin_username" name="admin_username" required><br>
    
    <label for="admin_password">Password:</label>
    <input type="password" id="admin_password" name="admin_password" required><br>
    
    <input type="submit" value="Login">
</form>
<?php include 'footer.php'; ?>
