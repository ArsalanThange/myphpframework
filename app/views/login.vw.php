<div class="container">
  <div class="row">
    <div class="col s12 m12 l5 offset-l3"><br>
      <form method="POST" action="/login">
        <div class="card">
          <div class="card-content">
            <div class="row">
                <div class="input-field col s6 l12">
                  <input id="username" type="text" class="validate" name="username" value="demo1">
                  <label for="username">Username</label>
                </div>
                <div class="input-field col s6 l12">
                  <input id="password" type="password" class="validate" name="password" value="Abc123!@#">
                  <label for="password">Password</label>
                </div>
              </div>
              <div class="row center">
                <input type="hidden" name="token" value="<?php echo generateCSRFToken() ?>">
                <button type="submit" class="btn blue">Login</button>
              </div>
              <div class="">
                  <?php
                    if (count($this->errors)) {
                        echo '<ul class="collection">';
                        foreach ($this->errors as $key => $value) {
                  ?>
                          <li class="collection-item">
                            <?php echo $value ?>
                          </li>
                  <?php
                        }
                        echo '</ul>';
                    }
                  ?>
              </div>
            </div>
          </div>
      </form>
    </div>
  </div>
</div>
