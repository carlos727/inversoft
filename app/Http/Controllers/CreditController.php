<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use App\Credit;
use App\Client;
use App\Collector;
use Validator;

class CreditController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application index.
	 */
	public function index()
	{
		return view('home',[
			'credits' => Credit::where('active', 1)->get(),
			'clients' => Client::all(),
			'collectors' => Collector::all()
		]);
	}

	public function create()
	{
		if (Collector::all()->count() > 0) {
			return view('credit', [
				'clients' => Client::orderBy('name', 'asc')->get(),
				'collectors' => Collector::orderBy('name', 'asc')->get()
			]);
		}
		else {
			return redirect()->action('CreditController@index')->with('alert', 'Error: No hay cobradores registrados para asignar creditos.');
		}
	}

	/**
	 * Store a new credit.
	 */
	public function store(Request $request)
	{
		$regex = "/^\d+((\.|\,)\d+)?$/";

		$validator = Validator::make($request->all(), [
			'collector_id' =>  'required|numeric|min:1',
			'client_id' =>  'required|numeric|min:1',
			'value'		=>	'required|numeric|min:1',
			'fee'		=>	'required|numeric|min:1',
			'type'		=>	'required|digits_between:0,3',
			'revenue'	=>	'required|numeric',//|regex:'.$regex,
			'start_at'	=>	'required|date'
		]);

		if ($validator->fails()) {
			return redirect()
				->action('CreditController@create')
				->withInput()
				->withErrors($validator->errors());
		}

		$credit = new Credit;
		$credit->collector_id = $request->input('collector_id');
		$credit->client_id    = $request->input('client_id');
		$credit->value		  = $request->input('value');
		$credit->fee		  = $request->input('fee');
		$credit->type		  = $request->input('type');
		$credit->revenue	  = $request->input('revenue');
		$credit->start_at	  = $request->input('start_at');
		$credit->save();

		return redirect()->action('CreditController@index')->with('message', 'Exito: Crédito creado!');
	}

	/**
	 * Delete a credit.
	 */
	public function delete($id)
	{
		$count = Credit::findOrFail($id)->payments->count();

		if ($count == 0) {
			Credit::findOrFail($id)->delete();
			return redirect()->action('CreditController@index')->with('message', 'Exito: Crédito eliminado!');
		}
		else {
			return redirect()->action('CreditController@index')->with('alert', 'Error: El crédito tiene pagos asociados.');
		}
	}

	/**
	 * Update a credit.
	 */
	public function update($id, Request $request)
	{
		$credit = Credit::findOrFail($id);

		if ($credit->payments->count() == 0) {

			$regex = "/^\d+((\.|\,)\d+)?$/";

			$validator = Validator::make($request->all(), [
				'client_id' =>  'required|numeric|min:1',
				'value'     =>  'required|numeric|min:1',
				'fee'       =>  'required|numeric|min:1',
				'type'      =>  'required|digits_between:0,3',
				'revenue'   =>  'required|numeric',//|regex:'.$regex,
				'start_at'  =>  'required|date'
			]);

			if ($validator->fails()) {
				return redirect()
					->action('CreditController@create')
					->withInput()
					->withErrors($validator->errors());
			}

			$credit->client_id  = $request->input('client_id');
			$credit->value      = $request->input('value');
			$credit->fee        = $request->input('fee');
			$credit->type       = $request->input('type');
			$credit->revenue    = $request->input('revenue');
			$credit->start_at   = $request->input('start_at');
			$credit->save();

			return redirect()->action('CreditController@index')->with('message', 'Exito: Datos del crédito actualizados!');
		}
		else {
			return redirect()->action('CreditController@index')->with('alert', 'Error: El crédito tiene pagos asociados.');
		}
	}
}