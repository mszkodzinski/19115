<?php
$activePage = 'charts';
include_once "header.inc";
?>

    <div id="container" class="container">
        <div class="page-header" id="banner">
            <div class="bs-component">
                <div class="navbar navbar-default">
                    <div class="navbar-header">
                        <span class="navbar-brand">Filtruj</span>
                    </div>
                    <div class="navbar-collapse collapse navbar-responsive-collapse">
                        <ul class="nav navbar-nav">
                            <li>
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Data <b class="caret"></b></a>
                                <ul class="dropdown-menu date-type">
                                    <li data-filter="all"><a href="#">Wszystkie</a></li>
                                    <li data-filter="year"><a href="#">Bieżący rok</a></li>
                                    <li data-filter="month" class="active"><a href="#">Bieżący miesiąc</a></li>
                                    <li data-filter="own"><a href="#">Dowolny zakres</a></li>
                                </ul>
                            </li>
                        </ul>
                        <form class="navbar-form navbar-left">
                            <input type="text" value="" id="date-from" class="form-control col-lg-8" placeholder="Od">
                            <input type="text" value="" id="date-to" class="form-control col-lg-8" placeholder="Do">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Dzień po dniu</div>
                    <div class="panel-body" id="day-by-day">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">Źródła</div>
                    <div class="panel-body">
                        <ul class="list-group"  id="source">
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">Miesiące</div>
                    <div class="panel-body" id="year">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">Organizacje</div>
                    <div class="panel-body" id="organization">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Dzielnice</div>
                    <div class="panel-body" id="district">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div id="calendar"></div>
        </div>
    </div>
    <script src="//maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAuPsJpk3MBtDpJ4G8cqBnjRRaGTYH6UMl8mADNa0YKuWNNa8VNxQCzVBXTx2DYyXGsTOxpWhvIG7Djw" type="text/javascript"></script>
    <script type="text/javascript" src="//www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.load('visualization', '1', {packages: ['corechart', 'calendar', 'corechart']});
    </script>
    <script src="./script/bootstrap-datepicker.js"></script>
    <script src="./script/init.js"></script>
    <script>
        $(function () {
            Hackathon19115.init('charts');
        });
    </script>
<?php
include_once "footer.inc";
?>