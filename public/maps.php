<?php
$activePage = 'maps';
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
    <div id="map" style="width:1140px; height:800px;"></div>
</div>
<script src="//maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAuPsJpk3MBtDpJ4G8cqBnjRRaGTYH6UMl8mADNa0YKuWNNa8VNxQCzVBXTx2DYyXGsTOxpWhvIG7Djw" type="text/javascript"></script>
<script src="./script/bootstrap-datepicker.js"></script>
<script src="./script/init.js"></script>
<script>
    $(function () {
        Hackathon19115.init('maps');
    });
</script>

<?php
include_once "footer.inc";
?>