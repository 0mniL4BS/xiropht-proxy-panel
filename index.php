<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>ProxyPanel</title>
    <meta content="width=1000, initial-scale=0.5, maximum-scale=1, user-scalable=yes" name="viewport">
    <link href="https://cdn.bootcss.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/font-awesome/3.1.1/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/datatables/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/AdminLTE.min.css">
    <link rel="stylesheet" href="css/skin-black.min.css">
	
    <style>
		html {
			-webkit-text-size-adjust: none;
			touch-action: manipulation;
		}

        .info-box-content {
            text-align: center;
            height: 30px;
            line-height: 30px;
            padding-top: 10px;
        }

        .info-box-text {
            text-transform: none;
        }

        .table-right {
            text-align: right;
        }

        .table-center {
            text-align: center;
        }
    </style>
</head>

<?php
$API_IP = "spookypool.nl";
$API_Port = "8282";


$Get_Proxy_Stats = file_get_contents('http://'.$API_IP.':'.$API_Port.'/get_proxy_stats');
$Get_Proxy_Stats_Obj = json_decode($Get_Proxy_Stats);

$TotalHashrate_NoSeperator = intval($Get_Proxy_Stats_Obj->proxy_hashrate_expected);
$TotalHashrate = round(number_format(intval($Get_Proxy_Stats_Obj->proxy_hashrate_expected), 0, ',', '.'), 2);
$TotalBlocksMined = $Get_Proxy_Stats_Obj->proxy_total_block_found;
$TotalMiners = $Get_Proxy_Stats_Obj->proxy_total_miners;

function GetHashrateSpeed($Hashrate) {
	if($Hashrate < 1000) {
		return " H/s";
	} else if ($Hashrate < 1000000) {
		return " KH/s";
	} else if ($Hashrate < 1000000000) {
		return " MH/s";
	} else if ($Hashrate < 1000000000000) {
		return " GH/s";
	}
}


$GoodShares = 0;
$BadShares = 0;
?>

<body class="hold-transition skin-black layout-top-nav">
<div class="wrapper">
    <header class="main-header">
        <nav class="navbar-default navbar-static-top navbar-light bg-faded">
            <div class="container">
                <div class="navbar-header">
                    <a href="" class="navbar-brand"><b>Proxy</b>Panel</a>
                </div>

                <div class="navbar-default pull-right">
                    <ul class="nav navbar-nav">
                        <li><span id="best" class="navbar-text"></span></li>
                        <li><span id="latency" class="navbar-text"></span></li>
                        <li><span id="avg_time" class="navbar-text"></span></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="content-wrapper">
        <div class="container">
            <section class="content">
                <div class="row">
                    <div id="main">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i
                                            class="ion ion-ios-world-outline"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Online workers</span>
                                        <span class="info-box-number" id="activeworkers">
										<?php
										$OnlineWorkers = 0;
										for ($id = 1 ; $id < intval($TotalMiners)+1; $id++){
											$Get_Miner_By_ID = file_get_contents('http://'.$API_IP.':'.$API_Port.'/get_miner_by_id='.$id);
											$Get_Miner_By_ID_Obj = json_decode($Get_Miner_By_ID);
											
											if($Get_Miner_By_ID_Obj->miner_status == "connected"){
												$OnlineWorkers = $OnlineWorkers+1;
												$GoodShares = $GoodShares+intval($Get_Miner_By_ID_Obj->miner_total_good_share);
												$BadShares = $BadShares+intval($Get_Miner_By_ID_Obj->miner_total_invalid_share);
											}
										}
										echo $OnlineWorkers;
										?>
										</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-red"><i class="ion ion-ios-world"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Offline workers</span>
                                        <span class="info-box-number" id="notactiveworkers">
										<?php
										$OfflineWorkers = 0;
										for ($id = 1 ; $id < intval($TotalMiners)+1; $id++){
											$Get_Miner_By_ID = file_get_contents('http://'.$API_IP.':'.$API_Port.'/get_miner_by_id='.$id);
											$Get_Miner_By_ID_Obj = json_decode($Get_Miner_By_ID);
											
											if($Get_Miner_By_ID_Obj->miner_status == "connected"){ } else {
												$OfflineWorkers = $OfflineWorkers+1;
												$GoodShares = $GoodShares+intval($Get_Miner_By_ID_Obj->miner_total_good_share);
												$BadShares = $BadShares+intval($Get_Miner_By_ID_Obj->miner_total_invalid_share);
											}
										}
										echo $OfflineWorkers;
										?>
										</span>
                                    </div>
                                </div>
                            </div>
							<div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-aqua"><i class="ion ion-speedometer"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Hashrate</span>
                                        <span class="info-box-number" id="hashrate"><?php echo $TotalHashrate." ".GetHashrateSpeed($TotalHashrate_NoSeperator); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="ion ion-flag"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Good Shares</span>
                                        <span class="info-box-number" id="acceptedshares"><?php echo $GoodShares; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-red"><i class="ion ion-flag"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Bad Shares</span>
                                        <span class="info-box-number" id="hashes"><?php echo $BadShares; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-aqua"><i class="ion ion-cube"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Blocks Mined</span>
                                        <span class="info-box-number" id="effort"><?php echo $TotalBlocksMined; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
						
						
						<div class="box box-success">
							<div class="box-header">
								<h3 class="box-title">Online workers</h3>
							</div>
							<div class="box-body">
								<div id="workerstable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
									<div class="row">
										<div class="col-sm-12">
											<table id="workerstable" class="table table-bordered table-hover table-striped dataTable no-footer" style="width: 100%;" role="grid" aria-describedby="workerstable_info">
												<thead>
													<tr role="row">
														<th class="table-center" style="width: 409px;">Name</th>
														<th class="table-right" style="width: 129px;">Hashrate</th>
														<th class="table-right" style="width: 129px;">Shares</th>
														<th class="table-right" style="width: 129px;">Good shares</th>
														<th class="table-right" style="width: 129px;">Bad shares</th>
														<th class="table-right" style="width: 130px;">Mining range</th>
														<th class="table-right" style="width: 130px;">Version</th>
													</tr>
												</thead>
												<tbody>
													<?php
													for ($id = 1 ; $id < intval($TotalMiners)+1; $id++){
														$Get_Miner_By_ID = file_get_contents('http://'.$API_IP.':'.$API_Port.'/get_miner_by_id='.$id);
														$Get_Miner_By_ID_Obj = json_decode($Get_Miner_By_ID);
														
														if($Get_Miner_By_ID_Obj->miner_status == "connected"){
															echo "<tr>";
															echo "<td>".$Get_Miner_By_ID_Obj->miner_name."</td>";
															echo "<td align='right'>".round(number_format(intval($Get_Miner_By_ID_Obj->miner_hashrate), 0, ',', '.'), 2)." ".GetHashrateSpeed($Get_Miner_By_ID_Obj->miner_hashrate)."</td>";
															echo "<td align='right'>".$Get_Miner_By_ID_Obj->miner_total_share."</td>";
															echo "<td align='right'>".$Get_Miner_By_ID_Obj->miner_total_good_share."</td>";
															echo "<td align='right'>".$Get_Miner_By_ID_Obj->miner_total_invalid_share."</td>";
															echo "<td align='right'>".$Get_Miner_By_ID_Obj->miner_range."</td>";
															echo "<td align='right'>".$Get_Miner_By_ID_Obj->miner_version."</td>";
															echo "</tr>";
														}
													}
													?>													
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="box box-danger">
							<div class="box-header">
								<h3 class="box-title">Offline workers</h3>
							</div>
							<div class="box-body">
								<div id="workerstable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
									<div class="row">
										<div class="col-sm-12">
											<table id="workerstable" class="table table-bordered table-hover table-striped dataTable no-footer" style="width: 100%;" role="grid" aria-describedby="workerstable_info">
												<thead>
													<tr role="row">
														<th class="table-center" style="width: 409px;">Name</th>
														<th class="table-right" style="width: 129px;">Hashrate</th>
														<th class="table-right" style="width: 129px;">Shares</th>
														<th class="table-right" style="width: 129px;">Good shares</th>
														<th class="table-right" style="width: 129px;">Bad shares</th>
														<th class="table-right" style="width: 130px;">Mining range</th>
														<th class="table-right" style="width: 130px;">Version</th>
													</tr>
												</thead>
												<tbody>
													<?php
													for ($id = 1 ; $id < intval($TotalMiners)+1; $id++){
														$Get_Miner_By_ID = file_get_contents('http://'.$API_IP.':'.$API_Port.'/get_miner_by_id='.$id);
														$Get_Miner_By_ID_Obj = json_decode($Get_Miner_By_ID);
														
														if($Get_Miner_By_ID_Obj->miner_status == "connected"){ } else {
															echo "<tr>";
															echo "<td>".$Get_Miner_By_ID_Obj->miner_name."</td>";
															echo "<td align='right'>".round(number_format(intval($Get_Miner_By_ID_Obj->miner_hashrate), 0, ',', '.'), 2)." ".GetHashrateSpeed($Get_Miner_By_ID_Obj->miner_hashrate)."</td>";
															echo "<td align='right'>".$Get_Miner_By_ID_Obj->miner_total_share."</td>";
															echo "<td align='right'>".$Get_Miner_By_ID_Obj->miner_total_good_share."</td>";
															echo "<td align='right'>".$Get_Miner_By_ID_Obj->miner_total_invalid_share."</td>";
															echo "<td align='right'>".$Get_Miner_By_ID_Obj->miner_range."</td>";
															echo "<td align='right'>".$Get_Miner_By_ID_Obj->miner_version."</td>";
															echo "</tr>";
														}
													}
													?>													
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<center>Download <a href="xiropht-proxy-panel-0.0.0.1R.zip" target="_blank">xiropht-proxy-panel-0.0.0.1R.zip</a></center>
						
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="https://cdn.bootcss.com/datatables/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.bootcss.com/datatables/1.10.16/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.bootcss.com/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
<script src="js/adminlte.min.js"></script>
</body>
</html>


