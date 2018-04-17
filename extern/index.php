<?php 
ini_set('date.timezone','Asia/Shanghai');

$resp = array('errcode' => 0 , 'errmsg' => 'success');

function fatal($errmsg)
{
	$resp['errcode'] = 500;
	$resp['errmsg'] = $errmsg;
	echo json_encode($resp);
	exit();
}

$db_host = 'rm-uf658t1hnuxsvd850.mysql.rds.aliyuncs.com';
$db_user = 'exands';
$db_pass = 'exands#100';

$exacs = mysqli_connect($db_host, $db_user, $db_pass, 'exacs');
if (!$exacs)
	fatal('exacs db connection failure');
mysqli_query($exacs, 'set names utf8');

$exals = mysqli_connect($db_host, $db_user, $db_pass, 'exals');
if (!$exals)
	fatal('exals db connection failure');
mysqli_query($exals, 'set names utf8');

function fmac(&$mac)
{
	$mac = strtolower($mac);
	$mac = str_replace(":", "", $mac);
	$mac = str_replace("-", "", $mac);
	$mac = str_replace(".", "", $mac);

	if (!preg_match('/^[0-9a-fA-F]{12}$/', $mac))
		return false;

	return true;
}

function deleteDevice($device_id)
{
	global $exacs, $exals;

	$sql = sprintf("delete from device_tag where device_id='%s'", $device_id);
	if (mysqli_query($exacs, $sql) == false)
		fatal(mysqli_error($exacs));

	$sql = sprintf("delete from device_package where device_id='%s'", $device_id);
	if (mysqli_query($exacs, $sql) == false)
		fatal(mysqli_error($exacs));

	$sql = sprintf("delete from device where device_id='%s'", $device_id);
	if (mysqli_query($exacs, $sql) == false)
		fatal(mysqli_error($exacs));

	$sql = sprintf("select site_id from site_device where device_id='%s'", $device_id);
	$results = mysqli_query($exals, $sql);
	if ($results && mysqli_num_rows($results) > 0)
	{
		$row = mysqli_fetch_assoc($results);

		$sql = sprintf("delete from site where site_id='%s'", $row['site_id']);
		mysqli_query($exals, $sql);

		mysqli_free_result($results);
	}

	$sql = sprintf("delete from site_device where device_id='%s'", $device_id);
	mysqli_query($exals, $sql);
}

function getTag(&$tags, $key = '')
{
	if ($key)
	{
		$tag = @$_REQUEST[$key];
		if (!empty($tag))
			$tags[] = $tag;
	}
}

function updateOldSystem($mac)
{
	# c
	$db = mysqli_connect('222.73.219.57', 'exands', 'exands', 'exacs');
	$sql = "INSERT INTO device SET device_id='".$mac."', server='c3' ON DUPLICATE KEY UPDATE server='c3'";
	mysqli_query($db, $sql);
	mysqli_close($db);

	# c2
#	$db = mysqli_connect('61.129.51.67', 'exands', 'exands', 'exacs');
#	$sql = "INSERT INTO device SET device_id='".$mac."', version='2.1.1' ON DUPLICATE KEY UPDATE version='2.1.1'";
#	mysqli_query($db, $sql);
#	mysqli_close($db);

	#c3
	$db = mysqli_connect('118.190.27.185', 'exands', 'exands', 'exacs');
	$sql = "INSERT INTO device SET device_id='".$mac."', server='c4' ON DUPLICATE KEY UPDATE server='c4'";
	mysqli_query($db, $sql);
	mysqli_close($db);

	# c4
	$db = mysqli_connect('118.190.25.90', 'exands', 'exands', 'exacs');
	$sql = "INSERT INTO device SET device_id='".$mac."', version='2.1.1' ON DUPLICATE KEY UPDATE version='2.1.1'";
	mysqli_query($db, $sql);
	mysqli_close($db);
}

function updateSiteName($site_id, $site_name, $device_id)
{
	global $exacs, $exals;

	$sql = sprintf("INSERT INTO site SET site_id='%s', site_name='%s' ON DUPLICATE KEY UPDATE site_name='%s'", $site_id, $site_name, $site_name);
	mysqli_query($exals, $sql);

	$sql = sprintf("INSERT INTO site_device SET site_id='%s', device_id='%s' ON DUPLICATE KEY UPDATE site_id='%s'", $site_id, $device_id, $site_id);
	mysqli_query($exals, $sql);
}

$method = @$_REQUEST['method'];
try
{
	switch ($method)
	{
		case 'add':
			// method=add&devid=[MAC]&province=shanghai&district=xuhui&project=starbucks&type=rbwan 

			$device_id = @$_REQUEST['devid'];
			if ($device_id == null || !fmac($device_id))
				throw new Exception('devid parameter missing or invalid', 406);

			deleteDevice($device_id);

			$config = '<conf><program name="exals"/></conf>'; 

			$sql = sprintf("insert into device set device_id='%s', create_time=now(), config_time=now()", $device_id);
			mysqli_query($exacs, $sql);

			// tags
			$tags = array('exals');
			getTag($tags, 'province');
			getTag($tags, 'city');
			getTag($tags, 'district');
			getTag($tags, 'project');
			getTag($tags, 'type');
			getTag($tags, 'site_id');
			getTag($tags, 'shop_id');
			getTag($tags, 'shop_name');
			array_unique($tags);

			foreach ($tags as $tag)
			{
				$sql = sprintf("insert into device_tag set device_id='%s', tag='%s'", $device_id , $tag);
				mysqli_query($exacs, $sql);
			}

			$sql = sprintf("insert into device_package set device_id='%s', package_name='exals-gw', package_type='img', config='%s'", $device_id, $config);
			mysqli_query($exacs, $sql);

			updateOldSystem($device_id);

			$site_id = @$_REQUEST['site_id'];
			if (!$site_id)
				$site_id = $device_id;

			$project = @$_REQUEST['project'];
			$site_name = @$_REQUEST['shop_name'];
			updateSiteName($site_id, $project.'-'.$site_name, $device_id);

			break;
	
		case 'del':
			// method=del&devid=[MAC]

			$device_id = @$_REQUEST['devid'];
			if ($device_id == null || !fmac($device_id))
				throw new Exception('devid parameter missing or invalid', 400);

			deleteDevice($device_id);

			break;

		default:
			throw new Exception('method ' . $method . ' not recognized', 400);
	}
}
catch (Exception $e)
{
	$resp['errcode'] = $e->getCode();
	$resp['errmsg'] = $e->getMessage();
}

echo json_encode($resp);

exit();
