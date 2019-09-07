<?php
    session_start();

    function dbgMsg() {
        echo '[HTTP ' . $_SERVER['REQUEST_METHOD'] . "]\n";
        echo '"PHPSESSID" = ' . session_id() . "\n\n";
        echo 'Session "user" = ' . (@$_SESSION['user'] ?: '""') . "\n\n";
        //echo 'Hash = ' . password_hash('password', PASSWORD_DEFAULT) . "\n";
        echo 'POST "user" = "' . (@$_POST['user'] ?: '') . "\"\n";
        //echo 'POST "pwd" = "' . (@$_POST['pwd'] ?: '') . "\"\n";
        echo 'Auth = ' . auth();
    }

    function auth() {
        if (empty( $_SESSION['user'] ) && !empty($_POST['pwd']) &&
            password_verify($_POST['pwd'], '$2y$10$xx1Qi4.LVcXhpXmD/aUae.P5g2BrRuPiCC0Lkapf0otPnOBsMF9XO')) {
            $_SESSION = [];
            session_unset();
            session_destroy();
            session_start();
            $_SESSION['user'] = $_POST['user'];
        }
    }

    if (!empty( $_POST['task'] ) && $_POST['task'] == 'logout') {
        $_SESSION = [];
        session_unset();
        session_destroy();
        session_write_close();
        setcookie('PHPSESSID', '', time() - 3600);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    $isLogin = !empty( $_SESSION['user'] );

    if ($isLogin && !empty( $_GET['task'] ) && $_GET['task'] == 'assets') {
        header('Location: /member/css/custom.css');
        exit;
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PHP 台灣</title>
    <link href="/assets/css/index.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/custom.css" rel="stylesheet" type="text/css" />
  </head>
  <body class="index">
    <header>
      <div class="nav">
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>">重新整理</a>
      </div>
      <div class="header">顯示主題</div>
    </header>
    <form method="POST">
      <div class="table">
        <div class="row">
          <div class="col">
            除錯:
          </div>
          <div class="col">
            <pre><?php dbgMsg(); ?>
            </pre>
          </div>
        </div>
<?php if (! $isLogin): ?>
        <div class="row">
          <div class="col">
            帳號:
          </div>
          <div class="col">
            <input type="text" name="user" value="user">
          </div>
        </div>
        <div class="row">
          <div class="col">
            密碼:
          </div>
          <div class="col">
            <input type="password" name="pwd" value="password">
          </div>
        </div>
        <div class="row">
          <div class="col">
            <input type="submit" value="登入">
          </div>
        </div>
<?php
    else:
        auth();
?>
        <div class="row">
          <div class="col">
            工作階段識別碼:
          </div>
          <div class="col">
            <?php echo session_id(); ?>
          </div>
        </div>
        <div class="row">
          <div class="col">
            帳號:
          </div>
          <div class="col">
            <?php echo $_SESSION['user']; ?>
          </div>
        </div>
        <div class="row">
          <div class="col">
            顯示主題:
          </div>
          <div class="col">
            <?php echo @$_SESSION['theme']; ?>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <input type="hidden" name="task" value="logout">
            <input type="submit" value="登出">
          </div>
        </div>
<?php endif; ?>
      <div>
    </form>
  </body>
</html>
