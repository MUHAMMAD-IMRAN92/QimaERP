<?php

namespace App\Http\Controllers;

use App\SystemDefination;
use Illuminate\Http\Request;

class SystemDefinationController extends Controller
{
    public function index()
    {
        $system_defination = SystemDefination::all();
        $genetices = collect();
        $flavours = collect();

        $genetics =   $system_defination->filter(function ($query) {
            return $query->key == 'genetic';
        });
        $flavours =   $system_defination->filter(function ($query) {
            return $query->key == 'flavour';
        });

        return view('admin.system_definations.index', [
            'genetics' =>   $genetics,
            'flavours' => $flavours
        ]);
    }
    public function create()
    {
        return view('admin.system_definations.create');
    }
    public function post(Request $request)
    {
        $request->validate([
            'key' => 'required',
            'value' => 'required'
        ]);
        SystemDefination::create([
            'key' => $request->key,
            'value' => $request->value
        ]);
        return redirect()->route('systemdefinition.index')->with('msg', ' System Definition Added');
    }

    public function delete(SystemDefination $genetic)
    {
        $genetic->delete();
        return redirect()->route('systemdefinition.index')->with('msg', ' System Definition Deleted');
    }
    public function edit(SystemDefination $genetic)
    {
        $defination =  $genetic;

        return view('admin.system_definations.edit', [
            'defination' => $defination
        ])->render();
    }
    public function update(Request $request, SystemDefination $genetic)
    {
        $genetic->update([
            'key' => $request->key,
            'value' => $request->value
        ]);
        return redirect()->route('systemdefinition.index')->with('msg', 'System Defination Updated');
    }
}
