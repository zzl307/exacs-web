<?php

namespace App\Http\Controllers;

use App\DeviceTypes;
use Illuminate\Http\Request;

class DeviceTypesController extends Controller
{   
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function home()
	{
		return View('device.types', [ 'types' => DeviceTypes::all() ]);
	}

	public function create()
	{
		if (!request()->isMethod('POST'))
			return View('devtype.create');

		$data = request()->input('DevType');

		if (!DeviceTypes::create($data))
		{
			$errmsg = '操作失败';
			return View('devtype.create', compact('data', 'errmsg'));
		}

		return View('device.types', [ 'types' => DeviceTypes::all() ]);
	}

	public function edit($id)
	{
		$row = DeviceTypes::find($id);
		if ($row == null)
			return View('device.types', [ 'types' => DeviceTypes::all() ]);

		if (request()->isMethod('POST'))
		{
			$data = request()->input('Data');
			$row->name = $data['name'];

			if (!$row->save())
			{
				$errmsg = '操作失败';
				return View('device.type_edit', compact('id', 'errmsg'));
			}

			return View('device.types', [ 'types' => DeviceTypes::all() ]);
		}

		return View('device.type_edit', compact('devtype'));
	}

	public function delete($id)
	{
		$devtype = DeviceTypes::find($id);
		if (!isset($devtype))
		{
			$errmsg = '数据已经不存在';
			return View('device.types', [ 'devtypes' => DeviceTypes::all(), 'errmsg' => $errmsg ]);
		}

		if (!$devtype->delete())
		{
			$errmsg = '操作失败';
			return View('devtype.home', [ 'devtypes' => DevType::all(), 'errmsg' => $errmsg ]);
		}

		return View('devtype.home', [ 'devtypes' => DevType::all() ]);
	}
}
