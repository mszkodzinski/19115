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
                        <p>
                            <a class="btn btn-danger btn-lg" href="https://warszawa19115.pl/web/portal/zgloszenie-awarii-lub-interwencji" target="_blank">Zgłoś problem</a>
                            <a class="btn btn-success btn-lg" href="https://warszawa19115.pl/web/portal/sprawdz-status" target="_blank">Sprawdź status</a>
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="./script/init.js"></script>
    <script>
        $(function () {
            Hackathon19115.init('main');
        });
    </script>

<?php
include_once "footer.inc";
?>