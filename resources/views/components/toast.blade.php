<div
    x-data="{
        notices: [],
        add(notice) {
            notice.id = Date.now();
            this.notices.push(notice);
            setTimeout(() => {
                this.remove(notice.id);
            }, notice.duration || 4000);
        },
        remove(id) {
            this.notices = this.notices.filter(n => n.id !== id);
        }
    }"
    x-on:toast.window="add($event.detail)"
    class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none"
    aria-live="polite"
>
    <template x-for="notice in notices" :key="notice.id">
        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="pointer-events-auto bg-white border rounded-[10px] shadow-lg p-4 flex items-start gap-3"
            :class="{
                'border-green-200 bg-green-50/40 text-green-800': notice.type === 'success',
                'border-red-200 bg-red-50/40 text-red-800': notice.type === 'error',
                'border-gray-200 text-gray-800': !notice.type || notice.type === 'info'
            }"
        >
            <div class="shrink-0 mt-0.5">
                <template x-if="notice.type === 'success'">
                    <span class="text-green-600 font-bold">✓</span>
                </template>
                <template x-if="notice.type === 'error'">
                    <span class="text-red-600 font-bold">▲</span>
                </template>
            </div>
            <div class="flex-1 text-sm">
                <p x-text="notice.message" class="font-medium"></p>
            </div>
            <button type="button" x-on:click="remove(notice.id)" class="text-gray-400 hover:text-gray-600 shrink-0">
                &times;
            </button>
        </div>
    </template>
</div>

