<?php
$activePage = 'main';
include_once "header.inc";
?>

    <div class="container">
        <div class="page-header" id="banner">
            <div class="row">
                <div class="col-lg-8">
                    <div class="jumbotron">
                        <h1>Warszawa 19115</h1>
                        <p class="lead">Jeden numer. Tysiąc spraw.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <p>
                        <a class="btn btn-danger btn-lg" href="https://warszawa19115.pl/web/portal/zgloszenie-awarii-lub-interwencji" target="_blank">Zgłoś problem</a>
                        <a class="btn btn-success btn-lg" href="https://warszawa19115.pl/web/portal/sprawdz-status" target="_blank">Sprawdź status</a>
                    </p>
                </div>
            </div>
            <!--<div class="row">
                <div class="col-lg-6" style="padding: 15px 15px 0 15px;">
                    <img src="http://warszawa19115.pl/documents/453414/0/new_logo.png?t=1383219332478"/>
                </div>
            </div>-->
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">Najaktywniejsze dzielnice</div>
                    <div class="panel-body">
                        <ul class="list-group" id="district">
                        </ul>
                        <button type="button" class="btn btn-primary" href="maps.php" style="float:right">Zobacz na mapie</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">Podsumowanie</div>
                    <div class="panel-body" id="stats">
                        <h2 class="sum30"><span class="value"></span> <span class="note"></span><br/>
                        <span class="note">ost. 30 dni</span></h2>
                        <h4 class="diff30"><span class="value"></span></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">Typy zgłoszeń</div>
                    <div class="panel-body" id="type">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="//www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.load('visualization', '1', {packages: ['corechart', 'calendar', 'corechart']});
    </script>
    <script src="./script/init.js"></script>
    <script>
        $(function () {
            Hackathon19115.init('main');
        });
    </script>

<?php
include_once "footer.inc";
?>