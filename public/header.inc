<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>19115</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="./css/bootswatch.css">
    <link rel="stylesheet" href="./css/datepicker.css">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="./script/html5shiv.js"></script>
    <script src="./script/respond.min.js"></script>
    <![endif]-->
    <script src="./script/jquery.js"></script>
    <script src="./script/bootstrap.js"></script>
    <script src="./script/api.js"></script>
    <script src="./script/chart.js"></script>
</head>
<body>

<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a href="../" class="navbar-brand">19115</a>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
            <ul class="nav navbar-nav">
                <li <?php echo $activePage == 'charts' ? 'class="active"' : ''; ?>>
                    <a href="./charts.php">Wizualizacja</a>
                </li>
                <li <?php echo $activePage == 'maps' ? 'class="active"' : ''; ?>>
                    <a href="./maps.php">Mapa</a>
                </li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li><a href="http://twitter.com/" target="_blank">Kontakt</a></li>
                <li><a href="http://twitter.com/" target="_blank">Twitter</a></li>
                <li><a href="http://facebook.com/" target="_blank">Facebook</a></li>
            </ul>

        </div>
    </div>
</div>