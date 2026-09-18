<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function dashboard(Request $r)
    {
        $person = $r->user()->person?->load(['membership.homeMasjid', 'positions.position', 'positions.division']);
        $tasks = $person?->hasMany(Task::class, 'assigned_to')->latest()->get() ?? collect();

        return view('member.dashboard', compact('person', 'tasks'));
    }

    public function profile(Request $r)
    {
        return view('member.profile', ['person' => $r->user()->person?->load('membership.homeMasjid')]);
    }
}
