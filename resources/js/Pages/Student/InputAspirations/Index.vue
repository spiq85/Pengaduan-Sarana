<script setup>
import { Link, Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";
import NotificationBell from "@/Components/NotificationBell.vue";

const props = defineProps({
    aspirations: Object,
    student: Object,
});

const sidebarOpen = ref(false);

const progressWidth = (status) => {
    switch (status) {
        case "Belum Dimulai":
            return "10%";
        case "Dalam Proses":
            return "50%";
        case "Selesai":
            return "100%";
        default:
            return "0%";
    }
};

// --- SweetAlert Logout ---
const handleLogout = () => {
    Swal.fire({
        title: "MAU CABUT, CUY?",
        text: "Sesi kamu bakal berakhir di sini.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#0f172a", // slate-900
        cancelButtonColor: "#f43f5e", // rose-500
        confirmButtonText: "YAP, LOGOUT!",
        cancelButtonText: "BATAL",
        background: "#ffffff",
        borderRadius: "2.5rem",
        customClass: {
            title: "font-black tracking-tighter italic",
            confirmButton:
                "rounded-2xl px-6 py-3 font-black text-xs tracking-widest",
            cancelButton:
                "rounded-2xl px-6 py-3 font-black text-xs tracking-widest",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            router.post("/student/logout");
        }
    });
};
</script>

<template>
    <Head title="Riwayat Aspirasi" />

    <div class="flex min-h-screen bg-slate-50 font-sans">
        <!-- Mobile Header -->
        <div class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-white border-b border-slate-100 px-4 py-3 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 bg-blue-600 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-rocket text-xs"></i>
                </div>
                <span class="font-black text-sm italic uppercase tracking-tighter">Command<span class="text-blue-600">Center</span></span>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                <i :class="sidebarOpen ? 'fas fa-times' : 'fas fa-bars'"></i>
            </button>
        </div>

        <!-- Overlay -->
        <div v-if="sidebarOpen" @click="sidebarOpen = false" class="lg:hidden fixed inset-0 bg-black/40 z-40 backdrop-blur-sm"></div>

        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="w-72 bg-white border-r border-slate-100 flex flex-col p-6 fixed lg:sticky top-0 h-screen z-50 lg:translate-x-0 transition-transform duration-300"
        >
            <div class="mb-10 px-2 flex items-center gap-3">
                <div
                    class="h-10 w-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200"
                >
                    <i class="fas fa-rocket text-sm"></i>
                </div>
                <h2
                    class="font-black text-slate-800 tracking-tighter text-xl italic uppercase"
                >
                    Command<span class="text-blue-600">Center</span>
                </h2>
            </div>

            <nav class="flex-1 space-y-2">
                <p
                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 ml-2"
                >
                    Main Menu
                </p>
                <Link
                    href="/student/dashboard"
                    class="flex items-center gap-4 px-4 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all group text-slate-400 hover:bg-slate-50 hover:text-blue-600"
                >
                    <i class="fas fa-th-large text-lg"></i>
                    <span>Dashboard</span>
                </Link>
                <Link
                    href="/student/input-aspirations"
                    class="flex items-center gap-4 px-4 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all bg-slate-900 text-white shadow-xl shadow-slate-200"
                >
                    <i class="fas fa-paper-plane text-lg"></i>
                    <span>Aspirasiku</span>
                </Link>
                <Link
                    href="/student/global"
                    class="flex items-center gap-4 px-4 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all text-slate-400 hover:bg-slate-50 hover:text-blue-600"
                >
                    <i class="fas fa-globe text-lg"></i>
                    <span>Jelajah</span>
                </Link>
            </nav>

            <Link
                href="/student/profile"
                class="flex items-center gap-4 px-4 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-slate-400 hover:bg-slate-50 hover:text-blue-600 transition-all"
                :class="{
                    'bg-slate-900 text-white shadow-xl shadow-slate-200':
                        $page.url === '/student/profile',
                }"
            >
                <i class="fas fa-user-circle text-lg"></i>
                <span>Profilku</span>
            </Link>

            <div class="mt-auto pt-6 border-t border-slate-100 space-y-4">
                <button
                    @click="handleLogout"
                    class="w-full flex items-center gap-4 px-4 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-rose-500 hover:bg-rose-50 transition-all text-left"
                >
                    <i class="fas fa-power-off text-lg"></i>
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <main class="flex-1 p-6 lg:p-10 overflow-y-auto pt-20 lg:pt-6">
            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10"
            >
                <div>
                    <h1
                        class="text-4xl font-black text-slate-800 tracking-tighter uppercase italic"
                    >
                        Riwayat <span class="text-blue-600">Aspirasimu</span>
                    </h1>
                    <p class="text-slate-500 font-medium">
                        Pantau status perbaikan fasilitas sekolah secara
                        realtime.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <NotificationBell />
                    <Link
                        href="/student/input-aspirations/create"
                        class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs tracking-widest uppercase hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-2 active:scale-95"
                    >
                        <i class="fas fa-plus"></i> Tambah Aspirasi
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div
                    v-for="item in aspirations.data"
                    :key="item.id_input"
                    class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-blue-900/5 transition-all group"
                >
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <div
                            class="w-full md:w-40 h-32 rounded-[2rem] overflow-hidden bg-slate-100 flex-shrink-0 relative"
                        >
                            <img
                                v-if="item.image"
                                :src="`/storage/${item.image}`"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                            />
                            <div
                                v-else
                                class="w-full h-full flex items-center justify-center text-slate-400 text-[10px] font-black uppercase italic"
                            >
                                No Photo
                            </div>
                        </div>

                        <div class="flex-1 w-full space-y-2 text-left">
                            <div class="flex items-center gap-2">
                                <span
                                    class="px-3 py-1 bg-blue-50 text-blue-600 text-[9px] font-black uppercase rounded-lg border border-blue-100"
                                    >{{ item.category?.category_name }}</span
                                >
                                <span
                                    :class="[
                                        'px-3 py-1 text-[9px] font-black uppercase rounded-lg border',
                                        item.submission_status === 'menunggu'
                                            ? 'bg-amber-50 text-amber-600 border-amber-100'
                                            : 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    ]"
                                >
                                    {{ item.submission_status }}
                                </span>
                            </div>
                            <h3
                                class="font-black text-slate-800 text-xl uppercase tracking-tighter"
                            >
                                {{ item.location }}
                            </h3>
                            <p
                                class="text-slate-500 text-sm line-clamp-1 font-medium"
                            >
                                {{ item.description }}
                            </p>
                        </div>

                        <div
                            class="w-full md:w-56 space-y-3 bg-slate-50/50 p-4 rounded-3xl border border-slate-50"
                        >
                            <p
                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest"
                            >
                                Repair Progress
                            </p>
                            <div
                                class="h-2.5 w-full bg-slate-200 rounded-full overflow-hidden p-0.5"
                            >
                                <div
                                    :style="{
                                        width: progressWidth(
                                            item.aspiration?.progress_status,
                                        ),
                                    }"
                                    class="h-full bg-gradient-to-r from-blue-600 to-cyan-400 rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(37,99,235,0.3)]"
                                ></div>
                            </div>
                            <p
                                class="text-[10px] font-black text-slate-700 uppercase italic"
                            >
                                {{
                                    item.aspiration?.progress_status ||
                                    "🕒 Checking..."
                                }}
                            </p>
                        </div>

                        <div class="flex gap-2 w-full md:w-auto">
                            <Link
                                v-if="item.submission_status === 'menunggu'"
                                :href="`/student/input-aspirations/${item.id_input}/edit`"
                                class="flex-1 md:flex-none p-5 bg-amber-50 text-amber-600 rounded-2xl hover:bg-amber-500 hover:text-white transition-all text-center active:scale-90"
                            >
                                <i class="fas fa-edit"></i>
                            </Link>
                            <Link
                                :href="`/student/input-aspirations/${item.id_input}`"
                                class="flex-1 md:flex-none p-5 bg-slate-900 text-white rounded-2xl hover:bg-blue-600 transition-all text-center active:scale-90"
                            >
                                <i class="fas fa-eye"></i>
                            </Link>
                        </div>
                    </div>
                </div>

                <div
                    v-if="aspirations.data.length === 0"
                    class="text-center py-20 bg-white rounded-[3rem] border border-dashed border-slate-200"
                >
                    <span class="text-5xl block mb-4">📂</span>
                    <p
                        class="text-slate-400 font-black uppercase text-xs tracking-widest"
                    >
                        Belum ada aspirasi yang dikirim
                    </p>
                </div>

                <!-- Pagination -->
                <div v-if="aspirations.links && aspirations.links.length > 3" class="flex items-center justify-center gap-2 mt-8">
                    <template v-for="(link, index) in aspirations.links" :key="index">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="px-5 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all"
                            :class="link.active
                                ? 'bg-slate-900 text-white shadow-xl shadow-slate-200'
                                : 'bg-white text-slate-400 hover:bg-slate-50 hover:text-blue-600 border border-slate-100'"
                            v-html="link.label"
                            preserve-scroll
                        />
                        <span
                            v-else
                            class="px-5 py-3 rounded-2xl font-black text-xs uppercase tracking-widest bg-slate-50 text-slate-300 cursor-not-allowed"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
::-webkit-scrollbar {
    width: 6px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
</style>
