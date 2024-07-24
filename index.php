<?php
error_reporting(0);
ob_start();
session_start();
include "config/koneksi.php";
include "config/fungsi_alert.php";
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <base href="localhost">
  <link rel="icon" href="gambar/admin/admin.png">
  <link href="css/font-awesome-4.2.0/font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet">
  <link href="css/owl-carousel/owl.carousel.css" rel="stylesheet" media="all">
  <link href="css/owl-carousel/owl.theme.css" rel="stylesheet" media="all">
  <link href="css/magnific-popup.css" type="text/css" rel="stylesheet" media="all" />
  <link href="css/font.css" rel="stylesheet" type="text/css" media="all">
  <link href="css/fontello.css" rel="stylesheet" type="text/css" media="all">
  <link href="css/main.css" rel="stylesheet" type="text/css" media="all" />
  <link rel=stylesheet href="css/paging.css" type="text/css" media=screen>
  <!-- Bootstrap 3.3.5 -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Bootstrap 3.3.5 -->
  <script src="aset/bootstrap.js"></script>


  <script src="aset/icheck/icheck.js"></script>
  <script src="aset/ckeditor/ckeditor.js"></script>
  <script src="aset/Flot/jquery.flot.js"></script>
  <!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
  <script src="aset/Flot/jquery.flot.resize.js"></script>
  <!-- FLOT PIE PLUGIN - also used to draw donut charts -->
  <script src="aset/Flot/jquery.flot.pie.js"></script>
  <!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->
  <script src="aset/Flot/jquery.flot.categories.js"></script>
  <!-- AdminLTE App -->
  <script src="aset/app.js"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      console.log('ready')

      let currentStep = 0;
      var arcolor = new Array('#b3ffcc', '#b3ffcc', '#b3ffcc', '#b3ffcc', '#b3ffcc', '#b3ffcc', '#b3ffcc', '#b3ffcc', '#b3ffcc', '#b3ffcc');
      setColor();
      $('.pilihkondisi').on('change', 'tr td select#sl<?php echo $i; ?>', function() {
        setColor();
      });

      function setColor() {
        var selectedItem = $('tr td select#sl<?php echo $i; ?> :selected');
        var color = arcolor[selectedItem.data("id")];
        $('tr td select#sl<?php echo $i; ?>.opsikondisi').css('background-color', color);
        // console.log(color);
      }

      const steps = Array.from(document.querySelectorAll("form .step"));
      // console.log(steps);
      const nextBtn = document.querySelectorAll("form .next-btn");
      const prevBtn = document.querySelectorAll("form .previous-btn");
      const form = document.querySelector("form");
      console.log(nextBtn);
      nextBtn.forEach(function(button) {
        //console.log(button);
        button.addEventListener("click", function() {
          // console.log("next clicked");
          changeStep("next");
        });
      });
      prevBtn.forEach((button) => {
        button.addEventListener("click", function() {
          // console.log("prev clicked");
          changeStep("prev");
        });
      });
      // form.addEventListener("submit", (e) => {
      //   e.preventDefault();
      //   alert("Hasil Klik");
      //   const inputs = [];
      //   form.querySelectorAll("input").forEach((input) => {
      //     const {
      //       name,
      //       value
      //     } = input;
      //     inputs.push({
      //       name,
      //       value
      //     });
      //   });
      //   console.log(inputs);
      //   form.reset();
      // });

      function changeStep(btn) {
        document.querySelector('.step-' + currentStep).classList.remove('active');
        if (btn === "next") {
          currentStep = currentStep + 1;
        } else if (btn === "prev") {
          currentStep = currentStep - 1;
        }

        document.querySelector('.step-' + currentStep).classList.add('active');
      }
    });
  </script>

  <link rel="stylesheet" href="aset/bootstrap.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="aset/AdminLTE.css">
  <link rel="stylesheet" href="aset/cinta.css">
  <link rel="stylesheet" href="aset/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="aset/skins/_all-skins.min.css">
  <link rel="stylesheet" href="aset/custom.css">
  <link rel="stylesheet" href="aset/icheck/green.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

  <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .content-wrapper {
          background-color: #f9fafc !important;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .step {
            display: none;
        }

        .active {
            display: block;
        }

        .question {
            margin-bottom: 15px;
        }

        .options {
            margin-top: 15px;
        }

        .button {
            padding: 10px;
            cursor: pointer;
            border: 1px solid #ccc;
            background-color: #f8f8f8;
        }

        .button-primary {
            background-color: #4CAF50;
            color: white;
        }
    </style>
    
</head>

<body id="pakaranjing" class="hold-transition skin-purple-light sidebar-mini">
  <div class="wrapper">
    <!-- Main Header -->
    <header class="main-header">
      <!-- Logo -->
      <a href="./" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b><i class="" aria-hidden="true"></i>AP</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b><i class="" aria-hidden="true"></i>Anjing Pomeranian</b></span>
      </a>
      <!-- Header Navbar -->
      <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <?php
            if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
            ?>
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="gambar/admin/admin.png" class="user-image" alt="User Image">
                  <?php echo ucfirst($_SESSION['username']); ?>
                  <span class="hidden-xs">
                    <?php echo $user; ?>
                  </span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    <img src="gambar/admin/admin.png" class="img-circle" alt="User Image">
                    <p>
                      Login sebagai
                      <?php echo ucfirst($_SESSION['username']); ?>
                      <small>Pakar</small>
                    </p>
                  </li>
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-right">
                      <a class="btn btn-default btn-flat" href="JavaScript: confirmIt('Anda yakin akan logout dari aplikasi ?','logout.php','','','','u','n','Self','Self')" onMouseOver="self.status = ''; return true" onMouseOut="self.status = ''; return true"><i class="fa fa-sign-out"></i> <span>LogOut</span></a>
                    </div>
                  </li>
                </ul>
              </li>
            <?php } else { ?>
              <li class="dropdown messages-menu">
                <a <?php if ($module == "formlogin")
                      echo 'class="active"'; ?> href="formlogin"><i class="fa fa-sign-in"></i> <span>Login</span></a>
              </li>
            <?php } ?>
          </ul>
        </div>
      </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
          <li class="header">Vincent</li>
          <!-- Optionally, you can add icons to the links -->
          <?php include "menu.php"; ?>
      </section>
      <!-- /.sidebar -->
    </aside>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="min-height: 310px;">
      <!-- Content Header (Page header) -->
      <section class="content-header">
      </section>
      <!-- Main content -->
      <section class="content">
        <div class="box">
          <div class="box-body">
            <?php include "content.php"; ?>
          </div>
        </div>
      </section><!-- /.content -->
    </div><!-- /.content-wrapper -->
    <!-- Add the sidebar's background. This div must be placed
          immediately after the control sidebar -->
    <div class="control-sidebar-bg" style="position: fixed; height: auto;"></div>
  </div><!-- ./wrapper -->


</body>

</html>
<?php
ob_end_flush();
?>