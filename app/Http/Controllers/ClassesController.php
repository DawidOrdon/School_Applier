<?php

namespace App\Http\Controllers;

use App\Models\Applications;
use App\Models\Classes;
use App\Models\schools;
use App\Models\Schools_types;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClassesController extends Controller
{
    public function create(int $school_id)
    {
        $user = Auth::user();
        if (isset($user)) {
            if ($user->hasPermissionTo('edit_school_data')) {
                $school = schools::all()->where('user_id', '=', $user->id)->where('id', '=', $school_id);
                if (count($school)) {
                    return view('classes.create', ['school_id' => $school_id, 'schools_types' => Schools_types::all(), 'subjects' => Subjects::all()->where('id', '!=', 1)->where('id', '!=', 2)]);
                } else {
                    return redirect('/');
                }
            }
            return redirect('/');
        }
        return redirect('/');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, int $school_id)
    {
        $user = Auth::user();
        if (isset($user)) {
            if ($user->hasPermissionTo('edit_school_data')) {
                $school = schools::all()->where('user_id', '=', $user->id)->where('id', '=', $school_id);
                if (count($school)) {
                    $request->validate([
                        'class_name' =>'required|min:0|max:255',
                        'desc' =>'required|min:0|max:255',
                        'slots' =>'required|min:0|max:255|numeric',
                        'school_type' => [
                            'required',
                            Rule::exists('schools_types', 'id'),
                        ],
                        'subject1' => [
                            'required',
                            Rule::exists('subjects', 'id'),
                        ],
                        'subject2' => [
                            'required',
                            Rule::exists('subjects', 'id'),
                        ],


                    ],ValidController::GetComment(),
                        ValidController::GetAlias());
                    $new_class = new Classes();
                    $new_class->school_id = $school_id;
                    $new_class->name = $request['class_name'];
                    $new_class->desc = $request['desc'];
                    $new_class->slots = $request['slots'];
                    $new_class->school_type = $request['school_type'];
                    $new_class->subject1 = $request['subject1'];
                    $new_class->subject2 = $request['subject2'];
                    $new_class->save();
                    return redirect('/schools/' . $school_id . '/admin');
                } else {
                    return redirect('/');
                }
            }
            return redirect('/');
        }
        return redirect('/');

    }

    /**
     * Display the specified resource.
     */
    public function show(int $school_id, int $class_id)
    {
        $application = Applications::join('kids', 'kids.id', '=', 'applications.kid_id')
            ->get(['kids.first_name', 'kids.last_name', 'kids.exam_photo', 'kids.certificate_photo1', 'kids.certificate_photo2',
                'applications.id', 'applications.class_id', 'applications.exam_points', 'applications.certificate_points', 'applications.bonus_points', 'applications.unlock', 'applications.status_id'])
            ->where('unlock', '=', 1)
            ->where('status_id', '!=', 6)
            ->where('class_id', '=', $class_id);
//        return $application;
        return view('classes.applications', ['applications' => $application,
            'school_id' => $school_id,
            'class_id' => $class_id]);
    }

    public function generate_lists(int $school_id, int $class_id)
    {
        $application_accept = Applications::join('kids', 'kids.id', '=', 'applications.kid_id')
            ->select([
                'kids.first_name',
                'kids.second_name',
                'kids.last_name',
                'kids.exam_photo',
                'kids.certificate_photo1',
                'kids.certificate_photo2',
                'applications.id',
                'applications.class_id',
                'applications.exam_points',
                'applications.certificate_points',
                'applications.bonus_points',
                'applications.unlock',
                'applications.status_id',
            ])
            ->selectRaw('applications.exam_points + applications.certificate_points + applications.bonus_points as points')
            ->where('unlock', '=', 1)
            ->where('status_id', '!=', 6)
            ->where('class_id', '=', $class_id)
            ->orderBy('points')
            ->get();

        $application_rejected = Applications::join('kids', 'kids.id', '=', 'applications.kid_id')
            ->get(['kids.first_name', 'kids.second_name', 'kids.last_name', 'kids.exam_photo', 'kids.certificate_photo1', 'kids.certificate_photo2',
                'applications.id', 'applications.class_id', 'applications.exam_points', 'applications.certificate_points', 'applications.bonus_points', 'applications.unlock', 'applications.status_id'])
            ->where('unlock', '=', 1)
            ->where('status_id', '=', 6)
            ->where('class_id', '=', $class_id);
        $class_limit = Classes::find($class_id)->slots;
//        return $application;
        return view('classes.lists', ['applications_accept' => $application_accept,
            'applications_rejected' => $application_rejected,
            'school_id' => $school_id,
            'class_id' => $class_id,
            'slots' => $class_limit]);
    }

    public function restore(int $school_id, int $class_id)
    {
        $application = Applications::join('kids', 'kids.id', '=', 'applications.kid_id')
            ->get(['kids.first_name', 'kids.last_name', 'kids.exam_photo', 'kids.certificate_photo1', 'kids.certificate_photo2',
                'applications.id', 'applications.class_id', 'applications.exam_points', 'applications.certificate_points', 'applications.bonus_points', 'applications.unlock', 'applications.status_id'])
            ->where('unlock', '=', 1)
            ->where('status_id', '=', 6)
            ->where('class_id', '=', $class_id);
//        return $application;
        return view('classes.applications_restore', ['applications' => $application,
            'school_id' => $school_id,
            'class_id' => $class_id]);

    }

    public function edit(int $school_id, int $class_id)
    {
        $user = Auth::user();
        if (isset($user)) {
            if ($user->hasPermissionTo('edit_school_data')) {
                $school = schools::all()->where('user_id', '=', $user->id)->where('id', '=', $school_id);
                if (count($school)) {
                    return view('classes.edit', ['school_id' => $school_id,
                                                        'class_id'=>$class_id,
                                                        'schools_types' => Schools_types::all(),
                                                        'subjects' => Subjects::all()->where('id', '!=', 1)->where('id', '!=', 2),
                                                        'class_data'=>Classes::find($class_id)]);
                } else {
                    return redirect('/');
                }
            }
            return redirect('/');
        }
        return redirect('/');
    }
    public function update(Request $request, int $school_id, int $class_id)
    {
        $user = Auth::user();
        if (isset($user)) {
            if ($user->hasPermissionTo('edit_school_data')) {
                $school = schools::all()->where('user_id', '=', $user->id)->where('id', '=', $school_id);
                if (count($school)) {
                    $request->validate([
                        'class_name' =>'required|min:0|max:255',
                        'desc' =>'required|min:0|max:255',
                        'slots' =>'required|min:0|max:255|numeric',
                        'school_type' => [
                            'required',
                            Rule::exists('schools_types', 'id'),
                        ],
                        'subject1' => [
                            'required',
                            Rule::exists('subjects', 'id'),
                        ],
                        'subject2' => [
                            'required',
                            Rule::exists('subjects', 'id'),
                        ],


                    ],ValidController::GetComment(),
                        ValidController::GetAlias());
                    $new_class = Classes::find($class_id);
                    $new_class->school_id = $school_id;
                    $new_class->name = $request['class_name'];
                    $new_class->desc = $request['desc'];
                    $new_class->slots = $request['slots'];
                    $new_class->school_type = $request['school_type'];
                    $new_class->subject1 = $request['subject1'];
                    $new_class->subject2 = $request['subject2'];
                    $new_class->save();
                    return redirect('/schools/' . $school_id . '/admin');
                } else {
                    return redirect('/');
                }
            }
            return redirect('/');
        }
        return redirect('/');

    }

}
