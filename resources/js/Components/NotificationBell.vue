<script setup>
import { ref } from "vue";
import { usePage, router } from "@inertiajs/vue3";

const showNotif = ref(false);

const page = usePage();

const handleNotifClick = (notification) => {
    if (!notification.read_at) {
        router.post(
            `/student/notifications/${notification.id}/read`,
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    showNotif.value = false;
                },
            },
        );
    } else {
        router.visit("/student/input-aspirations");
        showNotif.value = false;
    }
};

const notifIcon = (type) => {
    switch (type) {
        case "approved":
            return "fas fa-check-circle text-emerald-500";
        case "rejected":
            return "fas fa-times-circle text-rose-500";
        case "progress":
            return "fas fa-tools text-blue-500";
        default:
            return "fas fa-bell text-slate-400";
    }
};

const closeDropdown = () => {
    showNotif.value = false;
};
</script>

<template>
    <div class="relative">
        <button
            @click="showNotif = !showNotif"
            class="relative w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-500 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm"
        >
            <i class="fas fa-bell text-sm"></i>
            <span
                v-if="page.props.unreadCount > 0"
                class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 text-white text-[9px] font-black rounded-full flex items-center justify-center shadow-lg shadow-rose-200 animate-pulse"
            >
                {{ page.props.unreadCount > 9 ? "9+" : page.props.unreadCount }}
            </span>
        </button>

        <!-- Overlay to close -->
        <div
            v-if="showNotif"
            @click="closeDropdown"
            class="fixed inset-0 z-40"
        ></div>

        <!-- Dropdown -->
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95 -translate-y-2"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 -translate-y-2"
        >
            <div
                v-if="showNotif"
                class="absolute right-0 mt-3 w-80 bg-white rounded-3xl border border-slate-100 shadow-2xl shadow-slate-200/50 z-50 overflow-hidden"
            >
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h4 class="font-black text-xs text-slate-800 uppercase tracking-widest">
                        Notifikasi
                    </h4>
                    <span
                        v-if="page.props.unreadCount > 0"
                        class="px-2 py-0.5 bg-rose-50 text-rose-600 text-[9px] font-black rounded-lg"
                    >
                        {{ page.props.unreadCount }} baru
                    </span>
                </div>

                <div class="max-h-80 overflow-y-auto">
                    <div
                        v-if="page.props.notifications?.length === 0"
                        class="p-8 text-center"
                    >
                        <i class="far fa-bell-slash text-3xl text-slate-200 mb-3 block"></i>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            Belum ada notifikasi
                        </p>
                    </div>

                    <button
                        v-for="notif in page.props.notifications"
                        :key="notif.id"
                        @click="handleNotifClick(notif)"
                        class="w-full text-left p-4 hover:bg-slate-50 transition-all border-b border-slate-50 last:border-0 flex items-start gap-3"
                        :class="{ 'bg-blue-50/50': !notif.read_at }"
                    >
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 bg-slate-50">
                            <i :class="notifIcon(notif.data?.type)"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-700 leading-relaxed line-clamp-2">
                                {{ notif.data?.message || "Aspirasi kamu telah diperbarui" }}
                            </p>
                            <p class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-wider">
                                {{ new Date(notif.created_at).toLocaleDateString("id-ID", { day: "numeric", month: "short", year: "numeric" }) }}
                            </p>
                        </div>
                        <span
                            v-if="!notif.read_at"
                            class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1.5"
                        ></span>
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>
