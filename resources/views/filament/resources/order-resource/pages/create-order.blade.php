<x-filament-panels::page
    @class([
        'fi-resource-create-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
    ])
>
    <div class="absolute inset-x-0 top-0 -z-10 h-40 bg-[linear-gradient(180deg,_rgba(240,249,255,0.95),_rgba(240,249,255,0))] dark:bg-[linear-gradient(180deg,_rgba(15,23,42,0.85),_rgba(15,23,42,0))]"></div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_24rem]">
        <x-filament-panels::form
            id="form"
            :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
            wire:submit="create"
        >
            <div class="space-y-6">
                <section class="rounded-[1.5rem] border border-slate-200/70 bg-white/92 px-6 py-6 shadow-[0_18px_60px_-38px_rgba(15,23,42,0.35)] ring-1 ring-slate-200/60 dark:border-slate-800 dark:bg-slate-900/88 dark:ring-slate-700/60">
                    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-[0.7rem] font-semibold uppercase tracking-[0.32em] text-sky-700 dark:text-sky-300">Point Of Sale</p>
                            <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">Create order</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                                Add products, optionally attach a client, then complete the sale.
                            </p>
                        </div>
                        <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-950/40 dark:text-slate-300">
                            Shortcut: Ctrl/Cmd + S
                        </span>
                    </div>
                </section>

                <section class="rounded-[1.5rem] border border-slate-200/70 bg-white/92 p-3 shadow-[0_18px_60px_-38px_rgba(15,23,42,0.38)] ring-1 ring-slate-200/60 dark:border-slate-800 dark:bg-slate-900/88 dark:ring-slate-700/60">
                    <div class="rounded-[1.2rem] bg-slate-50/75 p-4 dark:bg-slate-950/30">
                        {{ $this->form }}
                        <x-filament-panels::form.actions
                            :actions="$this->getCachedFormActions()"
                            :full-width="$this->hasFullWidthFormActions()"
                        />
                    </div>
                </section>
            </div>
        </x-filament-panels::form>

        <aside class="space-y-6 xl:sticky xl:top-6">
            <section class="overflow-hidden rounded-[1.5rem] border border-slate-200/70 bg-white/92 shadow-[0_18px_60px_-38px_rgba(15,23,42,0.4)] ring-1 ring-slate-200/60 dark:border-slate-800 dark:bg-slate-900/88 dark:ring-slate-700/60">
                <div class="border-b border-slate-200/70 bg-slate-50/80 px-6 py-5 dark:border-slate-800 dark:bg-slate-950/35">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500 dark:text-slate-400">Live receipt</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-950 dark:text-white">Sale summary</h3>
                        </div>
                        <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 dark:border-sky-500/20 dark:bg-sky-500/10 dark:text-sky-300">
                            {{ $this->getCartQuantity() }} units
                        </span>
                    </div>
                </div>

                <div class="px-6 py-5">
                    <div class="mb-5 flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600 dark:bg-slate-950/35 dark:text-slate-300">
                        <span>{{ count($this->getCartItems()) }} line(s)</span>
                        <span>{{ $this->getCartQuantity() }} unit(s)</span>
                    </div>

                    <div class="space-y-3">
                        @forelse ($this->getCartItems() as $item)
                            <div class="rounded-2xl border border-slate-200/70 bg-[linear-gradient(180deg,_rgba(248,250,252,0.95),_rgba(241,245,249,0.88))] p-4 dark:border-slate-800 dark:bg-[linear-gradient(180deg,_rgba(30,41,59,0.72),_rgba(15,23,42,0.82))]">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-medium text-slate-950 dark:text-white">{{ $item['name'] }}</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="rounded-full bg-white px-2.5 py-1 text-xs font-medium text-slate-600 shadow-sm dark:bg-slate-900 dark:text-slate-300">
                                                Qty {{ $item['quantity'] }}
                                            </span>
                                            <span class="rounded-full bg-white px-2.5 py-1 text-xs font-medium text-slate-600 shadow-sm dark:bg-slate-900 dark:text-slate-300">
                                                {{ number_format($item['price'], 2) }} XFA each
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-right text-base font-semibold text-slate-950 dark:text-white">{{ number_format($item['line_total'], 2) }} XFA</p>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 p-5 text-sm leading-6 text-slate-500 dark:border-slate-700 dark:bg-slate-950/30 dark:text-slate-400">
                                Select products to build the cart. Each line added on the left appears here immediately as a clean receipt preview.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6 rounded-[1.4rem] bg-slate-950 px-5 py-4 text-white shadow-lg dark:bg-slate-100 dark:text-slate-950">
                        <div class="flex items-center justify-between text-sm text-white/70 dark:text-slate-600">
                            <span>Amount due</span>
                            <span>{{ $this->getCartQuantity() }} unit(s)</span>
                        </div>
                        <div class="mt-2 flex items-end justify-between gap-4">
                            <span class="text-3xl font-semibold tracking-tight">{{ number_format($this->getCartTotal(), 2) }} XFA</span>
                        </div>
                    </div>

                    @if ($client = $this->getSelectedClient())
                        <div class="mt-6 rounded-2xl bg-slate-50/85 p-5 dark:bg-slate-950/35">
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-sm font-semibold text-slate-950 dark:text-white">Client</p>
                                <span class="rounded-full border border-slate-200 px-2.5 py-1 text-[11px] font-semibold text-slate-600 dark:border-slate-700 dark:text-slate-300">
                                    Registered
                                </span>
                            </div>
                            <p class="text-lg font-semibold text-slate-950 dark:text-white">{{ $client->name }}</p>
                            <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                                <div class="flex items-center justify-between gap-4 border-b border-slate-200/70 pb-3 dark:border-slate-800">
                                    <span class="uppercase tracking-[0.22em] text-xs text-slate-400 dark:text-slate-500">Phone</span>
                                    <span class="text-right">{{ $client->phone ?: 'No phone on file' }}</span>
                                </div>
                                <div class="flex items-start justify-between gap-4">
                                    <span class="uppercase tracking-[0.22em] text-xs text-slate-400 dark:text-slate-500">Address</span>
                                    <span class="max-w-[14rem] text-right">{{ $client->address ?: 'No address on file' }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 p-5 text-sm leading-6 text-slate-500 dark:border-slate-700 dark:bg-slate-950/30 dark:text-slate-400">
                            This sale will be saved as a walk-in order unless you attach a saved client from the form.
                        </div>
                    @endif
                </div>
            </section>
        </aside>
    </div>

    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>
