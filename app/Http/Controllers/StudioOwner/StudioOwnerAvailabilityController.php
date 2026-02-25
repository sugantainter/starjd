<?php

namespace App\Http\Controllers\StudioOwner;

use App\Http\Controllers\Controller;
use App\Models\AvailabilitySlot;
use App\Models\Studio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudioOwnerAvailabilityController extends Controller
{
    /**
     * GET /api/studio-owner/studios/{studio}/availability — List slots (optional date range).
     */
    public function index(Request $request, Studio $studio): JsonResponse
    {
        $this->authorize('update', $studio);

        $query = $studio->availabilitySlots()->orderBy('date')->orderBy('start_time');

        if ($request->filled('from')) {
            $query->where('date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->where('date', '<=', $request->input('to'));
        }

        $slots = $query->limit(500)->get()->map(fn (AvailabilitySlot $s) => [
            'id' => $s->id,
            'date' => $s->date->format('Y-m-d'),
            'start_time' => $s->start_time,
            'end_time' => $s->end_time,
            'is_available' => $s->is_available,
        ]);

        return response()->json(['slots' => $slots]);
    }

    /**
     * POST /api/studio-owner/studios/{studio}/availability — Create one or more slots.
     */
    public function store(Request $request, Studio $studio): JsonResponse
    {
        $this->authorize('update', $studio);

        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'string', 'date_format:H:i'],
            'end_time' => ['required', 'string', 'date_format:H:i', 'after:start_time'],
            'is_available' => ['nullable', 'boolean'],
            'slots' => ['nullable', 'array'],
            'slots.*.date' => ['required', 'date', 'after_or_equal:today'],
            'slots.*.start_time' => ['required', 'string', 'date_format:H:i'],
            'slots.*.end_time' => ['required', 'string', 'date_format:H:i', 'after:slots.*.start_time'],
        ]);

        $created = [];

        if ($request->filled('slots')) {
            foreach ($request->input('slots') as $s) {
                $slot = $studio->availabilitySlots()->create([
                    'date' => $s['date'],
                    'start_time' => $s['start_time'],
                    'end_time' => $s['end_time'],
                    'is_available' => $s['is_available'] ?? true,
                ]);
                $created[] = ['id' => $slot->id, 'date' => $slot->date->format('Y-m-d'), 'start_time' => $slot->start_time, 'end_time' => $slot->end_time];
            }
        } else {
            $slot = $studio->availabilitySlots()->create([
                'date' => $request->input('date'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'is_available' => $request->boolean('is_available', true),
            ]);
            $created[] = ['id' => $slot->id, 'date' => $slot->date->format('Y-m-d'), 'start_time' => $slot->start_time, 'end_time' => $slot->end_time];
        }

        return response()->json(['message' => 'Slots added.', 'slots' => $created], 201);
    }

    /**
     * PUT /api/studio-owner/availability-slots/{availability_slot} — Update slot.
     */
    public function update(Request $request, AvailabilitySlot $availability_slot): JsonResponse
    {
        $this->authorize('update', $availability_slot->studio);

        $request->validate([
            'date' => ['sometimes', 'date', 'after_or_equal:today'],
            'start_time' => ['sometimes', 'string', 'date_format:H:i'],
            'end_time' => ['sometimes', 'string', 'date_format:H:i', 'after:start_time'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $availability_slot->update($request->only(['date', 'start_time', 'end_time', 'is_available']));

        return response()->json([
            'message' => 'Updated.',
            'slot' => [
                'id' => $availability_slot->id,
                'date' => $availability_slot->date->format('Y-m-d'),
                'start_time' => $availability_slot->start_time,
                'end_time' => $availability_slot->end_time,
                'is_available' => $availability_slot->is_available,
            ],
        ]);
    }

    /**
     * DELETE /api/studio-owner/availability-slots/{availability_slot} — Delete slot.
     */
    public function destroy(AvailabilitySlot $availability_slot): JsonResponse
    {
        $this->authorize('update', $availability_slot->studio);
        $availability_slot->delete();
        return response()->json(['message' => 'Slot removed.']);
    }
}
