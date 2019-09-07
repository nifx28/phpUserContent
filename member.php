<?php
    session_start();

    function dbgMsg() {
        echo '[HTTP ' . $_SERVER['REQUEST_METHOD'] . "]\n";
        echo '"PHPSESSID" = "' . session_id() . "\"\n\n";
        echo 'Session "user" = "' . (@$_SESSION['user'] ?: '') . "\"\n";
        echo 'Session "theme" = "' . (@$_SESSION['theme'] ?: '') . "\"\n\n";
        //echo 'Hash = ' . password_hash('password', PASSWORD_DEFAULT) . "\n";
        echo 'POST "user" = "' . (@$_POST['user'] ?: '') . "\"\n";
        //echo 'POST "pwd" = "' . (@$_POST['pwd'] ?: '') . "\"\n";
        echo 'POST "theme" = "' . (@$_POST['theme'] ?: '') . "\"";
    }

    function auth() {
        if (empty( $_SESSION['user'] ) && !empty($_POST['pwd']) &&
            password_verify($_POST['pwd'], '$2y$10$xx1Qi4.LVcXhpXmD/aUae.P5g2BrRuPiCC0Lkapf0otPnOBsMF9XO')) {
            $_SESSION = [];
            session_unset();
            session_destroy();
            session_start();
            $_SESSION['user'] = $_POST['user'];
            $_SESSION['theme'] = 'custom';
        }
    }

    if (!empty( $_POST['task'] ) && $_POST['task'] == 'theme') {
        $_SESSION['theme'] = htmlspecialchars( $_POST['theme'], ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
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

    auth();
    $isLogin = !empty( $_SESSION['user'] );

    if (!empty( $_GET['task'] ) && $_GET['task'] == 'assets') {
        if ($isLogin) {
            header('Location: /member/css/' . $_GET['theme'] . '.css');
            exit;
        } else {
            http_response_code(404);
            die();
        }
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
<?php if (!empty($_SESSION['theme'])): ?>
    <link href="/assets/css/<?php echo @$_SESSION['theme']; ?>.css" rel="stylesheet" type="text/css" />
<?php endif; ?>
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
            <pre><?php dbgMsg(); ?></pre>
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
    </form>
<?php else: ?>
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
            <select name="theme">
              <option value="custom"<?php echo @$_SESSION['theme'] == 'custom' ?  'selected' : ''; ?>>自訂風格</option>
              <option value="fancy"<?php echo @$_SESSION['theme'] == 'fancy' ?  'selected' : ''; ?>>華麗風格</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <input type="hidden" name="task" value="theme">
            <input type="submit" value="儲存風格">
          </div>
        </div>
      <div>
    </form>
    <form method="POST">
      <div class="table">
        <div class="row">
          <div class="col">
            <input type="hidden" name="task" value="logout">
            <input type="submit" value="登出">
          </div>
        </div>
      <div>
    </form>
<?php endif; ?>
  </body>
</html>
