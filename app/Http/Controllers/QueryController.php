<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use function PHPUnit\Framework\assertEqualsIgnoringCase;

class QueryController extends Controller
{
    function getTables()
    {

        $tables = \DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE' ORDER BY TABLE_NAME");
        return $tables;
    }


    function home()
    {
        return view('home', [
            'tables' => $this->getTables()
        ]);
    }

    function getData(Request $request)
    {
        $type = $request->get('type');

        $query = \DB::table($request->get('table'));
        $data = DataTables::query($query)->make(true);
        if($type && $type == 'columns') {
            $columns = collect($data->original['data'][0])->keys();
            $columns = $columns->map(function($col) {
                return (object)[
                    'data' => $col,
                    'name' => $col,
                    'title' => $col,
                ];
            });

            $response = [
                'columns' => $columns
            ];
        } else {
            $response = $data;
        }

        return $response;


    }


}
