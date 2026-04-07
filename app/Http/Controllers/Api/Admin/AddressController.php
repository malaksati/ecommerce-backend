<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        return Address::with('user')->paginate(20);
    }

    
    public function show(Address $address)
    {
        return $address->load('user');
    }

}
