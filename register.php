<?php require 'php/forms.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>

    <?php require 'php/import-headers.php'; ?>

</head>
<body>
  <div class="app" id="app">

<!-- ############ LAYOUT START-->
    <div class="center-block w-xxl w-auto-xs p-y-md">
      <div class="d-flex justify-content-center align-items-center">
        <img src="./assets/images/logo.png" width="30%" alt="">
      </div>

    <div class="p-a-md box-color r box-shadow-z1 text-color m-a">
        <div class="mt-2 mb-2">
            <?php echo $msg ?>
        </div>
        <form method="post" target="register.php" name="form">
         <div class="md-form-group">
           <input name="email" type="email" class="md-input" value="<?php echo $email; ?>" required>
           <label>Email</label>
         </div>
         <div class="md-form-group">
           <input name="password" type="password" class="md-input" required>
           <label>Password</label>
         </div>
         <div class="md-form-group">
           <input name="password_r" type="password" class="md-input" required>
           <label>Repetir Password</label>
         </div>
         <button type="submit" class="btn primary btn-block p-x-md">Sign up</button>
       </form>
    </div>
    <br>
      <div class="p-v-lg text-center">
         <div>Ya tiene cuenta? <a ui-sref="access.signin" href="login.php" class="text-primary _600">Login</a></div>
      </div>
    </div>
  </div>

  <?php require 'php/import-script.php' ?>

</body>
</html>
