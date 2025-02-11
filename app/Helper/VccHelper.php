<?php
// Model
use App\Models\VccCredential;
use App\Models\Club;
use App\Models\Course;
use App\Models\PinRotationMarker;

if(!function_exists("loginVccVisageNet"))
{
    function loginVccVisageNet($clubName)
    {
        $loginApi = "https://vcc.visagenet.com/api/Account/JsonLogin";
        $cred = Club::where('site_name', $clubName)->first();
        if(!$cred) {
            //
            $res["success"] = false;
            $res["message"] = "There is no credential matched with the same club name.";
            $res["status"] = 400;
            return $res;
        }
        $credentials = [
            'UserName' => $cred ? $cred->username : '',
            'Password' => $cred ? $cred->password : ''
        ];
        //
        $response = Http::post($loginApi, $credentials);
        Log::info($response);
        if($response["success"]) {
            $headers = $response->headers();
            $cookie = $headers["Set-Cookie"][0];
            //
            $res["success"] = $response["success"];
            $res["cookie"] = $cookie;
            $res["cred"] = $cred;
            $res["status"] = 200;
        } else {
            $res["success"] = $response["success"];
            $res["message"] = "Invalid credential";
            $res["status"] = 400;
        }
        //
        return $res;
    }
}

if(!function_exists("loginBetaVisageNet"))
{
    function loginBetaVisageNet($clubName)
    {
        $loginApi = "https://beta.visagenet.com/api/Account/JsonLogin";
        $cred = Club::where('site_name', $clubName)->first();
        if(!$cred) {
            //
            $res["success"] = false;
            $res["message"] = "There is no credential matched with the same club name.";
            $res["status"] = 400;
            return $res;
        }
        $credentials = [
            'UserName' => $cred ? $cred->username : '',
            'Password' => $cred ? $cred->password : ''
        ];
        //
        $response = Http::post($loginApi, $credentials);
        Log::info($response);
        if($response["success"]) {
            $headers = $response->headers();
            $cookie = $headers["Set-Cookie"][0];
            //
            $res["success"] = $response["success"];
            $res["cookie"] = $cookie;
            $res["cred"] = $cred;
            $res["status"] = 200;
        } else {
            $res["success"] = $response["success"];
            $res["message"] = "Invalid credential";
            $res["status"] = 400;
        }
        //
        return $res;
    }
}


if(!function_exists("loginVccVisageGolf"))
{
    function loginVccVisageGolf($clubName)
    {
        $loginApi = "https://vcc.visage.golf/api/Account/JsonLogin";
        $cred = Club::where('site_name', $clubName)->first();
        if(!$cred) {
            //
            $res["success"] = false;
            $res["message"] = "There is no credential matched with the same club name.";
            $res["status"] = 400;
            return $res;
        }
        $credentials = [
            'UserName' => $cred ? $cred->username : '',
            'Password' => $cred ? $cred->password : ''
        ];
        //
        $response = Http::post($loginApi, $credentials);
        if($response["success"]) {
            $headers = $response->headers();
            $cookie = $headers["Set-Cookie"][0];
            //
            $res["success"] = $response["success"];
            $res["cookie"] = $cookie;
            $res["cred"] = $cred;
            $res["status"] = 200;
        } else {
            $res["success"] = $response["success"];
            $res["message"] = "Invalid credential";
            $res["status"] = 400;
        }
        //
        return $res;
    }
}


if(!function_exists("getPinPlacementsAtBetaVisageNet"))
{
    function getPinPlacementsAtBetaVisageNet($cookie, $siteId, $courseId) // $courseId = $courseNumber
    {
        // get
        $getApi = "https://beta.visagenet.com/api/VisageSpa/GreensWithPinPlacements";
        $getParams = [
            'theSiteId' => $siteId,
            'theCourseId' => $courseId
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'Cookie' => $cookie
        ])->get($getApi, $getParams);
        $orgData = $response->json();
        Log::info($getParams);
        Log::info($orgData);
        //
        return $orgData;
    }
}

if(!function_exists("getPinPlacementsAtBetaVisageNet"))
{
    function getPinPlacementsAtBetaVisageNet($cookie, $siteId, $courseId) // $courseId = $courseNumber
    {
        // get
        $getApi = "https://beta.visagenet.com/api/VisageSpa/GreensWithPinPlacements";
        $getParams = [
            'theSiteId' => $siteId,
            'theCourseId' => $courseId
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'Cookie' => $cookie
        ])->get($getApi, $getParams);
        $orgData = $response->json();
        Log::info($getParams);
        Log::info($orgData);
        //
        return $orgData;
    }
}

if(!function_exists("getPinPlacementsAtBetaVisageNet"))
{
    function getPinPlacementsAtBetaVisageNet($cookie, $siteId, $courseId) // $courseId = $courseNumber
    {
        // get
        $getApi = "https://beta.visagenet.com/api/VisageSpa/GreensWithPinPlacements";
        $getParams = [
            'theSiteId' => $siteId,
            'theCourseId' => $courseId
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'Cookie' => $cookie
        ])->get($getApi, $getParams);
        $orgData = $response->json();
        Log::info($getParams);
        Log::info($orgData);
        //
        return $orgData;
    }
}

if(!function_exists("getPinPlacementsAtBetaVisageNet"))
{
    function getPinPlacementsAtBetaVisageNet($cookie, $siteId, $courseId) // $courseId = $courseNumber
    {
        // get
        $getApi = "https://beta.visagenet.com/api/VisageSpa/GreensWithPinPlacements";
        $getParams = [
            'theSiteId' => $siteId,
            'theCourseId' => $courseId
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'Cookie' => $cookie
        ])->get($getApi, $getParams);
        $orgData = $response->json();
        Log::info($getParams);
        Log::info($orgData);
        //
        return $orgData;
    }
}

if(!function_exists("getPinPlacementsAtVisageNet"))
{
    function getPinPlacementsAtVisageNet($cookie, $siteId, $courseId) // $courseId = $courseNumber
    {
        // get
        $getApi = "https://vcc.visagenet.com/api/VisageSpa/GreensWithPinPlacements";
        $getParams = [
            'theSiteId' => $siteId,
            'theCourseId' => $courseId
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'Cookie' => $cookie
        ])->get($getApi, $getParams);
        $orgData = $response->json();
        Log::info($getParams);
        Log::info($orgData);
        //
        return $orgData;
    }
}


if(!function_exists("getPinPlacementsAtVisageGolf"))
{
    function getPinPlacementsAtVisageGolf($cookie, $siteId, $courseId) // $courseId = $courseNumber
    {
        // get
        $getApi = "https://vcc.visage.golf/api/VisageSpa/GreensWithPinPlacements";
        $getParams = [
            'theSiteId' => $siteId,
            'theCourseId' => $courseId
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'Cookie' => $cookie
        ])->get($getApi, $getParams);
        $orgData = $response->json();
        //
        return $orgData;
    }
}


if(!function_exists("getRotationSetTypeKeys"))
{
    function getRotationSetTypeKeys($RotationSets)
    {
        $keyArr = [];
        foreach($RotationSets as $RotationSet)
        {
            // RotationSetType
            if(in_array($RotationSet["RotationSetType"], $keyArr) === false) {
                array_push($keyArr, $RotationSet["RotationSetType"]);
            }
            $markers = [];
            // Rotation Marker
            foreach($RotationSet["Rotations"] as $Rotation) {

            }
        }
        return $keyArr;
    }
}

if(!function_exists("getRotationMarkersBySetType"))
{
    function getRotationMarkersBySetType($RotationSets, $RotationSetTypeKeys)
    {
        $RotationsBySetTypeKeys = [];
        foreach ($RotationSetTypeKeys as $RotationSetTypeKey) {
            $markers = [];
            foreach($RotationSets as $RotationSet)
            {
                // Rotation Marker
                foreach($RotationSet["Rotations"] as $Rotation) {
                    if($RotationSet["RotationSetType"] == $RotationSetTypeKey) {
                        // RotationSetType
                        array_push($markers, $Rotation["Marker"]);
                    }
                }
            }
            $_markerObj["RotationSetType"] = $RotationSetTypeKey;
            $_markerObj["markers"] = $markers;
            array_push($RotationsBySetTypeKeys, $_markerObj);
        }
        return $RotationsBySetTypeKeys;
    }
}


// if(!function_exists("getRotationMarkersBySetType"))
// {
//     function getRotationMarkersBySetType($RotationSets, $RotationSetTypeKeys)
//     {
//         $RotationsBySetTypeKeys = [];
//         foreach ($RotationSetTypeKeys as $RotationSetTypeKey) {
//             $markers = [];
//             foreach($RotationSets as $RotationSet)
//             {
//                 // Rotation Marker
//                 foreach($RotationSet["Rotations"] as $Rotation) {
//                     if($RotationSet["RotationSetType"] == $RotationSetTypeKey) {
//                         // RotationSetType
//                         array_push($markers, $Rotation["Marker"]);
//                     }
//                 }
//             }
//             $_RotationsBySetTypeKey = (object)[
//                 $RotationSetTypeKey => $markers
//             ];
//             // array_push($RotationsBySetTypeKeys, $_RotationsBySetTypeKey);
//             $RotationsBySetTypeKeys[$RotationSetTypeKey] = $_RotationsBySetTypeKey;
//         }
//         return (object)$RotationsBySetTypeKeys;
//     }
// }

if(!function_exists("getRotationsBySetType"))
{
    function getRotationsBySetType($ApproachViews, $rotationSetTypeMarkerKey)
    {
        $rotations = [];
        $RotationSetType = $rotationSetTypeMarkerKey["RotationSetType"];
        $markers = $rotationSetTypeMarkerKey["markers"];
        foreach ($markers as $index => $marker) 
        {
            $pinCoordinates = [];
            foreach($ApproachViews as $ApproachView)
            {
                $pinCoordinates["pin_coordinates"][$ApproachView["Hole"]] = getRotationCoordinates($ApproachView["PinPlacements"], $marker);
            }
            //
            // $rotations[$marker] = $pinCoordinates;
            $rotations[$index + 1] = $pinCoordinates;
        }
        //
        return $rotations;
    }
}


if(!function_exists("getRotationsArrBySetType"))
{
    function getRotationsArrBySetType($ApproachViews, $rotationSetTypeMarkerKey)
    {
        $rotations = [];
        $RotationSetType = $rotationSetTypeMarkerKey["RotationSetType"];
        $markers = $rotationSetTypeMarkerKey["markers"];
        foreach ($markers as $index => $marker) 
        {
            $pinCoordinates = [];
            foreach($ApproachViews as $ApproachView)
            {
                $pinCoordinates["pin_coordinates"][$ApproachView["Hole"]] = getRotationCoordinates($ApproachView["PinPlacements"], $marker);
            }
            //
            $rotations[$marker] = $pinCoordinates;
        }
        //
        return $rotations;
    }
}


if(!function_exists("getRotationCoordinates"))
{
    function getRotationCoordinates($pinPlacements, $marker)
    {
        $coordinates = [];
        foreach ($pinPlacements as $pinPlacement) {
            if($pinPlacement["Name"] == $marker) {
                $coordinates["lat"] = (string)$pinPlacement["Latitude"];
                $coordinates["long"] = (string)$pinPlacement["Longitude"];
            }
        }
        //
        return $coordinates;
    }
}

if(!function_exists("updateSetTypeAndMarker"))
{
    function updateSetTypeAndMarker($courseName, $rotationSetTypeMarkerKeys)
    {
        $markers = PinRotationMarker::where('course_name', $courseName)->get();
        foreach ($markers as $marker) {
            $_marker = PinRotationMarker::find($marker->id);
            if($_marker) {
                $_marker->delete();
            }
        }
        foreach ($rotationSetTypeMarkerKeys as $rotationSetTypeMarkerKey) {
            $set_type_id = $rotationSetTypeMarkerKey["RotationSetType"];
            $newMarkers = $rotationSetTypeMarkerKey["markers"];
            foreach ($newMarkers as $key => $newMarker) {
                PinRotationMarker::create(
                    [
                        'course_name' => $courseName,
                        'set_type_id' => $set_type_id,
                        'key' => $key + 1,
                        'marker' => $newMarker,
                    ]
                );
            }
        }
        //
        return true;
    }
}

// if(!function_exists("transformPoint")) {
//     function transformPoint($long, $lat, $WorldToImageTransform) {
//         $m = explode(",", $WorldToImageTransform);
//         $i = $long;
//         $r = $lat;
//         return $n = $i * $m[0] + $r * $m[2] + $m[4],
//         $t = $i * $m[1] + $r * $m[3] + $m[5],
//         [$n, $t]
//     }
// }

if(!function_exists("transformPointX")) {
    function transformPointX($long, $lat, $WorldToImageTransform) {
        $m = explode(",", $WorldToImageTransform);
        $m0 = (strpos($m[0], 'E') !== false) ? calcNumE($m[0]) : $m[0];
        $m2 = (strpos($m[2], 'E') !== false) ? calcNumE($m[2]) : $m[2];;
        $m4 = (strpos($m[4], 'E') !== false) ? calcNumE($m[4]) : $m[4];;
        $n = $long * $m0 + $lat * $m2 + $m4;
        return round($n);
    }
}

if(!function_exists("transformPointY")) {
    function transformPointY($long, $lat, $WorldToImageTransform) {
        $m = explode(",", $WorldToImageTransform);
        $m1 = (strpos($m[1], 'E') !== false) ? calcNumE($m[1]) : $m[1];
        $m3 = (strpos($m[3], 'E') !== false) ? calcNumE($m[3]) : $m[3];
        $m5 = (strpos($m[5], 'E') !== false) ? calcNumE($m[5]) : $m[5];
        $t = $long * $m1 + $lat * $m3 + $m5;
        return round($t);
    }
}

if(!function_exists("calcNumE")) {
    function calcNumE($numStr) {
        $m = explode("E", $numStr);
        return $m[0] * pow(10, intval($m[1]));
    }
}

if(!function_exists("calcDistanceOf2Points")) {
    function calcDistanceOf2Points($lati1, $long1, $lati2, $long2) {
        $step1 = deg2rad(90 - $lati2);
        $step2 = cos($step1);
        $step3 = deg2rad(90-$lati1);
        $step4 = cos($step3);
        $step5 = $step4 * $step2;
        $step6 = deg2rad(90 - $lati2);
        $step7 = sin($step6);
        $step8 = deg2rad(90 - $lati1);
        $step9 = sin($step8);
        $step10 = $step9 * $step7;
        $step11 = deg2rad($long2 - $long1);
        $step12 = cos($step11);
        $step13 = $step12 * $step10;
        $step14 = $step13 + $step5;
        $step15 = acos($step14);
        $step16 = $step15 * 6371;
        $step17 = $step16 / 1.609;
        $step18 = $step17 * 1760;
    
        return $step18;
    }
}

if(!function_exists("getImageHeightYards")) {
    function getImageHeightYards($lati1, $long1, $lati2, $long2) {
        $distance = calcDistanceOf2Points($lati1, $long1, $lati2, $long2);
        $height =  pow(((pow($distance, 2))/2), 0.5);
        return $height;
    }
}

if(!function_exists("getImageWidthYards")) {
    function getImageWidthYards($lati1, $long1, $lati2, $long2) {
        $distance = calcDistanceOf2Points($lati1, $long1, $lati2, $long2);
        $width =  pow(((pow($distance, 2))/2), 0.5);
        return $width;
    }
}

if(!function_exists("getOrientation")) {
    function getOrientation($lati1, $long1, $lati2, $long2) {
        $f2 = fmod(
            rad2deg(
                atan2(
                    (sin(deg2rad($long2 - $long1)) * cos(deg2rad($lati2)))
                    , (cos(deg2rad($lati1)) * sin(deg2rad($lati2)) - sin(deg2rad($lati1)) * cos(deg2rad($lati2)) * cos(deg2rad($long2 - $long1)))
                )
            )
            , 360
        );

        $orientation = fmod((135 - $f2), 360);
        $orientation = round($orientation * 10) / 10;
        
        return $orientation;
    }
}