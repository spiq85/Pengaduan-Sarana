<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from "vue";
import Swal from "sweetalert2";
import NotificationBell from "@/Components/NotificationBell.vue";

const props = defineProps({
    student: Object,
    stats: Object,
});

// Logic Inisial ala WhatsApp
const getInitials = computed(() => {
    if (!props.student?.username) return "??";
    const names = props.student.username.trim().split(" ");
    if (names.length > 1) {
        return (names[0][0] + names[1][0]).toUpperCase();
    }
    return names[0][0].toUpperCase();
});

// Handle Logout
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
    <Head title="Profil Saya" />

    <div v-if="student" class="flex min-h-screen bg-[#FDFDFF] font-sans">
        <aside
            class="w-72 bg-white border-r border-slate-100 flex flex-col p-8 sticky top-0 h-screen z-40 hidden lg:flex"
        >
            <div class="mb-12 flex items-center gap-3">
                <div
                    class="bg-blue-600 h-10 w-10 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200 shrink-0"
                >
                    <i class="fas fa-rocket text-sm"></i>
                </div>
                <div class="flex flex-col leading-none">
                    <h1
                        class="text-[18px] font-[1000] italic tracking-tighter text-slate-900 flex items-center"
                    >
                        COMMAND<span class="text-blue-600">CENTER</span>
                    </h1>
                </div>
            </div>

            <nav class="flex-1">
                <div class="space-y-3">
                    <Link
                        href="/student/dashboard"
                        class="flex items-center gap-4 px-5 py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 hover:bg-blue-600 hover:text-white transition-all group"
                    >
                        <div class="w-5 flex justify-center items-center">
                            <i class="fas fa-arrow-left text-xs"></i>
                        </div>
                        <span>Kembali Ke Dashboard</span>
                    </Link>
                </div>
            </nav>

            <div class="mt-auto pt-6 border-t border-slate-50">
                <button
                    @click="handleLogout"
                    class="w-full flex items-center gap-4 px-5 py-2 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] text-rose-500 hover:text-rose-600 transition-all group"
                >
                    <div class="w-5 flex justify-center items-center">
                        <i class="fas fa-power-off text-sm"></i>
                    </div>
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <main class="flex-1 p-6 lg:p-12">
            <div class="max-w-4xl mx-auto">
                <div class="flex justify-end mb-4">
                    <NotificationBell />
                </div>
                <div
                    class="bg-white rounded-[3.5rem] border border-slate-100 shadow-2xl shadow-slate-200/40 overflow-hidden mb-10"
                >
                    <div
                        class="h-56 bg-gradient-to-tr from-blue-700 via-blue-600 to-cyan-400 relative"
                    >
                        <div
                            class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"
                        ></div>
                        <div class="absolute -bottom-14 left-12">
                            <div
                                class="p-2 bg-white rounded-[2.5rem] shadow-2xl shadow-blue-900/10"
                            >
                                <div
                                    class="h-32 w-32 bg-slate-900 rounded-[2rem] flex items-center justify-center text-5xl font-black text-white tracking-tighter ring-8 ring-slate-50"
                                >
                                    {{ getInitials }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-20 px-12 pb-12">
                        <div
                            class="flex flex-col md:flex-row md:items-center justify-between gap-8"
                        >
                            <div class="space-y-2">
                                <div
                                    class="inline-flex items-center gap-2 bg-blue-50 text-blue-600 px-4 py-1.5 rounded-full border border-blue-100"
                                >
                                    <div
                                        class="h-1.5 w-1.5 bg-blue-600 rounded-full animate-pulse"
                                    ></div>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-widest"
                                        >Student Official Account</span
                                    >
                                </div>
                                <h2
                                    class="text-5xl font-black text-slate-900 uppercase italic tracking-tighter leading-none"
                                >
                                    {{ student.username }}
                                </h2>
                            </div>

                            <div class="flex gap-3">
                                <div
                                    class="bg-slate-50 p-5 rounded-3xl border border-slate-100 text-center min-w-[110px]"
                                >
                                    <p
                                        class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1"
                                    >
                                        Total Laporan
                                    </p>
                                    <p
                                        class="text-2xl font-black text-slate-900"
                                    >
                                        {{ stats?.total || 0 }}
                                    </p>
                                </div>
                                <div
                                    class="bg-amber-50 p-5 rounded-3xl border border-amber-100 text-center min-w-[110px]"
                                >
                                    <p
                                        class="text-[8px] font-black text-amber-600 uppercase tracking-widest mb-1"
                                    >
                                        Pending
                                    </p>
                                    <p
                                        class="text-2xl font-black text-amber-700"
                                    >
                                        {{ stats?.pending || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
                    <div
                        class="md:col-span-3 bg-white p-10 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-100"
                    >
                        <div class="flex items-center gap-4 mb-8">
                            <div
                                class="h-10 w-10 bg-slate-900 text-white rounded-xl flex items-center justify-center"
                            >
                                <i class="fas fa-fingerprint"></i>
                            </div>
                            <h3
                                class="text-sm font-black uppercase italic text-slate-800"
                            >
                                Identitas Digital
                            </h3>
                        </div>

                        <div class="space-y-6">
                            <div class="group">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1"
                                    >Username Login</label
                                >
                                <div
                                    class="p-5 bg-slate-50 rounded-2xl border-2 border-slate-50 group-hover:border-blue-100 transition-all font-bold text-slate-700"
                                >
                                    {{ student.username }}
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="group">
                                    <label
                                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1"
                                        >NIS Siswa</label
                                    >
                                    <div
                                        class="p-5 bg-slate-50 rounded-2xl border-2 border-slate-50 group-hover:border-blue-100 transition-all font-bold text-slate-700 flex items-center gap-3"
                                    >
                                        <i
                                            class="fas fa-id-card text-blue-500 text-xs"
                                        ></i>
                                        {{ student.nis || "N/A" }}
                                    </div>
                                </div>
                                <div class="group">
                                    <label
                                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1"
                                        >Tingkatan / Kelas</label
                                    >
                                    <div
                                        class="p-5 bg-slate-50 rounded-2xl border-2 border-slate-50 group-hover:border-blue-100 transition-all font-bold text-slate-700 flex items-center gap-3"
                                    >
                                        <i
                                            class="fas fa-graduation-cap text-blue-500 text-xs"
                                        ></i>
                                        {{ student.class || "Belum diatur" }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <div
                            class="bg-slate-900 p-8 rounded-[3rem] text-white shadow-2xl shadow-blue-900/20 relative overflow-hidden h-full flex flex-col justify-center items-center"
                        >
                            <div class="relative z-10 text-center">
                                <p
                                    class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-400 mb-4"
                                >
                                    Waktu Registrasi
                                </p>
                                <i
                                    class="fas fa-calendar-check text-4xl mb-6 text-white/20"
                                ></i>
                                <p
                                    class="text-2xl font-black italic uppercase leading-tight"
                                >
                                    {{
                                        student.created_at
                                            ? new Date(
                                                  student.created_at,
                                              ).toLocaleDateString("id-ID", {
                                                  day: "numeric",
                                                  month: "long",
                                                  year: "numeric",
                                              })
                                            : "-"
                                    }}
                                </p>
                            </div>
                            <div
                                class="absolute -right-10 -bottom-10 h-32 w-32 bg-blue-600 rounded-full blur-[50px] opacity-30"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
.transition-all {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
