                @if($isOwner ?? false)
                <!-- Payment Proof (Owner Only) -->
                <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">{{ __('admin.detail.payment_proof') }}</h2>
                        <template x-if="activeDetail?.payment_proof_url">
                            <a :href="activeDetail.payment_proof_url" target="_blank" class="text-xs font-semibold text-brand-600 hover:underline">
                                {{ __('admin.detail.open_full_res') }}
                            </a>
                        </template>
                    </div>

                    <template x-if="activeDetail?.payment_proof_url && !activeDetail?.is_proof_pdf">
                        <a :href="activeDetail.payment_proof_url" target="_blank" class="block rounded-2xl overflow-hidden border border-[#EFF4F2] bg-[#F8FAFA]">
                            <img :src="activeDetail.payment_proof_url" alt="{{ __('admin.detail.payment_proof') }}" class="w-full max-h-96 object-contain mx-auto">
                        </a>
                    </template>
                    <template x-if="activeDetail?.payment_proof_url && activeDetail?.is_proof_pdf">
                        <div class="p-6 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] text-center space-y-3">
                            <p class="text-xs font-bold text-slate-700">{{ __('admin.detail.pdf_transfer_title') }}</p>
                            <a :href="activeDetail.payment_proof_url" target="_blank" class="inline-block px-5 py-2 rounded-full bg-brand-600 text-white text-xs font-semibold">
                                {{ __('admin.detail.open_pdf_doc') }}
                            </a>
                        </div>
                    </template>
                    <template x-if="!activeDetail?.payment_proof_url">
                        <p class="text-xs text-slate-400 py-4 text-center">{{ __('admin.detail.no_proof_uploaded') }}</p>
                    </template>
                </div>
                @endif
