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
        $header = $request->get('header');


        $query = \DB::table($request->get('table'))->take(200);
        if($header) {
            $query = $query->whereNotNull($header)->where($header, '!=',"");
        }

        $query = $query->get();
        $data = DataTables::Collection($query)->make(true);
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
