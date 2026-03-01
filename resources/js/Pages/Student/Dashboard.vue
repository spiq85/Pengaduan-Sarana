<script setup>
import { onMounted } from "vue";
import { ref } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import NotificationBell from "@/Components/NotificationBell.vue";

const props = defineProps({
    student: Object,
    total: Number,
    diterima: Number,
    menunggu: Number,
});

const sidebarOpen = ref(false);

onMounted(() => {
    const flash = usePage().props.flash;
    if (flash?.message) {
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: flash.message,
            showConfirmButton: false,
            timer: 3000,
        });
    }
});

// --- SWEETALERT LOGOUT ---
const handleLogout = () => {
    Swal.fire({
        title: "YAKIN MAU KELUAR?",
        text: "Sesi kamu bakal berakhir di sini, cuy.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#0f172a",
        cancelButtonColor: "#f43f5e",
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
    <Head title="Dashboard Siswa" />

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
                    class="flex items-center gap-4 px-4 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all bg-slate-900 text-white shadow-xl shadow-slate-200"
                >
                    <i class="fas fa-th-large text-lg"></i>
                    <span>Dashboard</span>
                </Link>
                <Link
                    href="/student/input-aspirations"
                    class="flex items-center gap-4 px-4 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-slate-400 hover:bg-slate-50 hover:text-blue-600 transition-all"
                >
                    <i class="fas fa-paper-plane text-lg"></i>
                    <span>Aspirasiku</span>
                </Link>
                <Link
                    href="/student/global"
                    class="flex items-center gap-4 px-4 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-slate-400 hover:bg-slate-50 hover:text-blue-600 transition-all"
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
                <span>{{ student.username }}</span>
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
                class="flex flex-col md:flex-row justify-between items-start gap-4 mb-10"
            >
                <div>
                    <h1
                        class="text-4xl font-black tracking-tighter text-slate-800 uppercase italic"
                    >
                        Command <span class="text-blue-600">Center</span> 🎓
                    </h1>
                    <p class="text-slate-500 font-medium mt-1">
                        Pantau & sampaikan aspirasimu dalam satu pintu.
                    </p>
                </div>
                <NotificationBell />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div
                    class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden group hover:border-blue-200 transition-all"
                >
                    <div class="relative z-10">
                        <p
                            class="text-blue-600 font-black uppercase text-[10px] tracking-widest mb-1"
                        >
                            Total Laporan
                        </p>
                        <h2 class="text-5xl font-black text-slate-800">
                            {{ total }}
                        </h2>
                    </div>
                    <i
                        class="fas fa-folder-open absolute -right-4 -bottom-4 text-8xl text-slate-50 group-hover:text-blue-50 transition-colors"
                    ></i>
                </div>
                <div
                    class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden group hover:border-amber-200 transition-all"
                >
                    <div class="relative z-10">
                        <p
                            class="text-amber-600 font-black uppercase text-[10px] tracking-widest mb-1"
                        >
                            Menunggu
                        </p>
                        <h2 class="text-5xl font-black text-slate-800">
                            {{ menunggu }}
                        </h2>
                    </div>
                    <i
                        class="fas fa-clock absolute -right-4 -bottom-4 text-8xl text-slate-50 group-hover:text-amber-50 transition-colors"
                    ></i>
                </div>
                <div
                    class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden group hover:border-emerald-200 transition-all"
                >
                    <div class="relative z-10">
                        <p
                            class="text-emerald-600 font-black uppercase text-[10px] tracking-widest mb-1"
                        >
                            Disetujui
                        </p>
                        <h2 class="text-5xl font-black text-slate-800">
                            {{ diterima }}
                        </h2>
                    </div>
                    <i
                        class="fas fa-check-circle absolute -right-4 -bottom-4 text-8xl text-slate-50 group-hover:text-emerald-50 transition-colors"
                    ></i>
                </div>
            </div>

            <div
                class="bg-slate-900 rounded-[3rem] p-10 md:p-16 text-center relative overflow-hidden shadow-2xl shadow-slate-400"
            >
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-blue-600/20 blur-[100px]"
                ></div>
                <div class="relative z-10 max-w-xl mx-auto">
                    <h3
                        class="text-3xl font-black text-white mb-4 uppercase italic tracking-tighter"
                    >
                        Ada fasilitas yang
                        <span class="text-blue-500">rusak?</span>
                    </h3>
                    <p
                        class="text-slate-400 font-medium mb-10 text-sm leading-relaxed"
                    >
                        Jangan diam saja! Laporkan sekarang dan pantau secara
                        real-time.
                    </p>
                    <Link
                        href="/student/input-aspirations/create"
                        class="inline-flex items-center gap-3 bg-white text-slate-900 px-10 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-blue-600 hover:text-white transition-all shadow-xl shadow-white/5 active:scale-95"
                    >
                        <i class="fas fa-plus-circle"></i> Buat Laporan Baru
                    </Link>
                </div>
            </div>
        </main>
    </div>
</template>
