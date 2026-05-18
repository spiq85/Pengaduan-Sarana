<script setup>
import { onMounted, ref, computed } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import NotificationBell from "@/Components/NotificationBell.vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";

dayjs.extend(relativeTime);

const props = defineProps({
    student: Object,
    total: Number,
    diterima: Number,
    menunggu: Number,
    recentAspirations: {
        type: Array,
        default: () => [],
    },
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

const handleLogout = () => {
    Swal.fire({
        title: "Yakin mau keluar?",
        text: "Sesi kamu bakal berakhir di sini, cuy.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#0f172a",
        cancelButtonColor: "#f43f5e",
        confirmButtonText: "Yap, Logout!",
        cancelButtonText: "Batal",
        borderRadius: "2rem",
    }).then((result) => {
        if (result.isConfirmed) router.post("/student/logout");
    });
};

const progressWidth = (status) => {
    switch (status) {
        case "Belum Dimulai": return "15%";
        case "Dalam Proses": return "60%";
        case "Observasi": return "80%";
        case "Selesai": return "100%";
        default: return "5%";
    }
};

const slaBadge = (endAt) => {
    if (!endAt) return { text: "SLA belum ditetapkan", className: "bg-slate-100 text-slate-500" };
    const now = dayjs();
    const deadline = dayjs(endAt);
    const diffDays = deadline.diff(now, "day");

    if (diffDays < 0) return { text: `Overdue ${Math.abs(diffDays)} hari`, className: "bg-rose-100 text-rose-700" };
    if (diffDays <= 2) return { text: `${diffDays} hari lagi`, className: "bg-amber-100 text-amber-700" };
    return { text: `${diffDays} hari lagi`, className: "bg-emerald-100 text-emerald-700" };
};
</script>

<template>
    <Head title="Dashboard Siswa" />

    <div class="flex min-h-screen bg-[#F8FAFC] font-sans text-slate-900">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed lg:sticky top-0 h-screen w-80 bg-white border-r border-slate-200/60 p-8 flex flex-col z-[60] transition-transform duration-500 lg:translate-x-0 shadow-2xl lg:shadow-none">
            <div class="mb-12 flex items-center gap-4">
                <div class="h-12 w-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-200 ring-4 ring-blue-50">
                    <i class="fas fa-rocket text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-800 tracking-tighter uppercase italic leading-none">Aspira<span class="text-blue-600">Chat</span></h1>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Student Portal</p>
                </div>
            </div>

            <nav class="flex-1 space-y-2">
                <p class="px-4 text-[11px] font-black text-slate-400 uppercase tracking-[0.25em] mb-6">Menu Navigasi</p>
                <Link href="/student/dashboard" class="group flex items-center gap-4 px-5 py-4 rounded-2xl font-black text-xs uppercase tracking-widest bg-slate-900 text-white shadow-2xl shadow-slate-200">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shadow-inner"><i class="fas fa-th-large text-base"></i></div>
                    <span>Beranda</span>
                </Link>
                <Link href="/student/input-aspirations" class="group flex items-center gap-4 px-5 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 group-hover:bg-white flex items-center justify-center shadow-sm transition-all"><i class="fas fa-paper-plane text-base"></i></div>
                    <span>Laporanku</span>
                </Link>
                <Link href="/student/global" class="group flex items-center gap-4 px-5 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 group-hover:bg-white flex items-center justify-center shadow-sm transition-all"><i class="fas fa-globe text-base"></i></div>
                    <span>Eksplorasi</span>
                </Link>
                <Link href="/student/profile" class="group flex items-center gap-4 px-5 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 group-hover:bg-white flex items-center justify-center shadow-sm transition-all"><i class="fas fa-user-circle text-base"></i></div>
                    <span>Profilku</span>
                </Link>
            </nav>

            <div class="mt-auto pt-8 border-t border-slate-100">
                <button @click="handleLogout" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-rose-500 hover:bg-rose-50 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center shadow-sm"><i class="fas fa-power-off"></i></div>
                    <span>Keluar Sesi</span>
                </button>
            </div>
        </aside>

        <main class="flex-1 min-w-0 flex flex-col">
            <!-- Header Area -->
            <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 px-6 lg:px-10 py-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center">
                        <i class="fas fa-bars-staggered"></i>
                    </button>
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tighter uppercase italic">Pusat <span class="text-blue-600">Kontrol</span> Siswa</h2>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Selamat datang kembali, {{ student.username }}!</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <NotificationBell />
                    <div class="h-12 w-12 rounded-2xl overflow-hidden ring-4 ring-slate-100 shadow-sm">
                        <div class="w-full h-full bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 font-black text-sm uppercase">
                            {{ student.username.substring(0, 2) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 p-6 lg:p-10 space-y-10">
                <!-- Stat Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="group bg-white p-8 rounded-[2.5rem] border border-slate-200/60 shadow-sm relative overflow-hidden transition-all duration-300 hover:shadow-xl hover:border-blue-100">
                        <div class="relative z-10">
                            <p class="text-blue-600 font-black uppercase text-[11px] tracking-widest mb-2">Total Aspirasi</p>
                            <h3 class="text-5xl font-black text-slate-800 tracking-tighter">{{ total }}</h3>
                        </div>
                        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-blue-50 rounded-full group-hover:scale-110 transition-transform"></div>
                        <i class="fas fa-folder-open absolute right-8 bottom-8 text-4xl text-blue-200/50"></i>
                    </div>
                    <div class="group bg-white p-8 rounded-[2.5rem] border border-slate-200/60 shadow-sm relative overflow-hidden transition-all duration-300 hover:shadow-xl hover:border-amber-100">
                        <div class="relative z-10">
                            <p class="text-amber-600 font-black uppercase text-[11px] tracking-widest mb-2">Sedang Antre</p>
                            <h3 class="text-5xl font-black text-slate-800 tracking-tighter">{{ menunggu }}</h3>
                        </div>
                        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-amber-50 rounded-full group-hover:scale-110 transition-transform"></div>
                        <i class="fas fa-hourglass-half absolute right-8 bottom-8 text-4xl text-amber-200/50"></i>
                    </div>
                    <div class="group bg-white p-8 rounded-[2.5rem] border border-slate-200/60 shadow-sm relative overflow-hidden transition-all duration-300 hover:shadow-xl hover:border-emerald-100">
                        <div class="relative z-10">
                            <p class="text-emerald-600 font-black uppercase text-[11px] tracking-widest mb-2">Berhasil Beres</p>
                            <h3 class="text-5xl font-black text-slate-800 tracking-tighter">{{ diterima }}</h3>
                        </div>
                        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-emerald-50 rounded-full group-hover:scale-110 transition-transform"></div>
                        <i class="fas fa-check-double absolute right-8 bottom-8 text-4xl text-emerald-200/50"></i>
                    </div>
                </div>

                <!-- CTA Banner -->
                <div class="relative rounded-[3rem] overflow-hidden bg-slate-900 p-12 lg:p-20 text-center shadow-2xl shadow-slate-200">
                    <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-blue-600/20 blur-[120px]"></div>
                    <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-indigo-600/10 blur-[100px]"></div>
                    <div class="relative z-10 max-w-2xl mx-auto space-y-8">
                        <h2 class="text-4xl lg:text-5xl font-black text-white italic tracking-tighter uppercase leading-none">
                            Lihat fasilitas yang <span class="text-blue-500">bermasalah?</span>
                        </h2>
                        <p class="text-slate-400 text-lg font-medium leading-relaxed">
                            Jangan biarkan kenyamanan belajarmu terganggu. Kirim laporan sekarang, tim kami siap bergerak!
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link href="/student/input-aspirations/create" 
                                  class="w-full sm:w-auto inline-flex items-center justify-center gap-4 bg-white text-slate-900 px-10 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl hover:bg-blue-600 hover:text-white transition-all active:scale-95">
                                <i class="fas fa-plus-circle"></i> Buat Laporan Baru
                            </Link>
                            <Link href="/student/global" 
                                  class="w-full sm:w-auto inline-flex items-center justify-center gap-4 bg-white/5 text-white border border-white/10 px-10 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-white/10 transition-all">
                                Jelajahi Suara Lain
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <section class="bg-white rounded-[3rem] border border-slate-200/60 p-8 lg:p-12 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-10">
                        <div>
                            <h3 class="text-2xl font-black text-slate-800 uppercase italic tracking-tighter leading-none">Aspirasi <span class="text-blue-600">Terbaru Kamu</span></h3>
                            <p class="text-slate-400 text-sm font-medium mt-2">Update status pengerjaan secara real-time.</p>
                        </div>
                        <Link href="/student/input-aspirations" class="inline-flex items-center gap-3 px-6 py-4 rounded-2xl bg-slate-50 text-slate-900 font-black text-[11px] uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all duration-300">
                            Lihat Riwayat Lengkap <i class="fas fa-arrow-right"></i>
                        </Link>
                    </div>

                    <div v-if="recentAspirations.length" class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                        <div v-for="item in recentAspirations" :key="item.id_input" class="group bg-slate-50/50 border border-slate-100 rounded-[2.5rem] p-8 hover:bg-white hover:shadow-xl hover:border-blue-100 transition-all duration-500">
                            <div class="flex items-start justify-between gap-4 mb-6">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-2.5 py-1 bg-white rounded-lg text-[9px] font-black text-blue-600 uppercase tracking-widest shadow-sm">{{ item.category?.name_category }}</span>
                                        <span class="text-slate-300 text-xs">•</span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ dayjs(item.input_at).format('DD MMM YYYY') }}</span>
                                    </div>
                                    <h4 class="text-xl font-black text-slate-800 uppercase tracking-tighter line-clamp-1">{{ item.location }}</h4>
                                </div>
                                <div :class="[
                                    'px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm',
                                    item.submission_status === 'diterima' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 
                                    item.submission_status === 'ditolak' ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-amber-50 text-amber-600 border border-amber-100'
                                ]">
                                    {{ item.submission_status }}
                                </div>
                            </div>

                            <!-- Progress Section -->
                            <div class="bg-white border border-slate-100 rounded-3xl p-6 mb-6 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-microchip text-blue-600 text-xs"></i>
                                        <span class="text-[10px] font-black text-slate-800 uppercase tracking-widest">Progress Pengerjaan</span>
                                    </div>
                                    <span class="text-xs font-black text-blue-600">{{ item.aspiration?.progress_status || 'Belum Diproses' }}</span>
                                </div>
                                
                                <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden p-0.5 mb-4">
                                    <div class="h-full bg-gradient-to-r from-blue-600 to-indigo-500 rounded-full transition-all duration-1000 ease-out shadow-sm"
                                         :style="{ width: progressWidth(item.aspiration?.progress_status) }"></div>
                                </div>

                                <div class="flex items-center justify-between pt-2">
                                    <span :class="['px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest', slaBadge(item.aspiration?.end_at).className]">
                                        {{ slaBadge(item.aspiration?.end_at).text }}
                                    </span>
                                    <Link :href="`/student/input-aspirations/${item.id_input}`" class="text-[10px] font-black text-blue-600 hover:underline uppercase tracking-widest">Detail Diskusi →</Link>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3">
                                <Link v-if="item.submission_status === 'menunggu'" :href="`/student/input-aspirations/${item.id_input}/edit`" 
                                      class="px-6 py-3 rounded-xl bg-amber-500 text-white font-black text-[10px] uppercase tracking-widest hover:bg-amber-600 transition-all shadow-lg shadow-amber-200">
                                    Edit Laporan
                                </Link>
                                <Link :href="`/student/input-aspirations/${item.id_input}`" 
                                      class="px-6 py-3 rounded-xl bg-slate-900 text-white font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg shadow-slate-200">
                                    Buka Detail
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-20 text-center bg-slate-50 rounded-[3rem] border-2 border-dashed border-slate-200">
                        <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-300 shadow-sm">
                            <i class="fas fa-wind text-3xl"></i>
                        </div>
                        <h4 class="text-lg font-black text-slate-800 uppercase tracking-tighter">Wah, belum ada data nih!</h4>
                        <p class="text-slate-400 text-sm mt-2 mb-8">Mulailah dengan membuat aspirasi pertamamu.</p>
                        <Link href="/student/input-aspirations/create" class="px-8 py-4 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-blue-200">
                            Gaskeun Lapor!
                        </Link>
                    </div>
                </section>
            </div>
        </main>
    </div>
</template>

<style scoped>
/* Smooth Fade Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
main { animation: fadeIn 0.6s ease-out forwards; }
</style>
