<?php require 'php/forms.php'; ?>

<!DOCTYPE html>
<html lang="en">
  <head>

    <?php require 'php/import-headers.php'; ?>

  </head>
  <body>
    <div class="app" id="app">
      <div class="center-block w-xxl w-auto-xs p-y-md">
      <div class="d-flex justify-content-center align-items-center">
        <img src="./assets/images/logo.png" width="30%" alt="">
      </div>
      <div class="p-a-md box-color r box-shadow-z1 text-color m-a">
        <div class="mt-3">
          <?php echo $msg ?>
        </div>
        <form target="" method="post" name="form">
          <div class="md-form-group float-label">
            <input name="email" type="email" class="md-input" value="<?php echo $email ?>" ng-model="user.email" required >
            <label>Email</label>
          </div>
          <div  class="md-form-group float-label">
            <input name="password" type="password" class="md-input" ng-model="user.password" required >
            <label>Password</label>
          </div>
          <button type="submit" class="btn primary btn-block p-x-md">Login</button>
        </form>
      </div>

      <div class="p-v-lg text-center">
        <div>No tiene cuenta? registrese aquí <a ui-sref="access.signup" href="register.php" class="text-primary _600">Registrarse</a></div>
      </div>
    </div>
    </div>

    <?php require 'php/import-script.php' ?>

  </body>
</html>
