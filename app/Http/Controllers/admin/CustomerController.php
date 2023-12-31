<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Imports\ImportFileExcel;
use App\Models\Customer;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;



class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function upload_file(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx,csv|max:51200',
        ]);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data = Excel::toCollection(new ImportFileExcel, $file)->first();
            if ($data->count() > 0) {
                // create customers
                $data->shift();
                foreach ($data as $row) {
                    $purchaseItem = new Customer([
                        'fullname' => $row[0],
                        'address' => $row[1],
                        'phone' => $row[2],
                        'debt' => 0,
                        'location' => 0
                    ]);
                    $purchaseItem->save();
                }
                return response()->json(['message' => 'upload file successful'], 201);
            }
        } else {
            return response()->json(['message' => 'upload file Failed'], 400);
        }
    }
    public function get_debt($id)
    {
        $customer = Customer::find($id);
        return response()->json($customer, 200);
    }
    public function update_debt(Request $request, $id)
    {
        if ($request) {
            $item = Customer::findOrFail($id);
            $item->debt += $request->debt;
            $item->save();
            return response()->json(['update successful', 200]);
        } else {
            return response()->json(['update faild', 401]);
        }
    }
    public function index()
    {
        $list_customers = DB::table('customers')
            ->select('customers.*', DB::raw('COUNT(sales.customer_id) as total_orders'))
            ->leftJoin('sales', 'customers.id', '=', 'sales.customer_id')
            ->groupBy('customers.id', 'customers.fullname', 'customers.address', 'customers.phone', 'customers.debt', 'customers.location', 'customers.created_at', 'customers.updated_at')
            ->get();
        $list_location = Location::select('id', 'name', 'desc')->get();
        $response = [
            'data' => $list_customers,
            'location' => $list_location,
        ];
        return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request) {
            $request->validate(
                [
                    'fullname' => 'required',
                    'address' => 'required',
                ]
            );
            $data = [
                'fullname' => $request->fullname,
                'address' => $request->address,
                'phone' => $request->phone,
                'debt' => $request->debt,
                'location' => $request->location,
            ];
            Customer::create($data);
            return response()->json(['successful ', 201]);
        }
        return response()->json(['faild ', 401]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::find($id);
        return response()->json($customer, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item = Customer::findOrFail($id);
        if ($item != null) {
            $data = $request->all();
            $item = Customer::findOrFail($id);
            $item->update($data);
            return response()->json(['successful ', 200]);
        } else {
            return response()->json(['successful ', 200]);
        }
        return response()->json(['faild ', 401]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
