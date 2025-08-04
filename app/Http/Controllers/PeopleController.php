<?php
namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Language;
use App\Models\Interest;
use Illuminate\Http\Request;
use App\Events\PersonCreated;

class PeopleController extends Controller
{
    public function index()
    {
        $people = \App\Models\Person::with(['language', 'interests'])->get()->map(function($person) {
            return [
                'id' => $person->id,
                'name' => $person->name,
                'surname' => $person->surname,
                'id_number' => $person->id_number,
                'mobile_number' => $person->mobile_number,
                'email' => $person->email,
                'birth_date' => $person->birth_date,
                'language_name' => $person->language->name ?? null,
            ];
        });
        return view('people', ['people' => $people]);
    }

    public function create()
    {
        $languages = Language::all();
        $interests = Interest::all();
        $person = new Person();
        $personInterests = [];
        $isEditing = false;
        return view('people.form', compact('languages', 'interests', 'person', 'personInterests', 'isEditing'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:100',
        'surname' => 'required|string|max:100',
        'id_number' => 'required|string|size:13|unique:people,id_number',
        'mobile_number' => 'required|string|max:20',
        'email' => 'required|email|max:100|unique:people,email',
        'birth_date' => 'required|date',
        'language_id' => 'nullable|exists:languages,id',
        'interests' => 'nullable|array',
        'interests.*' => 'exists:interests,id',
    ]);

    $person = Person::create($validated);

    if ($request->has('interests')) {
        $person->interests()->sync($request->input('interests'));
    }

    event(new PersonCreated($person));

    return redirect()->route('people.index')->with('success', 'Person added successfully.');
}

    public function show(Person $person)
    {
        $person->load(['language', 'interests']);
        return view('people.show', compact('person'));
    }

    public function edit(Person $person)
    {
        $languages = Language::all();
        $interests = Interest::all();
        $personInterests = $person->interests->pluck('id')->toArray();
        $isEditing = true;

        return view('people.form', compact('languages', 'interests', 'person', 'personInterests', 'isEditing'));
    }

    public function update(Request $request, Person $person)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'surname' => 'required|string|max:100',
            'id_number' => 'required|string|size:13|unique:people,id_number,' . $person->id,
            'mobile_number' => 'required|string|max:20',
            'email' => 'required|email|max:100|unique:people,email,' . $person->id,
            'birth_date' => 'required|date',
            'language_id' => 'nullable|exists:languages,id',
            'interests' => 'nullable|array',
            'interests.*' => 'exists:interests,id',
        ]);
        $person->update($validated);
        if ($request->has('interests')) {
            $person->interests()->sync($request->input('interests'));
        }
        return redirect()->route('people.index')->with('success', 'Person updated successfully.');
    }

    public function destroy(Person $person)
    {
        $person->delete();
        return redirect()->route('people.index')->with('success', 'Person deleted successfully.');
    }
}
