<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query();

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('responsable')) {
            $query->where('responsables', 'like', '%' . $request->responsable . '%');
        }
        if ($request->filled('semana')) {
            $query->where('semana', $request->semana);
        }

        $activities = $query->orderBy('semana')->get();

        return view('activities.index', compact('activities'));
    }

    public function create()
    {
        $users = \App\Models\User::orderBy('name')->get();
        return view('activities.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'semana' => 'required|string|max:255',
            'fecha_entrega' => 'nullable|date',
            'responsables' => 'required|array|min:1',
            'responsables.*' => 'string',
        ]);

        Activity::create([
            'nombre' => $validated['nombre'],
            'semana' => $validated['semana'],
            'fecha_entrega' => $validated['fecha_entrega'] ?? null,
            'responsables' => implode('; ', $validated['responsables']),
            'estado' => 'pendiente',
            'observaciones' => null,
        ]);

        return redirect()->route('activities.index')->with('success', 'Actividad registrada correctamente.');
    }

    public function edit(Activity $activity)
    {
        if (!auth()->user()->canEditActivities()) {
            abort(403, 'No tienes permiso para editar actividades.');
        }

        $users = \App\Models\User::orderBy('name')->get();

        return view('activities.edit', compact('activity', 'users'));
    }

    public function update(Request $request, Activity $activity)
    {
        if (!auth()->user()->canEditActivities()) {
            abort(403, 'No tienes permiso para editar actividades.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'semana' => 'required|string|max:255',
            'fecha_entrega' => 'nullable|date',
            'responsables' => 'required|array|min:1',
            'responsables.*' => 'string',
            'estado' => 'required|in:realizado,pendiente,seguimiento',
            'observaciones' => 'nullable|string',
        ]);

        $activity->update([
            'nombre' => $validated['nombre'],
            'semana' => $validated['semana'],
            'fecha_entrega' => $validated['fecha_entrega'] ?? null,
            'responsables' => implode('; ', $validated['responsables']),
            'estado' => $validated['estado'],
            'observaciones' => $validated['observaciones'] ?? null,
        ]);

        return redirect()->route('activities.index')->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Activity $activity)
    {
        if (!auth()->user()->canDeleteActivities()) {
            abort(403, 'No tienes permiso para eliminar actividades.');
        }

        $activity->delete();

        return redirect()->route('activities.index')->with('success', 'Actividad eliminada correctamente.');
    }
}
