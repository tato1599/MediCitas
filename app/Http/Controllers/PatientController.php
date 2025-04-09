<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        return view('patients.index');
    }

    public function create()
    {
        return view('patients.create');
    }

    public function show($id)
    {
        return view('patients.show', ['id' => $id]);
    }

    public function edit($id)
    {
        return view('patients.edit', ['id' => $id]);
    }
}
