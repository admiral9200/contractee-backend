<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Http\Requests\StoreProfileRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $profile = Profile::where('user_id', '=', auth()->user()->id)
                ->first();

            if ($profile) {
                $response = [
                    'status' => 'success',
                    'message' => "Found matched profile",
                    'data' => $profile
                ];
            } else {
                $response = [
                    'status' => 'success',
                    'message' => "Matched profile not found!",
                ];
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $ruleForPhone = [
                'phone' => ['required|string', 'regex:/^\+\d{1,3}-\d{3}-\d{3}-\d{4}$/']
            ];

            $validate = Validator::make($request->all(), [
                'job_title' => 'required|string',
                'name' => 'required|string',
                'surname' => 'required|string',
                // 'phone' => $ruleForPhone,
                'subscription_status' => 'required|string'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Profile validation error!',
                    'data' => $validate->errors()
                ], 403);
            }

            $profile = Profile::create([
                'user_id' => auth()->user()->id,
                'job_title' => $request->job_title,
                'name' => $request->name,
                'surname' => $request->surname,
                'phone' => $request->phone,
                'subscription_status' => $request->subscription_status
            ]);

            $data['profile'] = $profile;

            $response = [
                'status' => 'success',
                'message' => 'Profile was created successfully',
                'data' => $data
            ];

            return response()->json($response, 201);
        } catch (\Exception $e) {

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Profile $profile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Profile $profile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            $ruleForPhone = [
                'phone' => ['required|string', 'regex:/^\+\d{1,3}-\d{3}-\d{3}-\d{4}$/']
            ];

            $validate = Validator::make($request->all(), [
                'job_title' => 'required|string',
                'name' => 'required|string',
                'surname' => 'required|string',
                // 'phone' => $ruleForPhone,
                'subscription_status' => 'required|string'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Profile validation error!',
                    'data' => $validate->errors()
                ], 403);
            }

            $profile = Profile::where('user_id', '=', auth()->user()->id)
                ->first();

            $profile->update([
                'job_title' => $request->job_title,
                'name' => $request->name,
                'surname' => $request->surname,
                'phone' => $request->phone,
                'subscription_status' => $request->subscription_status
            ]);

            $data['profile'] = $profile;

            $response = [
                'status' => 'success',
                'message' => 'Profile was updated successfully',
                'data' => $data
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profile $profile)
    {
        //
    }
}
