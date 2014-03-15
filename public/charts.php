<?php
include_once "header.inc";
?>

<div id="container" class="container">
    <div class="page-header" id="banner">
        <div class="bs-component">
            <div class="navbar navbar-default">
                <div class="navbar-header">
                    <span class="navbar-brand">Sortuj dane</span>
                </div>
                <div class="navbar-collapse collapse navbar-responsive-collapse">
                    <form class="navbar-form navbar-left open">
<!--                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>-->
                        <select name="date-type" id="date-type" class="dropdown-menu">
                            <option value="all">Wszystkie</option>
                            <option value="year" selected="selected">Bieżący rok</option>
                            <option value="month">Bieżący miesiąc</option>
                            <option value="own">Dowolny zakres</option>
                        </select>
                        <input type="text" value="" id="date-from" class="form-control col-lg-8" placeholder="Od">
                        <input type="text" value="" id="date-to" class="form-control col-lg-8" placeholder="Do">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="map" style="width:1140px; height:800px;display:none"></div>
    <div id="visualization" style="width:1140px; height:800px;display:none"></div>
    <div id="source"></div>
    <div id="district"></div>
    <div id="year"></div>
    <div id="calendar"></div>

    <div id="pie" style="width: 900px; height: 700px;"></div>
</div>
    <script src="//maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAuPsJpk3MBtDpJ4G8cqBnjRRaGTYH6UMl8mADNa0YKuWNNa8VNxQCzVBXTx2DYyXGsTOxpWhvIG7Djw" type="text/javascript"></script>
    <script type="text/javascript" src="//www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load('visualization', '1', {packages: ['corechart', 'calendar', 'corechart']});
</script>
<script src="./script/bootstrap-datepicker.js"></script>
<script src="./script/init.js"></script>

<?php
include_once "footer.inc";
?>