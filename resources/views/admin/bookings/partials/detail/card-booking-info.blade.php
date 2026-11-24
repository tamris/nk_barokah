                <!-- Booking Information -->
                <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-6">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Booking Information</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-full bg-[#F4F8F7] border border-[#E8F0EE] text-brand-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-slate-400 block">Event Date</span>
                                <span class="text-sm font-bold text-slate-900 mt-0.5 block" x-text="activeDetail?.date"></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-full bg-[#F4F8F7] border border-[#E8F0EE] text-brand-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-slate-400 block">Booked At</span>
                                <span class="text-sm font-bold text-slate-900 mt-0.5 block" x-text="activeDetail?.created_at_label"></span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-5 border-t border-[#EFF4F2] flex flex-wrap items-center gap-4 text-xs">
                        <span class="inline-flex items-center gap-1.5 font-semibold" :class="activeDetail?.synced_to_sheets ? 'text-emerald-700' : 'text-amber-600'">
                            <span class="w-2 h-2 rounded-full" :class="activeDetail?.synced_to_sheets ? 'bg-emerald-500' : 'bg-amber-400'"></span>
                            <span x-text="activeDetail?.synced_to_sheets ? 'Google Sheets Synced' : 'Google Sheets Queued'"></span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 font-semibold" :class="activeDetail?.google_calendar_synced ? 'text-emerald-700' : 'text-slate-400'">
                            <span class="w-2 h-2 rounded-full" :class="activeDetail?.google_calendar_synced ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                            <span x-text="activeDetail?.google_calendar_synced ? 'Google Calendar Registered' : 'Not on Calendar'"></span>
                        </span>
                    </div>
                </div>
