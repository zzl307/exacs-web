<?php

namespace App\Http\Controllers;

use App\Device;
use App\DeviceTag;
use App\DeviceTagAttrs;
use Illuminate\Http\Request;

class IndexController extends Controller
{	
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		$devices = Device::devices();

		$total['devices'] = count($devices);
		$total['online'] = 0;
		$total['rate'] = 0;

		foreach ($devices as $device)
		{
			if (Device::isOnline($device))
				$total['online']++;
		}
		if ($total['devices'] > 0)
			$total['rate'] = round($total['online'] / $total['devices'] * 100);

		$tags = DeviceTagAttrs::select('name')->where('status', '=', 1)->get();
		$devicesByTag = array();

		if (count($tags) > 0)
		{
			foreach ($tags as $tag)
			{
				$devicesByTag[$tag->name]['devices'] = 0;
				$devicesByTag[$tag->name]['online'] = 0;
				$devicesByTag[$tag->name]['rate'] = 0;

				foreach ($devices as $device)
				{
					if (in_array($tag->name, $device['tags']))
					{
						$devicesByTag[$tag->name]['devices']++;
						if (Device::isOnline($device))
							$devicesByTag[$tag->name]['online']++;
					}
				}

				if ($devicesByTag[$tag->name]['devices'] > 0)
				{
					$devicesByTag[$tag->name]['rate'] = round($devicesByTag[$tag->name]['online'] / $devicesByTag[$tag->name]['devices'] * 100);
				}

				$rates[$tag->name] = $devicesByTag[$tag->name]['rate'];
				$totals[$tag->name] = $devicesByTag[$tag->name]['devices'];
			}

			array_multisort($rates, SORT_DESC, $totals, SORT_DESC, $devicesByTag);
		}

		return View('index', compact('total', 'devicesByTag'));
	}

	// 实时报警
	public function police()
	{
		$data = \DB::table('admin_user')
						->get();

		return response()->json($data);
	}
}
