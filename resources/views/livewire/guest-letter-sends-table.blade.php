<div wire:poll.30s>
    {{-- Replace this table markup with your existing Guest Letters table UI --}}
    <div class="overflow-hidden rounded-xl border border-white/10">
        <table class="w-full text-sm">
            <thead class="bg-white/5">
                <tr>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">To</th>
                    <th class="px-4 py-3 text-left">Scheduled</th>
                    <th class="px-4 py-3 text-left">Sent</th>
                </tr>
            </thead>

            <tbody>
                @forelse($sends as $send)
                <tr class="border-t border-white/10">
                    <td class="px-4 py-3">{{ str_replace('_', '-', $send->type) }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2 py-1 text-xs bg-white/10">
                            {{ $send->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $send->to_email }}</td>
                    <td class="px-4 py-3">{{ optional($send->scheduled_for)->format('Y-m-d H:i') ?? '-' }}</td>
                    <td class="px-4 py-3">{{ optional($send->sent_at)->format('Y-m-d H:i') ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-white/60">No guest letters.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>