<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        return AddressResource::collection(Address::with('user')->paginate(20));
    }

    
    public function show(Address $address)
    {
        return new AddressResource($address->load('user'));
    }

}
