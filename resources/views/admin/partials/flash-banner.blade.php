        <!-- Sleek App Bar Notification Banner (Positioned Right Below Top App Bar, Auto-dismiss, Progress Bar, Close Button) -->
        <div x-data="{
                show: {{ (session('success') || session('error')) ? 'true' : 'false' }},
                type: '{{ session('error') ? 'error' : 'success' }}',
                title: '{{ session('error') ? __('admin.toast.error') : __('admin.toast.success') }}',
                message: {{ Js::from(session('success') ?? session('error') ?? '') }},
                progress: 100,
                duration: 4500,
                remaining: 4500,
                paused: false,
                interval: null,
                init() {
                    window.addEventListener('toast', (e) => {
                        this.trigger(e.detail.message, e.detail.type || 'success', e.detail.title);
                    });
                    if (this.show && this.message) {
                        this.startTimer();
                    }
                },
                trigger(msg, t = 'success', customTitle = null) {
                    this.message = msg;
                    this.type = t;
                    this.title = customTitle || (t === 'error' ? '{{ __('admin.toast.error') }}' : '{{ __('admin.toast.success') }}');
                    this.show = true;
                    this.remaining = this.duration;
                    this.progress = 100;
                    this.startTimer();
                },
                startTimer() {
                    if (this.interval) clearInterval(this.interval);
                    const step = 50;
                    this.interval = setInterval(() => {
                        if (!this.paused && this.show) {
                            this.remaining -= step;
                            this.progress = Math.max(0, (this.remaining / this.duration) * 100);
                            if (this.remaining <= 0) {
                                this.close();
                            }
                        }
                    }, step);
                },
                pause() {
                    this.paused = true;
                },
                resume() {
                    this.paused = false;
                },
                close() {
                    if (this.interval) clearInterval(this.interval);
                    this.show = false;
                }
             }"
             x-show="show"
             x-cloak
             @mouseenter="pause()"
             @mouseleave="resume()"
             x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-[0.99]"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition cubic-bezier(0.16, 1, 0.3, 1) duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-2 scale-[0.99]"
             class="mt-4 sm:mt-5 rounded-2xl border shadow-card overflow-hidden select-none"
             :class="type === 'error' 
                 ? 'bg-gradient-to-r from-rose-50/90 via-white to-white border-rose-200/90 text-rose-900' 
                 : 'bg-gradient-to-r from-emerald-50/80 via-white to-white border-emerald-200/90 text-slate-800'">

            <div class="p-3 sm:p-3.5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Icon Badge -->
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 shadow-xs"
                         :class="type === 'error' ? 'bg-gradient-to-br from-rose-500 to-rose-700 text-white' : 'bg-gradient-to-br from-brand-500 to-brand-700 text-white'">
                        <template x-if="type === 'error'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </template>
                        <template x-if="type !== 'error'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </template>
                    </div>

                    <!-- Message Body -->
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded"
                                  :class="type === 'error' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-800'"
                                  x-text="title"></span>
                            <span class="text-[10px] text-slate-400 font-medium hidden sm:inline">{{ __('admin.toast.just_now') }}</span>
                        </div>
                        <p class="text-xs font-semibold text-slate-800 mt-0.5 leading-snug break-words"
                           x-text="message"></p>
                    </div>
                </div>

                <!-- Dismiss Button -->
                <button type="button" @click="close()"
                        class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100/80 active:scale-90 flex items-center justify-center transition shrink-0 cursor-pointer"
                        title="{{ __('admin.toast.dismiss') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Progress Bar Countdown Line -->
            <div class="w-full bg-slate-100 h-1 overflow-hidden">
                <div class="h-full transition-[width] duration-75 ease-linear"
                     :class="type === 'error' ? 'bg-gradient-to-r from-rose-500 to-rose-600' : 'bg-gradient-to-r from-brand-500 to-brand-600'"
                     :style="`width: ${progress}%`"></div>
            </div>

        </div>
