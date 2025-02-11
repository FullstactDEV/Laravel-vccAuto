<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
// Model
use App\Models\VccCredential;
use App\Models\PinRotationMarker;
use App\Models\Club;
use App\Models\Course;
// Mail
use Mail;
use App\Mail\SubscriptionMails;

class ApiController extends Controller
{
    private $centersNet = [
        [
            'Hole' => 1,
            'X' => 348,
            'Y' => 276,
        ],
        [
            'Hole' => 2,
            'X' => 387,
            'Y' => 255,
        ],
        [
            'Hole' => 3,
            'X' => 348,
            'Y' => 270,
        ],
        [
            'Hole' => 4,
            'X' => 347,
            'Y' => 274,
        ],
        [
            'Hole' => 5,
            'X' => 385,
            'Y' => 293,
        ],
        [
            'Hole' => 6,
            'X' => 352,
            'Y' => 255,
        ],
        [
            'Hole' => 7,
            'X' => 377,
            'Y' => 278,
        ],
        [
            'Hole' => 8,
            'X' => 341,
            'Y' => 275,
        ],
        [
            'Hole' => 9,
            'X' => 365,
            'Y' => 271,
        ],
        [
            'Hole' => 10,
            'X' => 380,
            'Y' => 277,
        ],
        [
            'Hole' => 11,
            'X' => 368,
            'Y' => 275,
        ],
        [
            'Hole' => 12,
            'X' => 374,
            'Y' => 264,
        ],
        [
            'Hole' => 13,
            'X' => 338,
            'Y' => 253,
        ],
        [
            'Hole' => 14,
            'X' => 354,
            'Y' => 279,
        ],
        [
            'Hole' => 15,
            'X' => 346,
            'Y' => 272,
        ],
        [
            'Hole' => 16,
            'X' => 364,
            'Y' => 273,
        ],
        [
            'Hole' => 17,
            'X' => 357,
            'Y' => 281,
        ],
        [
            'Hole' => 18,
            'X' => 341,
            'Y' => 271,
        ]
    ];

    private $centersGolf = [
        [
            'Hole' => 1,
            'X' => 535,
            'Y' => 255,
        ],
        [
            'Hole' => 2,
            'X' => 495,
            'Y' => 225,
        ],
        [
            'Hole' => 3,
            'X' => 510,
            'Y' => 245,
        ],
        [
            'Hole' => 4,
            'X' => 380,
            'Y' => 215,
        ],
        [
            'Hole' => 5,
            'X' => 445,
            'Y' => 245,
        ],
        [
            'Hole' => 6,
            'X' => 340,
            'Y' => 220,
        ],
        [
            'Hole' => 7,
            'X' => 485,
            'Y' => 210,
        ],
        [
            'Hole' => 8,
            'X' => 415,
            'Y' => 245,
        ],
        [
            'Hole' => 9,
            'X' => 468,
            'Y' => 243,
        ],
        [
            'Hole' => 10,
            'X' => 450,
            'Y' => 265,
        ],
        [
            'Hole' => 11,
            'X' => 445,
            'Y' => 270,
        ],
        [
            'Hole' => 12,
            'X' => 475,
            'Y' => 210,
        ],
        [
            'Hole' => 13,
            'X' => 415,
            'Y' => 215,
        ],
        [
            'Hole' => 14,
            'X' => 450,
            'Y' => 217,
        ],
        [
            'Hole' => 15,
            'X' => 450,
            'Y' => 272,
        ],
        [
            'Hole' => 16,
            'X' => 395,
            'Y' => 195,
        ],
        [
            'Hole' => 17,
            'X' => 440,
            'Y' => 215,
        ],
        [
            'Hole' => 18,
            'X' => 470,
            'Y' => 187,
        ]
    ];

    private $centersGolf2 = [
        [
            'Hole' => 1,
            'X' => 436,
            'Y' => 258,
        ],
        [
            'Hole' => 2,
            'X' => 405,
            'Y' => 202,
        ],
        [
            'Hole' => 3,
            'X' => 400,
            'Y' => 256,
        ],
        [
            'Hole' => 4,
            'X' => 353,
            'Y' => 246,
        ],
        [
            'Hole' => 5,
            'X' => 429,
            'Y' => 263,
        ],
        [
            'Hole' => 6,
            'X' => 430,
            'Y' => 289,
        ],
        [
            'Hole' => 7,
            'X' => 436,
            'Y' => 303,
        ],
        [
            'Hole' => 8,
            'X' => 423,
            'Y' => 254,
        ],
        [
            'Hole' => 9,
            'X' => 363,
            'Y' => 318,
        ],
        [
            'Hole' => 10,
            'X' => 425,
            'Y' => 271,
        ],
        [
            'Hole' => 11,
            'X' => 454,
            'Y' => 271,
        ],
        [
            'Hole' => 12,
            'X' => 456,
            'Y' => 260,
        ],
        [
            'Hole' => 13,
            'X' => 410,
            'Y' => 281,
        ],
        [
            'Hole' => 14,
            'X' => 398,
            'Y' => 262,
        ],
        [
            'Hole' => 15,
            'X' => 459,
            'Y' => 254,
        ],
        [
            'Hole' => 16,
            'X' => 364,
            'Y' => 303,
        ],
        [
            'Hole' => 17,
            'X' => 384,
            'Y' => 252,
        ],
        [
            'Hole' => 18,
            'X' => 358,
            'Y' => 248,
        ]
    ];

    private function calcX($index, $up, $down, $left, $right) {
        $unitsLeft = [5.8, 6.2, 5.6, 6.5, 6.5, 6.8, 5.8, 6.2, 4.8, 6.2, 6, 6.4, 5.7, 5.7, 5.4, 7, 5.6, 6.8];
        $unitsRight = [6, 6.5, 5.6, 6.8, 6.5, 6.6, 5.3, 6.1, 4.6, 6.2, 6, 6.4, 5.8, 5.9, 5.2, 6.9, 5.6, 6.4];
        $unit = $left ? $unitsLeft[$index] : $unitsRight[$index];
        $orgX = $this->centers[$index]['X'];
        return round($orgX - ($left * $unit) + ($right * $unit));
    }

    private function calcY($index, $up, $down, $left, $right) {
        $unitsUp = [6.5, 6.2, 6.1, 5.2, 5.8, 5.2, 5.4, 6.2, 5.4, 5.6, 5.7, 7.4, 6.2, 5.2, 5.4, 6.6, 6.6, 6.4];
        $unitsDown = [6.6, 5.8, 6.2, 5.0, 5.6, 5.3, 5.5, 6.3, 5.45, 5.75, 5.8, 7.2, 6.65, 5.1, 5.4, 6.6, 6.6, 6.5];
        $unit = $up ? $unitsUp[$index] : $unitsDown[$index];
        $orgY = $this->centers[$index]['Y'];
        return round($orgY - ($up * $unit) + ($down * $unit));
    }

    /********************************************************
     * @ SaveCourseRotationSetsByClub
     * @ params (course_name, pin_number, rotation, latitude, longitude, rotation_set_type, marker<1, 2, ..., A, B, ...>)
    ********************************************************/
    public function SaveCourseRotationSetsByClub(Request $request) // All Clubs
    {
        //
        $validator = Validator::make($request->all(), [
            'club_name' => 'required',
            'course_name' => 'required',
            'rotation' => 'required',
            'pin_number' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'rotation_set_type' => 'required', // new
            'marker' => 'required', // new
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => "Required params not passed.", 'status' => 400]);
        }
        $args["clubName"] = $request->club_name;
        $args["courseName"] = $request->course_name;
        $args["pinNumber"] = $request->pin_number;
        $args["rotationNumber"] = $request->rotation;
        $args["latitude"] = $request->latitude;
        $args["longitude"] = $request->longitude;
        $args["rotationSetType"] = $request->rotation_set_type; // * new
        $args["marker"] = $request->marker; // * new
        $args["up"] = $request->up ? $request->up : 0;
        $args["down"] = $request->down ? $request->down : 0;
        $args["left"] = $request->left ? $request->left : 0;
        $args["right"] = $request->right ? $request->right : 0;
        //
        $result = [];
        // $club = Club::where('site_name', $args["clubName"])->first();
        $courses = Course::where('site_name', $args["clubName"])
                    ->where('course_name', $args["courseName"])
                    ->get();
        $asClubName = true;
        if(!count($courses)) {
            // $club = Club::where('site_name', $args["courseName"])->first();
            $courses = Course::where('site_name', $args["courseName"])
                    ->where('course_name', $args["clubName"])
                    ->get();
            $asClubName = false;
        }

        // executing Unit
        if(!count($courses)) {
            $result["success"] = false;
            $result["message"] = "There is no matched club name.";
            $result["status"] = 400;
        }
        // 
        foreach($courses as $course) {
            $club = Club::find($course->club_id);
            // 
            
            if($club && $club->domain_type == "betavisagenet") {
                $result = $this->callSaveCourseRotationSetsByClubAtBetaVisageNet($args, $asClubName);
            } else if($club && $club->domain_type == "visagenet") {
                $result = $this->callSaveCourseRotationSetsByClubAtVisageNet($args, $asClubName);
            } else if($club && $club->domain_type == "visagegolf") {
                $result = $this->callSaveCourseRotationSetsByClubAtVisageGolf($args);
            } else {
                $result["success"] = false;
                $result["message"] = "There is no matched club name.";
                $result["status"] = 400;
            }
        }
        // 
        // if($club && $club->domain_type == "visagenet") {
        //     $result = $this->callSaveCourseRotationSetsByClubAtVisageNet($args, $asClubName);
        // } else if($club && $club->domain_type == "visagegolf") {
        //     $result = $this->callSaveCourseRotationSetsByClubAtVisageGolf($args);
        // } else {
        //     $result["success"] = false;
        //     $result["message"] = "There is no matched club name.";
        //     $result["status"] = 400;
        // }

        return response()->json($result);
        
    }

    // beta.visagenet.com
    private function callSaveCourseRotationSetsByClubAtBetaVisageNet($args, $asClubName) {
        //
        $clubName = $asClubName ? $args["clubName"] : $args["courseName"];
        $courseName = $asClubName ? $args["courseName"] : $args["clubName"];
        $pinNumber = $args["pinNumber"];
        $rotationNumber = $args["rotationNumber"];
        $latitude = $args["latitude"];
        $longitude = $args["longitude"];
        $rotationSetType = $args["rotationSetType"];
        $marker = $args["marker"];
        $up = $args["up"];
        $down = $args["down"];
        $left = $args["left"];
        $right = $args["right"];
        // Login & Get Cookie
        $res = loginBetaVisageNet($clubName); // helper function
        if($res["success"]) {
            $cred = $res["cred"];
            $cookie = $res["cookie"];
            $course = Course::whereHas('club', function($q) {
                                $q->where('domain_type', 'betavisagenet');
                            })
                            ->where('site_name', $clubName)
                            ->where('course_name', $courseName)
                            ->first();
            if(!$course) {
                $result["success"] = false;
                $result["message"] = "There is no matched course.";
                $result["status"] = 400;
                return $result;
            }
            $courseNumber = $course->course_number;
            $siteId = $course->club->site_id;
            // get PinPlacements data
            $orgData = getPinPlacementsAtBetaVisageNet($cookie, $siteId, $courseNumber);
            // save
            $saveApi = "https://beta.visagenet.com/api/VisageSpa/SaveCourseRotationSets";
            // Rotations
            $RotationSets = $orgData["RotationSets"]; // size = 1
            $RotationSetsArr = [];
            $ApproachViews = $orgData["ApproachViews"]; // size = 18
            foreach($RotationSets as $index => $RotationsSet) {
                // 
                $Rotations = $RotationsSet["Rotations"]; // size = 3
                $RotationsArr = [];
                foreach($Rotations as $iindex => $Rotation) { // *********************** rotation
                    $rotationMarker = '';
                    // if($RotationsSet["RotationSetType"] == $rotationSetType) {
                        $rotationMarker = $Rotation["Marker"];
                    // }
                    $PinPlacementsArr = [];
                    // **************
                    foreach($ApproachViews as $iiindex => $ApproachView) { // *********************** pin_number * hole
                        // dd('===', $iindex, count($ApproachView["PinPlacements"]));
                        if ($iindex < count($ApproachView["PinPlacements"])) {
                            $_PinPlacement = [
                                "Usage" => $ApproachView["PinPlacements"][$iindex]["Usage"],
                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                "Hole" => $ApproachView["Hole"],
                                "Latitude" => $ApproachView["PinPlacements"][$iindex]["Latitude"], // input param
                                "Longitude" => $ApproachView["PinPlacements"][$iindex]["Longitude"], // input param
                                "X" => $ApproachView["PinPlacements"][$iindex]["X"],
                                "Y" => $ApproachView["PinPlacements"][$iindex]["Y"]
                            ];
                            // updated param
                            // if($Rotation["RotationNumber"] == $rotationNumber && $ApproachView["Hole"] == $pinNumber) { // **** need to obtain
                                // 
                                foreach($ApproachView["PinPlacements"] as $PinPlacement) {
                                    if($rotationMarker == $PinPlacement['Name']) {
                                        if($ApproachView["Hole"] == $pinNumber && $PinPlacement['Name'] == $marker) {
                                            $_PinPlacement = [
                                                "Usage" => $PinPlacement["Usage"],
                                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                                "Hole" => $ApproachView["Hole"],
                                                "Latitude" => $latitude, // input param
                                                "Longitude" => $longitude, // input param
                                                // "X" => ($this->calcNetPoiXFromCenter($ApproachView["PoiData"], $iiindex, $up, $down, $left, $right) > 100) ? $this->calcNetPoiXFromCenter($ApproachView["PoiData"], $iiindex, $up, $down, $left, $right) : transformPointX($longitude, $latitude, $ApproachView["WorldToImageTransform"]),
                                                // "Y" => ($this->calcNetPoiYFromCenter($ApproachView["PoiData"], $iiindex, $up, $down, $left, $right) > 100) ? $this->calcNetPoiYFromCenter($ApproachView["PoiData"], $iiindex, $up, $down, $left, $right) : transformPointY($longitude, $latitude, $ApproachView["WorldToImageTransform"])
                                                "X" => transformPointX($longitude, $latitude, $ApproachView["WorldToImageTransform"]),
                                                "Y" => transformPointY($longitude, $latitude, $ApproachView["WorldToImageTransform"])
                                            ];
                                        }
                                        else {
                                            $_PinPlacement = [
                                                "Usage" => $PinPlacement["Usage"],
                                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                                "Hole" => $ApproachView["Hole"],
                                                "Latitude" => $PinPlacement["Latitude"], // input param
                                                "Longitude" => $PinPlacement["Longitude"], // input param
                                                "X" => $PinPlacement["X"],
                                                "Y" => $PinPlacement["Y"]
                                            ];
                                        }
                                        
                                    }
                                }
                            // }
                        } else {
                            $_PinPlacement = [
                                "Usage" => $ApproachView["PinPlacements"][0]["Usage"],
                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                "Hole" => $ApproachView["Hole"],
                                "Latitude" => 0, // input param
                                "Longitude" => 0, // input param
                                "X" => 0,
                                "Y" => 0
                            ];
                        }
                        array_push($PinPlacementsArr, $_PinPlacement);
                    }
                    // ********************
                    $_Rotation = [
                        "RotationNumber" => $Rotation["RotationNumber"],
                        "RotationType" => $Rotation["RotationType"],
                        "PinPlacements" => $PinPlacementsArr
                    ];
                    array_push($RotationsArr, $_Rotation);
                }
                //
                $_RotationSet = [
                    "Course" => $orgData["Course"],
                    "CurrentRotation" => $RotationsSet["CurrentRotation"],
                    "FirstHole" => $RotationsSet["FirstHole"],
                    "IsActive" => $RotationsSet["ActiveSet"],
                    "LastHole" => $RotationsSet["LastHole"],
                    "Name" => $RotationsSet["Name"],
                    "RotationSetType" => $RotationsSet["RotationSetType"],
                    "PinCharacter" => $RotationsSet["UserFigure"],
                    "Rotations" => $RotationsArr
                ];
                array_push($RotationSetsArr, $_RotationSet);
            }
            
            //
            $params = [
                "SiteId" => $siteId,
                "CourseRotationSetsDto" => [
                    "Version" => $orgData["Version"],
                    "Course" => $orgData["Course"],
                    "IsOneDayOverride" => $orgData["IsOneDayOverride"],
                    "RotationSets" => $RotationSetsArr
                ],
                "Log" => "Move pins"
            ];
            $response2 = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'Cookie' => $cookie
            ])->post($saveApi, $params);

            // return response()->json(['result' => $response2->json(), 'success' => $response2->successful(), 'status' => $response2->status()]);
            $result["success"] = $response2->successful();
            $result["message"] = "";
            $result["status"] = 200;
            $result["result"] = $response2->json();
        } else {
            // return response()->json(['message' => 'An error has occured', 'success' => false, 'status' => 400]);
            $result = $res;
        }

        return $result;
    }

    // vcc.visagenet.com
    private function callSaveCourseRotationSetsByClubAtVisageNet($args, $asClubName) {
        //
        $clubName = $asClubName ? $args["clubName"] : $args["courseName"];
        $courseName = $asClubName ? $args["courseName"] : $args["clubName"];
        $pinNumber = $args["pinNumber"];
        $rotationNumber = $args["rotationNumber"];
        $latitude = $args["latitude"];
        $longitude = $args["longitude"];
        $rotationSetType = $args["rotationSetType"];
        $marker = $args["marker"];
        $up = $args["up"];
        $down = $args["down"];
        $left = $args["left"];
        $right = $args["right"];
        // Login & Get Cookie
        $res = loginVccVisageNet($clubName); // helper function
        if($res["success"]) {
            $cred = $res["cred"];
            $cookie = $res["cookie"];
            $course = Course::whereHas('club', function($q) {
                                $q->where('domain_type', 'visagenet');
                            })
                            ->where('site_name', $clubName)
                            ->where('course_name', $courseName)
                            ->first();
            if(!$course) {
                $result["success"] = false;
                $result["message"] = "There is no matched course.";
                $result["status"] = 400;
                return $result;
            }
            $courseNumber = $course->course_number;
            $siteId = $course->club->site_id;
            // get PinPlacements data
            $orgData = getPinPlacementsAtVisageNet($cookie, $siteId, $courseNumber);
            // save
            $saveApi = "https://vcc.visagenet.com/api/VisageSpa/SaveCourseRotationSets";
            // Rotations
            $RotationSets = $orgData["RotationSets"]; // size = 1
            $RotationSetsArr = [];
            $ApproachViews = $orgData["ApproachViews"]; // size = 18
            foreach($RotationSets as $index => $RotationsSet) {
                // 
                $Rotations = $RotationsSet["Rotations"]; // size = 3
                $RotationsArr = [];
                foreach($Rotations as $iindex => $Rotation) { // *********************** rotation
                    $rotationMarker = '';
                    // if($RotationsSet["RotationSetType"] == $rotationSetType) {
                        $rotationMarker = $Rotation["Marker"];
                    // }
                    $PinPlacementsArr = [];
                    // **************
                    foreach($ApproachViews as $iiindex => $ApproachView) { // *********************** pin_number * hole
                        // dd('===', $iindex, count($ApproachView["PinPlacements"]));
                        if ($iindex < count($ApproachView["PinPlacements"])) {
                            $_PinPlacement = [
                                "Usage" => $ApproachView["PinPlacements"][$iindex]["Usage"],
                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                "Hole" => $ApproachView["Hole"],
                                "Latitude" => $ApproachView["PinPlacements"][$iindex]["Latitude"], // input param
                                "Longitude" => $ApproachView["PinPlacements"][$iindex]["Longitude"], // input param
                                "X" => $ApproachView["PinPlacements"][$iindex]["X"],
                                "Y" => $ApproachView["PinPlacements"][$iindex]["Y"]
                            ];
                            // updated param
                            // if($Rotation["RotationNumber"] == $rotationNumber && $ApproachView["Hole"] == $pinNumber) { // **** need to obtain
                                // 
                                foreach($ApproachView["PinPlacements"] as $PinPlacement) {
                                    if($rotationMarker == $PinPlacement['Name']) {
                                        if($ApproachView["Hole"] == $pinNumber && $PinPlacement['Name'] == $marker) {
                                            $_PinPlacement = [
                                                "Usage" => $PinPlacement["Usage"],
                                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                                "Hole" => $ApproachView["Hole"],
                                                "Latitude" => $latitude, // input param
                                                "Longitude" => $longitude, // input param
                                                // "X" => ($this->calcNetPoiXFromCenter($ApproachView["PoiData"], $iiindex, $up, $down, $left, $right) > 100) ? $this->calcNetPoiXFromCenter($ApproachView["PoiData"], $iiindex, $up, $down, $left, $right) : transformPointX($longitude, $latitude, $ApproachView["WorldToImageTransform"]),
                                                // "Y" => ($this->calcNetPoiYFromCenter($ApproachView["PoiData"], $iiindex, $up, $down, $left, $right) > 100) ? $this->calcNetPoiYFromCenter($ApproachView["PoiData"], $iiindex, $up, $down, $left, $right) : transformPointY($longitude, $latitude, $ApproachView["WorldToImageTransform"])
                                                "X" => transformPointX($longitude, $latitude, $ApproachView["WorldToImageTransform"]),
                                                "Y" => transformPointY($longitude, $latitude, $ApproachView["WorldToImageTransform"])
                                            ];
                                        }
                                        else {
                                            $_PinPlacement = [
                                                "Usage" => $PinPlacement["Usage"],
                                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                                "Hole" => $ApproachView["Hole"],
                                                "Latitude" => $PinPlacement["Latitude"], // input param
                                                "Longitude" => $PinPlacement["Longitude"], // input param
                                                "X" => $PinPlacement["X"],
                                                "Y" => $PinPlacement["Y"]
                                            ];
                                        }
                                        
                                    }
                                }
                            // }
                        } else {
                            $_PinPlacement = [
                                "Usage" => $ApproachView["PinPlacements"][0]["Usage"],
                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                "Hole" => $ApproachView["Hole"],
                                "Latitude" => 0, // input param
                                "Longitude" => 0, // input param
                                "X" => 0,
                                "Y" => 0
                            ];
                        }
                        array_push($PinPlacementsArr, $_PinPlacement);
                    }
                    // ********************
                    $_Rotation = [
                        "RotationNumber" => $Rotation["RotationNumber"],
                        "RotationType" => $Rotation["RotationType"],
                        "PinPlacements" => $PinPlacementsArr
                    ];
                    array_push($RotationsArr, $_Rotation);
                }
                //
                $_RotationSet = [
                    "Course" => $orgData["Course"],
                    "CurrentRotation" => $RotationsSet["CurrentRotation"],
                    "FirstHole" => $RotationsSet["FirstHole"],
                    "IsActive" => $RotationsSet["ActiveSet"],
                    "LastHole" => $RotationsSet["LastHole"],
                    "Name" => $RotationsSet["Name"],
                    "RotationSetType" => $RotationsSet["RotationSetType"],
                    "PinCharacter" => $RotationsSet["UserFigure"],
                    "Rotations" => $RotationsArr
                ];
                array_push($RotationSetsArr, $_RotationSet);
            }
            
            //
            $params = [
                "SiteId" => $siteId,
                "CourseRotationSetsDto" => [
                    "Version" => $orgData["Version"],
                    "Course" => $orgData["Course"],
                    "IsOneDayOverride" => $orgData["IsOneDayOverride"],
                    "RotationSets" => $RotationSetsArr
                ],
                "Log" => "Move pins"
            ];
            $response2 = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'Cookie' => $cookie
            ])->post($saveApi, $params);

            // return response()->json(['result' => $response2->json(), 'success' => $response2->successful(), 'status' => $response2->status()]);
            $result["success"] = $response2->successful();
            $result["message"] = "";
            $result["status"] = 200;
            $result["result"] = $response2->json();
        } else {
            // return response()->json(['message' => 'An error has occured', 'success' => false, 'status' => 400]);
            $result = $res;
        }

        return $result;
    }

    // vcc.visage.golf
    private function callSaveCourseRotationSetsByClubAtVisageGolf($args) {
        //
        $clubName = $args["clubName"];
        $courseName = $args["courseName"];
        $pinNumber = $args["pinNumber"];
        $rotationNumber = $args["rotationNumber"];
        $latitude = $args["latitude"];
        $longitude = $args["longitude"];
        $rotationSetType = $args["rotationSetType"];
        $marker = $args["marker"];
        $up = $args["up"];
        $down = $args["down"];
        $left = $args["left"];
        $right = $args["right"];
        // Login & Get Cookie
        $res = loginVccVisageGolf($clubName); // helper function
        if($res["success"]) {
            $cred = $res["cred"];
            $cookie = $res["cookie"];
            $course = Course::whereHas('club', function($q) {
                                $q->where('domain_type', 'visagegolf');
                            })
                            ->where('site_name', $clubName)
                            ->where('course_name', $courseName)
                            ->first();
            if(!$course) {
                $result["success"] = false;
                $result["message"] = "There is no matched course.";
                $result["status"] = 400;
                return $result;
            }
            $courseNumber = $course->course_number;
            $siteId = $course->club->site_id;
            // get PinPlacements data
            $orgData = getPinPlacementsAtVisageGolf($cookie, $siteId, $courseNumber);
            // save
            $saveApi = "https://vcc.visage.golf/api/VisageSpa/SaveCourseRotationSets";
            // Rotations
            $RotationSets = $orgData["RotationSets"]; // size = 1
            $RotationSetsArr = [];
            $ApproachViews = $orgData["ApproachViews"]; // size = 18
            foreach($RotationSets as $index => $RotationsSet) {
                // 
                $Rotations = $RotationsSet["Rotations"]; // size = 3
                $RotationsArr = [];
                foreach($Rotations as $iindex => $Rotation) { // *********************** rotation
                    $rotationMarker = '';
                    // if($RotationsSet["RotationSetType"] == $rotationSetType) {
                        $rotationMarker = $Rotation["Marker"];
                    // }
                    $PinPlacementsArr = [];
                    // **************
                    foreach($ApproachViews as $iiindex => $ApproachView) { // *********************** pin_number * hole
                        // dd('===', $iindex, count($ApproachView["PinPlacements"]));
                        if ($iindex < count($ApproachView["PinPlacements"])) {
                            $_PinPlacement = [
                                "Usage" => $ApproachView["PinPlacements"][$iindex]["Usage"],
                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                "Hole" => $ApproachView["Hole"],
                                "Latitude" => $ApproachView["PinPlacements"][$iindex]["Latitude"], // input param
                                "Longitude" => $ApproachView["PinPlacements"][$iindex]["Longitude"], // input param
                                "X" => $ApproachView["PinPlacements"][$iindex]["X"],
                                "Y" => $ApproachView["PinPlacements"][$iindex]["Y"]
                            ];
                            // updated param
                            // if($Rotation["RotationNumber"] == $rotationNumber && $ApproachView["Hole"] == $pinNumber) { // **** need to obtain
                                // 
                                foreach($ApproachView["PinPlacements"] as $PinPlacement) {
                                    if($rotationMarker == $PinPlacement['Name']) {
                                        if($ApproachView["Hole"] == $pinNumber && $PinPlacement['Name'] == $marker) {
                                            $_PinPlacement = [
                                                "Usage" => $PinPlacement["Usage"],
                                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                                "Hole" => $ApproachView["Hole"],
                                                "Latitude" => $latitude, // input param
                                                "Longitude" => $longitude, // input param
                                                // "X" => $this->calcGolfPoiXFromCenter($ApproachView["PoiData"], $iiindex, $up, $down, $left, $right, $courseNumber),
                                                // "Y" => $this->calcGolfPoiYFromCenter($ApproachView["PoiData"], $iiindex, $up, $down, $left, $right, $courseNumber)
                                                "X" => transformPointX($longitude, $latitude, $ApproachView["WorldToImageTransform"]),
                                                "Y" => transformPointY($longitude, $latitude, $ApproachView["WorldToImageTransform"])
                                            ];
                                            // dd('========', $_PinPlacement);
                                        }
                                        else {
                                            $_PinPlacement = [
                                                "Usage" => $PinPlacement["Usage"],
                                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                                "Hole" => $ApproachView["Hole"],
                                                "Latitude" => $PinPlacement["Latitude"], // input param
                                                "Longitude" => $PinPlacement["Longitude"], // input param
                                                "X" => $PinPlacement["X"],
                                                "Y" => $PinPlacement["Y"]
                                            ];
                                        }
                                        
                                    }
                                }
                            // }
                        } else {
                            $_PinPlacement = [
                                "Usage" => $ApproachView["PinPlacements"][0]["Usage"],
                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                "Hole" => $ApproachView["Hole"],
                                "Latitude" => 0, // input param
                                "Longitude" => 0, // input param
                                "X" => 0,
                                "Y" => 0
                            ];
                        }
                        array_push($PinPlacementsArr, $_PinPlacement);
                    }
                    // ********************
                    $_Rotation = [
                        "RotationNumber" => $Rotation["RotationNumber"],
                        "RotationType" => $Rotation["RotationType"],
                        "PinPlacements" => $PinPlacementsArr
                    ];
                    array_push($RotationsArr, $_Rotation);
                }
                //
                $_RotationSet = [
                    "Course" => $orgData["Course"],
                    "CurrentRotation" => $RotationsSet["CurrentRotation"],
                    "FirstHole" => $RotationsSet["FirstHole"],
                    "IsActive" => $RotationsSet["ActiveSet"],
                    "LastHole" => $RotationsSet["LastHole"],
                    "Name" => $RotationsSet["Name"],
                    "RotationSetType" => $RotationsSet["RotationSetType"],
                    "PinCharacter" => $RotationsSet["UserFigure"],
                    "Rotations" => $RotationsArr
                ];
                array_push($RotationSetsArr, $_RotationSet);
            }
            
            //
            $params = [
                "SiteId" => $siteId,
                "CourseRotationSetsDto" => [
                    "Version" => $orgData["Version"],
                    "Course" => $orgData["Course"],
                    "IsOneDayOverride" => $orgData["IsOneDayOverride"],
                    "RotationSets" => $RotationSetsArr
                ],
                "Log" => "Move pins"
            ];
            $response2 = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'Cookie' => $cookie
            ])->post($saveApi, $params);

            // return response()->json(['result' => $response2->json(), 'success' => $response2->successful(), 'status' => $response2->status()]);
            $result["success"] = $response2->successful();
            $result["message"] = "";
            $result["status"] = 200;
            $result["result"] = $response2->json();
        } else {
            // return response()->json(['message' => 'An error has occured', 'success' => false, 'status' => 400]);
            $result = $res;
        }

        return $result;
    }

    // === deprecated
    private function calcNetPoiXFromCenter($PoiData, $index, $up, $down, $left, $right) {
        $unitsLeft = [5.8, 6.2, 5.6, 6.5, 6.5, 6.8, 5.8, 6.2, 4.8, 6.2, 6, 6.4, 5.7, 5.7, 5.4, 7, 5.6, 6.8];
        $unitsRight = [6, 6.5, 5.6, 6.8, 6.5, 6.6, 5.3, 6.1, 4.6, 6.2, 6, 6.4, 5.8, 5.9, 5.2, 6.9, 5.6, 6.4];
        $unit = $left ? $unitsLeft[$index] : $unitsRight[$index];
        //
        $orgX = $this->centersNet[$index]['X'];
        foreach($PoiData as $data) {
            if (strpos($data["Name"], 'Center') !== false) {
                $orgX = $data["X"];
            }
        }
        return round($orgX - ($left * $unit) + ($right * $unit));
    }

    private function calcNetPoiYFromCenter($PoiData, $index, $up, $down, $left, $right) {
        $unitsUp = [6.5, 6.2, 6.1, 5.2, 5.8, 5.2, 5.4, 6.2, 5.4, 5.6, 5.7, 7.4, 6.2, 5.2, 5.4, 6.6, 6.6, 6.4];
        $unitsDown = [6.6, 5.8, 6.2, 5.0, 5.6, 5.3, 5.5, 6.3, 5.45, 5.75, 5.8, 7.2, 6.65, 5.1, 5.4, 6.6, 6.6, 6.5];
        $unit = $up ? $unitsUp[$index] : $unitsDown[$index];
        //
        $orgY = $this->centersNet[$index]['Y'];
        foreach($PoiData as $data) {
            if (strpos($data["Name"], 'Center') !== false) {
                $orgY = $data["Y"];
            }
        }
        return round($orgY - ($up * $unit) + ($down * $unit));
    }

    // === deprecated
    private function calcGolfPoiXFromCenter($PoiData, $index, $up, $down, $left, $right, $courseNumber) {
        $unitsLeft = [3.8, 4.78, 6.5, 5.77, 6.92, 5.43, 5.53, 5.25, 6.27, 4.5, 5.3, 6.85, 7, 6.73, 7.17, 5.58, 6.4, 6.13];
        $unitsRight = [3.8, 4.46, 6.5, 5.58, 7.06, 5.68, 5.60, 5.13, 5.93, 4.5, 5.62, 6.75, 7.42, 6.81, 7.31, 5.4, 5.91, 6.21];
        //
        $orgX = $this->centersGolf[$index]['X'];
        if($courseNumber == 2) {
            $unitsLeft = [3.8, 4.78, 6.5, 5.77, 6.92, 5.43, 5.53, 5.25, 6.27, 4.5, 5.3, 6.85, 7, 6.73, 7.17, 5.58, 6.4, 6.13];
            $unitsRight = [3.8, 4.46, 6.5, 5.58, 7.06, 5.68, 5.60, 5.13, 5.93, 4.5, 5.62, 6.75, 7.42, 6.81, 7.31, 5.4, 5.91, 6.21];
            $orgX = $this->centersGolf2[$index]['X'];
        }
        $unit = $left ? $unitsLeft[$index] : $unitsRight[$index];
        return round($orgX - ($left * $unit) + ($right * $unit));
    }

    private function calcGolfPoiYFromCenter($PoiData, $index, $up, $down, $left, $right, $courseNumber) {
        $unitsUp = [3.85, 4.63, 6.25, 5.41, 7, 5.85, 5.36, 5.31, 6.15, 4.53, 5.45, 6.75, 7.2, 6.73, 7.5, 5.62, 6.2, 5.94];
        $unitsDown = [3.75, 4.63, 6.61, 5.85, 6.94, 5.46, 5.45, 5.1, 6, 4.50, 5.28, 6.94, 7.17, 6.76, 7.23, 5.3, 5.88, 6.29];
        //
        $orgY = $this->centersGolf[$index]['Y'];
        if($courseNumber == 2) {
            $unitsUp = [3.85, 4.63, 6.25, 5.41, 7, 5.85, 5.36, 5.31, 6.15, 4.53, 5.45, 6.75, 7.2, 6.73, 7.5, 5.62, 6.2, 5.94];
            $unitsDown = [3.75, 4.63, 6.61, 5.85, 6.94, 5.46, 5.45, 5.1, 6, 4.50, 5.28, 6.94, 7.17, 6.76, 7.23, 5.3, 5.88, 6.29];
            $orgY = $this->centersGolf2[$index]['Y'];
        }
        $unit = $up ? $unitsUp[$index] : $unitsDown[$index];
        return round($orgY - ($up * $unit) + ($down * $unit));
    }

    // getTransformPoint
    public function getTransformPoint(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
            'world2image_transform' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => "Required params not passed.", 'status' => 400]);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $world2image_transform = $request->world2image_transform;

        $x = transformPointX($longitude, $latitude, $world2image_transform);
        $y = transformPointY($longitude, $latitude, $world2image_transform);

        $data = array(
            "X" => $x,
            "Y" => $y,
            "x" => $x,
            "y" => $y
        );

        return response()->json(["status" => 200, "data" => $data, "message" => "success"], 200);
    }

    // getActiveRotation
    public function getActiveRotation(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'club_name' => 'required',
            'course_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => "Required params not passed.", 'status' => 400]);
        }

        $args["clubName"] = $request->club_name;
        $args["courseName"] = $request->course_name;
        // visagenet
        $club = Club::where('site_name', $args["clubName"])->first();
        $asClubName = true;
        if(!$club) {
            $club = Club::where('site_name', $args["courseName"])->first();
            $asClubName = false;
        }
        if($club && $club->domain_type == "betavisagenet") {
            $result = $this->getActiveRotationAtBetaVisageNet($args, $asClubName);
        } else if($club && $club->domain_type == "visagenet") {
            $result = $this->getActiveRotationAtVisageNet($args, $asClubName);
        } else if($club && $club->domain_type == "visagegolf") {
            $result = $this->getActiveRotationAtVisageGolf($args);
        } else {
            $result["success"] = false;
            $result["message"] = "There is no matched club name.";
            $result["status"] = 400;
        }

        return response()->json($result);
    }

    // beta.visagenet.com
    private function getActiveRotationAtBetaVisageNet($args, $asClubName) {
        //
        $clubName = $asClubName ? $args["clubName"] : $args["courseName"];
        $courseName = $asClubName ? $args["courseName"] : $args["clubName"];
        // Login & Get Cookie
        $res = loginBetaVisageNet($clubName); // helper function
        if($res["success"]) {
            $cred = $res["cred"];
            $cookie = $res["cookie"];
            $course = Course::with('club')
                            ->where('site_name', $clubName)
                            ->where('course_name', $courseName)
                            ->first();
            if(!$course) {
                $result["success"] = false;
                $result["message"] = "There is no matched course.";
                $result["status"] = 400;
                return $result;
            }
            $courseNumber = $course->course_number;
            $siteId = $course->club->site_id;
            // get PinPlacements data
            $orgData = getPinPlacementsAtBetaVisageNet($cookie, $siteId, $courseNumber);
            // Rotations
            $RotationSets = $orgData["RotationSets"];
            $data = [];
            foreach($RotationSets as $index => $RotationSet) {
                // Check if RotationSet is activated.
                if($RotationSet["ActiveSet"]) {
                    $Rotations = $RotationSet["Rotations"];
                    foreach($Rotations as $iindex => $Rotation) { // *********************** rotation
                        // Check if Rotation is activated.
                        if($Rotation["Active"]) {
                            $data["RotationSetType"] = $RotationSet["RotationSetType"];
                            $data["RotationNumber"] = $Rotation["RotationNumber"];
                            $data["Marker"] = $Rotation["Marker"];
                            $data["DisplayName"] = $Rotation["DisplayName"];
                        }
                    }
                }
            }
            
            $result["success"] = true;
            $result["message"] = empty($data) ? "Not found an active Rotation." : "Found an active Rotation.";
            $result["status"] = 200;
            $result["result"] = $data;
        } else {
            $result = $res;
        }

        return $result;
    }

    // vcc.visagenet.com
    private function getActiveRotationAtVisageNet($args, $asClubName) {
        //
        $clubName = $asClubName ? $args["clubName"] : $args["courseName"];
        $courseName = $asClubName ? $args["courseName"] : $args["clubName"];
        // Login & Get Cookie
        $res = loginVccVisageNet($clubName); // helper function
        if($res["success"]) {
            $cred = $res["cred"];
            $cookie = $res["cookie"];
            $course = Course::with('club')
                            ->where('site_name', $clubName)
                            ->where('course_name', $courseName)
                            ->first();
            if(!$course) {
                $result["success"] = false;
                $result["message"] = "There is no matched course.";
                $result["status"] = 400;
                return $result;
            }
            $courseNumber = $course->course_number;
            $siteId = $course->club->site_id;
            // get PinPlacements data
            $orgData = getPinPlacementsAtVisageNet($cookie, $siteId, $courseNumber);
            // Rotations
            $RotationSets = $orgData["RotationSets"];
            $data = [];
            foreach($RotationSets as $index => $RotationSet) {
                // Check if RotationSet is activated.
                if($RotationSet["ActiveSet"]) {
                    $Rotations = $RotationSet["Rotations"];
                    foreach($Rotations as $iindex => $Rotation) { // *********************** rotation
                        // Check if Rotation is activated.
                        if($Rotation["Active"]) {
                            $data["RotationSetType"] = $RotationSet["RotationSetType"];
                            $data["RotationNumber"] = $Rotation["RotationNumber"];
                            $data["Marker"] = $Rotation["Marker"];
                            $data["DisplayName"] = $Rotation["DisplayName"];
                        }
                    }
                }
            }
            
            $result["success"] = true;
            $result["message"] = empty($data) ? "Not found an active Rotation." : "Found an active Rotation.";
            $result["status"] = 200;
            $result["result"] = $data;
        } else {
            $result = $res;
        }

        return $result;
    }

    // vcc.visage.golf
    private function getActiveRotationAtVisageGolf($args) {
        //
        $clubName = $args["clubName"];
        $courseName = $args["courseName"];
        // Login & Get Cookie
        $res = loginVccVisageGolf($clubName); // helper function
        if($res["success"]) {
            $cred = $res["cred"];
            $cookie = $res["cookie"];
            $course = Course::with('club')
                            ->where('site_name', $clubName)
                            ->where('course_name', $courseName)
                            ->first();
            if(!$course) {
                $result["success"] = false;
                $result["message"] = "There is no matched course.";
                $result["status"] = 400;
                return $result;
            }
            $courseNumber = $course->course_number;
            $siteId = $course->club->site_id;
            // get PinPlacements data
            $orgData = getPinPlacementsAtVisageGolf($cookie, $siteId, $courseNumber);
            // Rotations
            $RotationSets = $orgData["RotationSets"];
            $data = [];
            foreach($RotationSets as $index => $RotationSet) {
                // Check if RotationSet is activated.
                if($RotationSet["ActiveSet"]) {
                    $Rotations = $RotationSet["Rotations"];
                    foreach($Rotations as $iindex => $Rotation) { // *********************** rotation
                        // Check if Rotation is activated.
                        if($Rotation["Active"]) {
                            $data["RotationSetType"] = $RotationSet["RotationSetType"];
                            $data["RotationNumber"] = $Rotation["RotationNumber"];
                            $data["Marker"] = $Rotation["Marker"];
                            $data["DisplayName"] = $Rotation["DisplayName"];
                        }
                    }
                }
            }
            
            $result["success"] = true;
            $result["message"] = empty($data) ? "Not found an active Rotation." : "Found an active Rotation.";
            $result["status"] = 200;
            $result["result"] = $data;
        } else {
            $result = $res;
        }

        return $result;
    }

    public function getClubImageEdge(Request $request) 
    {
        //
        $validator = Validator::make($request->all(), [
            'url' => 'required',
            'width' => 'required',
            'height' => 'required',
            'x' => 'required',
            'y' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => "Required params not passed.", 'status' => 400]);
        }
        
        // test data
        // $url = "https://accupin-green-images-prod.s3.amazonaws.com/macatawa_golf_club/macatawa_legends/1.PNG"; // PNG SAMPLE
        // $url = "https://accupin-green-images-prod.s3.amazonaws.com/daniel_island_club_-_sc/ralston_creek/1.PNG"; // JPEG SAMPLE
        // $width = "454";
        // $height = "473";
        // $x = "140";
        // $y = "184";

        // input params
        $url = $request->url;
        $width = $request->width;
        $height = $request->height;
        $x = $request->x;
        $y = $request->y;

        //
        if(exif_imagetype($url) == IMAGETYPE_JPEG){
            $image = imagecreatefromjpeg($url);
        } else {
            $image = imagecreatefrompng($url);
        }
        $w = imagesx($image);
        $h = imagesy($image);
        $r_w = $w / $width;
        $r_h = $h / $height;
        //
        $x = $x * $r_w;
        $y = $y * $r_h;
        //
        $left = 0;
        $right = 0;
        $up = 0;
        $down = 0;
        // left of bound
        for ($_x = $x; $_x > 0; $_x--) {
            $rgb = imagecolorat($image, $_x, $y);
            $color = [];
            $color["red"] = ($rgb >> 16) & 255;
            $color["green"] = ($rgb >> 8) & 255;
            $color["blue"] = $rgb & 255;
            $color_avg = ($color["red"] + $color["green"] + $color["blue"]) / 3;
            if($color_avg < 50) {
                $left = $_x;
                break;
            }
        }
        // right of bound
        for ($_x = $x; $_x < $w; $_x++) {
            $rgb = imagecolorat($image, $_x, $y);
            $color = [];
            $color["red"] = ($rgb >> 16) & 255;
            $color["green"] = ($rgb >> 8) & 255;
            $color["blue"] = $rgb & 255;
            $color_avg = ($color["red"] + $color["green"] + $color["blue"]) / 3;
            if($color_avg < 50) {
                $right = $_x;
                break;
            }
        }
        // up of bound
        for ($_y = $y; $_y > 0; $_y--) {
            $rgb = imagecolorat($image, $x, $_y);
            $color = [];
            $color["red"] = ($rgb >> 16) & 255;
            $color["green"] = ($rgb >> 8) & 255;
            $color["blue"] = $rgb & 255;
            $color_avg = ($color["red"] + $color["green"] + $color["blue"]) / 3;
            if($color_avg < 50) {
                $up = $_y;
                break;
            }
        }
        // down of bound
        for ($_y = $y; $_y < $h; $_y++) {
            $rgb = imagecolorat($image, $x, $_y);
            $color = [];
            $color["red"] = ($rgb >> 16) & 255;
            $color["green"] = ($rgb >> 8) & 255;
            $color["blue"] = $rgb & 255;
            $color_avg = ($color["red"] + $color["green"] + $color["blue"]) / 3;
            if($color_avg < 50) {
                $down = $_y;
                break;
            }
        }
        $res["data"]["up"]["x"] = (int)round($x / $r_w);
        $res["data"]["up"]["y"] = (int)round($up / $r_h);
        $res["data"]["down"]["x"] = (int)round($x / $r_w);
        $res["data"]["down"]["y"] = (int)round($down / $r_h);
        $res["data"]["left"]["x"] = (int)round($left / $r_w);
        $res["data"]["left"]["y"] = (int)round($y / $r_h);
        $res["data"]["right"]["x"] = (int)round($right / $r_w);
        $res["data"]["right"]["y"] = (int)round($y / $r_h);
        // dd('===', $res);
        return response()->json($res);
    }


    // ===== ***** ===== //
    public function getPinRotationMarkersByClub(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'course_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => "Required params not passed.", 'status' => 400]);
        }
        $courseName = $request->course_name;
        //
        $markers = PinRotationMarker::where('course_name', $courseName)
                                    ->orderBy('key', 'ASC')
                                    ->get()
                                    ->groupBy('set_type_id');
        // dd('=========', $markers);
        return response()->json(['status' => 200, 'data' => $markers, 'message' => 'success'], 200);
    }

    // ===== send subscription email ===== //
    public function sendSubscriptionEmails(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'parent_course_name' => 'required',
            'child_course_name' => 'required',
            'recipient_emails' => 'required',
            'content' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => "Required params not passed.", 'status' => 400]);
        }

        //
        $parent_course_name = $request->parent_course_name;
        $child_course_name = $request->child_course_name;
        $recipient_emails = $request->recipient_emails;
        $content = $request->content;
        //
        $dm = "https://accupin-green-images-prod.s3.amazonaws.com/";
        $dp = "";
        $dc = "";
        $dt = array();
        foreach($content as $item) {
            $hole = array();
            // $hole["h"] = $item["h"];
            $img = explode($dm, $item["img"])[1];
            if($dp == "") {
                $dp = explode("/", $img)[0];
            }
            if($dc == "") {
                $dc = explode("/", $img)[1];
            }
            $hole["img"] = explode("/", $img)[2];
            //
            $_pins = array();
            $pins = $item["pins"];
            foreach($pins as $pin) {
                $_pin = array(
                    "pm" => $pin["pm"],
                    "x" => (int)($pin["x"] * 100),
                    "y" => (int)($pin["y"] * 100)
                );
                array_push($_pins, $_pin);
            }
            $hole["pins"] = $_pins;
            array_push($dt, $hole);
        }
        
        $data = array(
            "pc" => $parent_course_name,
            "cc" => $child_course_name,
            "dm" => $dm,
            "dp" => $dp,
            "dc" => $dc,
            "dt" => $dt
            // "dt" => $content
        );
        $link = env('APP_URL', 'https://vcc.accupin.com') . "/from-email?prm=".urlencode(json_encode($data));
        // $link = env('APP_URL', 'https://vcc.accupin.com') . "/from-email?prm=".json_encode($data);
        //
        // foreach ($recipient_emails as $recipient_email) {
        //     $this->sendEachEmail($recipient_email, $parent_course_name, $child_course_name, $link);
        // }

        return response()->json(['data' => $link, 'message' => "sent mails.", 'status' => 200]);
    }

    private function sendEachEmail($recipient_email, $parent_course_name, $child_course_name, $link)
    {
        Mail::to($recipient_email)->send(new SubscriptionMails($parent_course_name, $child_course_name, $link));
        return true;
    }


    /********************************************************
     * @ getCourseRotationPositionsByClubPin
     * @ params (course_name, pin_number, rotation, latitude, longitude, rotation_set_type, marker<1, 2, ..., A, B, ...>)
    ********************************************************/
    public function getCourseRotationPositionsByClubPin(Request $request) // All Clubs
    {
        //
        $validator = Validator::make($request->all(), [
            'club_name' => 'required',
            'course_name' => 'required',
            'rotation_type' => 'required',
            'rotation_number' => 'required',
            'pin_number' => 'required',
            'marker' => 'required', // new
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => "Required params not passed.", 'status' => 400]);
        }
        $args["clubName"] = $request->club_name;
        $args["courseName"] = $request->course_name;
        $args["pinNumber"] = $request->pin_number;
        $args["rotationType"] = $request->rotation_type;
        $args["rotationNumber"] = $request->rotation_number;
        $args["marker"] = $request->marker; // * new
        //
        $result = [];
        // $club = Club::where('site_name', $args["clubName"])->first();
        $courses = Course::where('site_name', $args["clubName"])
                    ->where('course_name', $args["courseName"])
                    ->get();
        $asClubName = true;
        if(!count($courses)) {
            // $club = Club::where('site_name', $args["courseName"])->first();
            $courses = Course::where('site_name', $args["courseName"])
                    ->where('course_name', $args["clubName"])
                    ->get();
            $asClubName = false;
        }

        // executing Unit
        if(!count($courses)) {
            $result["success"] = false;
            $result["message"] = "There is no matched club name.";
            $result["status"] = 400;
        }
        // 
        foreach($courses as $course) {
            $club = Club::find($course->club_id);
            // 
            if($club && $club->domain_type == "betavisagenet") {
                $result = $this->getCourseRotationPositionsByClubPinAtBetaVisageNet($args, $asClubName);
            } else if($club && $club->domain_type == "visagenet") {
                $result = $this->getCourseRotationPositionsByClubPinAtVisageNet($args, $asClubName);
            } else if($club && $club->domain_type == "visagegolf") {
                $result = $this->getCourseRotationPositionsByClubPinAtVisageGolf($args);
            } else {
                $result["success"] = false;
                $result["message"] = "There is no matched club name.";
                $result["status"] = 400;
            }
        }

        return response()->json($result);
        
    }

    // getCourseRotationPositionsByClubPinAtVisageNet | beta.visagenet.com
    private function getCourseRotationPositionsByClubPinAtBetaVisageNet($args, $asClubName) {
        //
        $clubName = $asClubName ? $args["clubName"] : $args["courseName"];
        $courseName = $asClubName ? $args["courseName"] : $args["clubName"];
        $pinNumber = $args["pinNumber"];
        $rotationType = $args["rotationType"];
        $rotationNumber = $args["rotationNumber"];
        $marker = $args["marker"];
        // retun variable
        $_PinPlacement = [];
        // Login & Get Cookie
        $res = loginBetaVisageNet($clubName); // helper function
        if($res["success"]) {
            $cred = $res["cred"];
            $cookie = $res["cookie"];
            $course = Course::whereHas('club', function($q) {
                                $q->where('domain_type', 'betavisagenet');
                            })
                            ->where('site_name', $clubName)
                            ->where('course_name', $courseName)
                            ->first();
            if(!$course) {
                $result["success"] = false;
                $result["message"] = "There is no matched course.";
                $result["status"] = 400;
                return $result;
            }
            $courseNumber = $course->course_number;
            $siteId = $course->club->site_id;
            // get PinPlacements data
            $orgData = getPinPlacementsAtBetaVisageNet($cookie, $siteId, $courseNumber);
            // Rotations
            $RotationSets = $orgData["RotationSets"]; // size = 1
            $RotationSetsArr = [];
            $ApproachViews = $orgData["ApproachViews"]; // size = 18
            foreach($RotationSets as $index => $RotationsSet) {
                // 
                $rotationSetType = $RotationsSet["RotationSetType"];
                $Rotations = $RotationsSet["Rotations"]; // size = 3
                $RotationsArr = [];
                if ($rotationSetType == $rotationType) {
                    foreach($Rotations as $iindex => $Rotation) { // *********************** rotation
                        $rotationMarker = $Rotation["Marker"];
                        $PinPlacementsArr = [];
                        // **************
                        foreach($ApproachViews as $iiindex => $ApproachView) { // *********************** pin_number * hole
                            if ($iindex < count($ApproachView["PinPlacements"])) {
                                foreach($ApproachView["PinPlacements"] as $PinPlacement) {
                                    if($rotationMarker == $PinPlacement['Name']) {
                                        if($ApproachView["Hole"] == $pinNumber && $PinPlacement['Name'] == $marker) {
                                            $_PinPlacement = $PinPlacement;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $result['data'] = $_PinPlacement;
        $result["success"] = count($_PinPlacement) ? true : false;
        $result["message"] = count($_PinPlacement) ? "" : "Rotation does not exist.";
        $result["status"] = count($_PinPlacement) ? 200 : 400;

        return $result;
    }

    // getCourseRotationPositionsByClubPinAtVisageNet | vcc.visagenet.com
    private function getCourseRotationPositionsByClubPinAtVisageNet($args, $asClubName) {
        //
        $clubName = $asClubName ? $args["clubName"] : $args["courseName"];
        $courseName = $asClubName ? $args["courseName"] : $args["clubName"];
        $pinNumber = $args["pinNumber"];
        $rotationType = $args["rotationType"];
        $rotationNumber = $args["rotationNumber"];
        $marker = $args["marker"];
        // retun variable
        $_PinPlacement = [];
        // Login & Get Cookie
        $res = loginVccVisageNet($clubName); // helper function
        if($res["success"]) {
            $cred = $res["cred"];
            $cookie = $res["cookie"];
            $course = Course::whereHas('club', function($q) {
                                $q->where('domain_type', 'visagenet');
                            })
                            ->where('site_name', $clubName)
                            ->where('course_name', $courseName)
                            ->first();
            if(!$course) {
                $result["success"] = false;
                $result["message"] = "There is no matched course.";
                $result["status"] = 400;
                return $result;
            }
            $courseNumber = $course->course_number;
            $siteId = $course->club->site_id;
            // get PinPlacements data
            $orgData = getPinPlacementsAtVisageNet($cookie, $siteId, $courseNumber);
            // Rotations
            $RotationSets = $orgData["RotationSets"]; // size = 1
            $RotationSetsArr = [];
            $ApproachViews = $orgData["ApproachViews"]; // size = 18
            foreach($RotationSets as $index => $RotationsSet) {
                // 
                $rotationSetType = $RotationsSet["RotationSetType"];
                $Rotations = $RotationsSet["Rotations"]; // size = 3
                $RotationsArr = [];
                if ($rotationSetType == $rotationType) {
                    foreach($Rotations as $iindex => $Rotation) { // *********************** rotation
                        $rotationMarker = $Rotation["Marker"];
                        $PinPlacementsArr = [];
                        // **************
                        foreach($ApproachViews as $iiindex => $ApproachView) { // *********************** pin_number * hole
                            if ($iindex < count($ApproachView["PinPlacements"])) {
                                foreach($ApproachView["PinPlacements"] as $PinPlacement) {
                                    if($rotationMarker == $PinPlacement['Name']) {
                                        if($ApproachView["Hole"] == $pinNumber && $PinPlacement['Name'] == $marker) {
                                            $_PinPlacement = $PinPlacement;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $result['data'] = $_PinPlacement;
        $result["success"] = count($_PinPlacement) ? true : false;
        $result["message"] = count($_PinPlacement) ? "" : "Rotation does not exist.";
        $result["status"] = count($_PinPlacement) ? 200 : 400;

        return $result;
    }

    // getCourseRotationPositionsByClubPinAtVisageGolf | vcc.visage.golf
    private function getCourseRotationPositionsByClubPinAtVisageGolf($args) {
        //
        $clubName = $args["clubName"];
        $courseName = $args["courseName"];
        $pinNumber = $args["pinNumber"];
        $rotationType = $args["rotationType"];
        $rotationNumber = $args["rotationNumber"];
        $marker = $args["marker"];
        // retun variable
        $_PinPlacement = [];
        // Login & Get Cookie
        $res = loginVccVisageGolf($clubName); // helper function
        if($res["success"]) {
            $cred = $res["cred"];
            $cookie = $res["cookie"];
            $course = Course::whereHas('club', function($q) {
                                $q->where('domain_type', 'visagegolf');
                            })
                            ->where('site_name', $clubName)
                            ->where('course_name', $courseName)
                            ->first();
            if(!$course) {
                $result["success"] = false;
                $result["message"] = "There is no matched course.";
                $result["status"] = 400;
                return $result;
            }
            $courseNumber = $course->course_number;
            $siteId = $course->club->site_id;
            // get PinPlacements data
            $orgData = getPinPlacementsAtVisageGolf($cookie, $siteId, $courseNumber);
            // Rotations
            $RotationSets = $orgData["RotationSets"]; // size = 1
            $RotationSetsArr = [];
            $ApproachViews = $orgData["ApproachViews"]; // size = 18
            foreach($RotationSets as $index => $RotationsSet) {
                // 
                $rotationSetType = $RotationsSet["RotationSetType"];
                $Rotations = $RotationsSet["Rotations"]; // size = 3
                $RotationsArr = [];
                if ($rotationSetType == $rotationType) {
                    foreach($Rotations as $iindex => $Rotation) { // *********************** rotation
                        $rotationMarker = '';
                        $rotationMarker = $Rotation["Marker"];
                        $PinPlacementsArr = [];
                        // **************
                        foreach($ApproachViews as $iiindex => $ApproachView) { // *********************** pin_number * hole
                            if ($iindex < count($ApproachView["PinPlacements"])) {
                                foreach($ApproachView["PinPlacements"] as $PinPlacement) {
                                    if($rotationMarker == $PinPlacement['Name']) {
                                        if($ApproachView["Hole"] == $pinNumber && $PinPlacement['Name'] == $marker) {
                                            $_PinPlacement = $PinPlacement;
                                            break;
                                        }                                    
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $result['data'] = $_PinPlacement;
        $result["success"] = count($_PinPlacement) ? true : false;
        $result["message"] = count($_PinPlacement) ? "" : "Rotation does not exist.";
        $result["status"] = count($_PinPlacement) ? 200 : 400;

        return $result;
    }

}


















// $params = [
//     "SiteId" => 704,
//     "CourseRotationSetsDto" => [
//         "Version" => $orgData["Version"],
//         "Course" => $orgData["Course"],
//         "IsOneDayOverride" => false,
//         "RotationSets" => [
//             [
//                 "Course" => $orgData["Course"],
//                 "CurrentRotation" => $orgData["RotationSets"][0]["CurrentRotation"],
//                 "FirstHole" => $orgData["RotationSets"][0]["FirstHole"],
//                 "IsActive" => $orgData["RotationSets"][0]["ActiveSet"],
//                 "LastHole" => $orgData["RotationSets"][0]["LastHole"],
//                 "Name" => $orgData["RotationSets"][0]["Name"],
//                 "RotationSetType" => $orgData["RotationSets"][0]["RotationSetType"],
//                 "PinCharacter" => $orgData["RotationSets"][0]["UserFigure"],
//                 "Rotations" => [
//                     [
//                         "RotationNumber" => $orgData["RotationSets"][0]["Rotations"][0]["RotationNumber"],
//                         "RotationType" => $orgData["RotationSets"][0]["Rotations"][0]["RotationType"],
//                         "PinPlacements" => [
//                             [
//                                 "Usage" => $orgData["ApproachViews"][0]["PinPlacements"][0]["Usage"],
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => $orgData["ApproachViews"][0]["Hole"],
//                                 "Latitude" => $orgData["ApproachViews"][0]["PinPlacements"][0]["Latitude"],
//                                 "Longitude" => $orgData["ApproachViews"][0]["PinPlacements"][0]["Longitude"],
//                                 "X" => $orgData["ApproachViews"][0]["PinPlacements"][0]["X"],
//                                 "Y" => $orgData["ApproachViews"][0]["PinPlacements"][0]["Y"]
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 2,
//                                 "Latitude" => 42.851054269743926,
//                                 "Longitude" => -86.14472478708794,
//                                 "X" => 376,
//                                 "Y" => 213
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 3,
//                                 "Latitude" => 42.851908628373195,
//                                 "Longitude" => -86.14625937458885,
//                                 "X" => 323,
//                                 "Y" => 320
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 4,
//                                 "Latitude" => 42.8535067984858,
//                                 "Longitude" => -86.14031553653507,
//                                 "X" => 284,
//                                 "Y" => 225
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 5,
//                                 "Latitude" => 42.8522450592953,
//                                 "Longitude" => -86.14521380601002,
//                                 "X" => 394,
//                                 "Y" => 291
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 6,
//                                 "Latitude" => 42.85441547017598,
//                                 "Longitude" => -86.14263659526954,
//                                 "X" => 296,
//                                 "Y" => 278
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 7,
//                                 "Latitude" => 42.85435938904458,
//                                 "Longitude" => -86.14109725974502,
//                                 "X" => 423,
//                                 "Y" => 351
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 8,
//                                 "Latitude" => 42.85042132994082,
//                                 "Longitude" => -86.13986847197643,
//                                 "X" => 421,
//                                 "Y" => 217
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 9,
//                                 "Latitude" => 42.85158007809387,
//                                 "Longitude" => -86.1325496345279,
//                                 "X" => 390,
//                                 "Y" => 316
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 10,
//                                 "Latitude" => 42.84803030050658,
//                                 "Longitude" => -86.12991751694935,
//                                 "X" => 399,
//                                 "Y" => 256
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 11,
//                                 "Latitude" => 42.847486339899845,
//                                 "Longitude" => -86.12502247041586,
//                                 "X" => 372,
//                                 "Y" => 290
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 12,
//                                 "Latitude" => 42.85095386266865,
//                                 "Longitude" => -86.12254112415496,
//                                 "X" => 385,
//                                 "Y" => 331
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 13,
//                                 "Latitude" => 42.85166654646634,
//                                 "Longitude" => -86.12189164491792,
//                                 "X" => 305,
//                                 "Y" => 301
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 14,
//                                 "Latitude" => 42.84804815270008,
//                                 "Longitude" => -86.12297887338894,
//                                 "X" => 330,
//                                 "Y" => 338
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 15,
//                                 "Latitude" => 42.85117517848783,
//                                 "Longitude" => -86.12655281632405,
//                                 "X" => 293,
//                                 "Y" => 315
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 16,
//                                 "Latitude" => 42.848348829526365,
//                                 "Longitude" => -86.12625697833006,
//                                 "X" => 368,
//                                 "Y" => 322
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 17,
//                                 "Latitude" => 42.84825381776732,
//                                 "Longitude" => -86.1288216658763,
//                                 "X" => 406,
//                                 "Y" => 314
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 18,
//                                 "Latitude" => 42.85171585246361,
//                                 "Longitude" => -86.13155493286652,
//                                 "X" => 345,
//                                 "Y" => 312
//                             ]
//                         ]
//                     ],
//                     [
//                         "RotationNumber" => 2,
//                         "RotationType" => 1,
//                         "PinPlacements" => [
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 1,
//                                 "Latitude" => 42.850287710918735,
//                                 "Longitude" => -86.13639790894676,
//                                 "X" => 411,
//                                 "Y" => 277
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 2,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 3,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 4,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 5,
//                                 "Latitude" => 42.85232065086547,
//                                 "Longitude" => -86.14531436305793,
//                                 "X" => 472,
//                                 "Y" => 250
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 6,
//                                 "Latitude" => 42.85442557414013,
//                                 "Longitude" => -86.142391601058,
//                                 "X" => 412,
//                                 "Y" => 170
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 7,
//                                 "Latitude" => 42.854136614564126,
//                                 "Longitude" => -86.14093219246436,
//                                 "X" => 445,
//                                 "Y" => 189
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 8,
//                                 "Latitude" => 42.85053695408541,
//                                 "Longitude" => -86.13967077834616,
//                                 "X" => 337,
//                                 "Y" => 353
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 9,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 10,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 11,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 12,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 13,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 14,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 15,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 16,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 17,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 18,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ]
//                         ]
//                     ],
//                     [
//                         "RotationNumber" => 3,
//                         "RotationType" => 1,
//                         "PinPlacements" => [
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 1,
//                                 "Latitude" => 42.850249524892654,
//                                 "Longitude" => -86.13631449229577,
//                                 "X" => 371,
//                                 "Y" => 321
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 2,
//                                 "Latitude" => 42.851107396451546,
//                                 "Longitude" => -86.14457862278257,
//                                 "X" => 443,
//                                 "Y" => 283
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 3,
//                                 "Latitude" => 42.85189783451225,
//                                 "Longitude" => -86.1463443224249,
//                                 "X" => 281,
//                                 "Y" => 292
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 4,
//                                 "Latitude" => 42.85328381874039,
//                                 "Longitude" => -86.14032427880124,
//                                 "X" => 426,
//                                 "Y" => 289
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 5,
//                                 "Latitude" => 42.8522776089235,
//                                 "Longitude" => -86.14522252765661,
//                                 "X" => 420,
//                                 "Y" => 291
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 6,
//                                 "Latitude" => 42.854386343713195,
//                                 "Longitude" => -86.14255580369125,
//                                 "X" => 352,
//                                 "Y" => 255
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 7,
//                                 "Latitude" => 42.85437050567476,
//                                 "Longitude" => -86.14104501072781,
//                                 "X" => 397,
//                                 "Y" => 334
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 8,
//                                 "Latitude" => 42.85050143701418,
//                                 "Longitude" => -86.13970997706836,
//                                 "X" => 351,
//                                 "Y" => 317
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 9,
//                                 "Latitude" => 42.85151289832456,
//                                 "Longitude" => -86.13258330924958,
//                                 "X" => 398,
//                                 "Y" => 360
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 10,
//                                 "Latitude" => 42.848015303615696,
//                                 "Longitude" => -86.12992460875027,
//                                 "X" => 403,
//                                 "Y" => 246
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 11,
//                                 "Latitude" => 42.84743732979225,
//                                 "Longitude" => -86.12499983476067,
//                                 "X" => 407,
//                                 "Y" => 277
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 12,
//                                 "Latitude" => 42.85108291263087,
//                                 "Longitude" => -86.1224468754411,
//                                 "X" => 385,
//                                 "Y" => 200
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 13,
//                                 "Latitude" => 42.851731613563054,
//                                 "Longitude" => -86.1217702854076,
//                                 "X" => 264,
//                                 "Y" => 228
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 14,
//                                 "Latitude" => 42.84799151252665,
//                                 "Longitude" => -86.12299127412156,
//                                 "X" => 325,
//                                 "Y" => 303
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 15,
//                                 "Latitude" => 42.851164691701264,
//                                 "Longitude" => -86.12652120636257,
//                                 "X" => 304,
//                                 "Y" => 330
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 16,
//                                 "Latitude" => 42.84831546846331,
//                                 "Longitude" => -86.12621895123206,
//                                 "X" => 355,
//                                 "Y" => 286
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 17,
//                                 "Latitude" => 42.84809691651889,
//                                 "Longitude" => -86.12897306638011,
//                                 "X" => 331,
//                                 "Y" => 201
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 18,
//                                 "Latitude" => 42.851820158092885,
//                                 "Longitude" => -86.1315370297481,
//                                 "X" => 368,
//                                 "Y" => 236
//                             ]
//                         ]
//                     ]
//                 ]
//             ]
//         ]
//     ],
//     "Log" => "Move pins"
// ];