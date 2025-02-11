<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Log;
// Model
// use App\Models\VccCredential;
use App\Models\Club;
use App\Models\Course;

class TestController extends Controller
{
    //
    public function syncPinRotationsByClub(Request $request)
    {
        $courseName = $request->course_name ? $request->course_name : '';
        if(!$courseName) {
            return;
        }
        $res = loginVcc($courseName); // helper function
        if($res["success"]) {
            $cred = $res["cred"];
            $cookie = $res["cookie"];
            // get PinPlacements data
            $orgData = getPinPlacements($cred, $cookie);
            // dd('********', $orgData);
            // make the data template for dynamodb
            $RotationSets = $orgData["RotationSets"];
            $ApproachViews = $orgData["ApproachViews"];
            $rotationSetTypeKeys = getRotationSetTypeKeys($RotationSets); // array
            $rotationSetTypeMarkerKeys = getRotationMarkersBySetType($RotationSets, $rotationSetTypeKeys); // array
            // dd('********', $rotationSetTypeMarkerKeys);
            if(updateSetTypeAndMarker($courseName, $rotationSetTypeMarkerKeys))
            {
                // step 1.
                $_rotationSets = [];
                foreach($rotationSetTypeMarkerKeys as $rotationSetTypeMarkerKey)
                {
                    $_rotationSets[$rotationSetTypeMarkerKey["RotationSetType"]] = getRotationsBySetType($ApproachViews, $rotationSetTypeMarkerKey);
                }
                // dd('********', $_rotationSets);
                $rotationData = array(
                    "rotations" => $_rotationSets,
                    "time_zone" => "America/New York"
                );
                // // step 2.
                // $dataTemple["parent_course_name"] = $courseName;
                // $dataTemple["child_course_names"] = array(
                //     $courseName => $rotationData
                // );
                // dd('***********', $rotationData);
                // call aws lambda function
                $lambdaApi = "https://5yjcrgi025.execute-api.us-east-1.amazonaws.com/accupin_dev/syncPinRotations";
                $params = [
                    "course_name" => $courseName,
                    "data" => json_encode($rotationData),
                ];
                // dd("=== generated params === ", $orgData, $params);
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => '*/*'
                ])->post($lambdaApi, $params);
                $res = $response->json();
                if($res && isset($res["statusCode"]) && $res["statusCode"] == 200) {
                    // dd('***********', $res["statusCode"]);
                    Log::info('**************** ' . $courseName . ' AWS Table Updating Successed ******************');
                    return response()->json(['status' => 200, 'message' => 'AWS Table Updating Successed'], 200);
                } else {
                    Log::info('**************** ' . $courseName . ' AWS Table Updating Failed ******************');
                    return response()->json(['status' => 400, 'message' => 'AWS Table Updating Failed'], 200);
                }
            } else {
                Log::info('**************** ' . $courseName . ' Local Table Updating Failed ******************');
                return response()->json(['status' => 400, 'message' => 'Local Table Updating Failed'], 200);
            }
        } else {
            Log::info('**************** ' . $courseName . ' Failed Authorization ******************');
            return response()->json(['status' => 400, 'message' => 'Failed Authorization'], 200);
        }
        return response()->json(['status' => 200, 'message' => 'executed cmd'], 200);
    }
    public function dbFix(Request $request)
    {
        $courses = Course::with('club')->get();
        if(count($courses)) {
            foreach ($courses as $course) {
                $site_id = $course->club->site_id;
                $_course = Course::find($course->id);
                $_course->site_id = $site_id;
                $_course->save();
            }
        }
        return response()->json(["status" => 200, "message" => "Success"]);
    }
}
