<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from "vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import Swal from "sweetalert2";
import NotificationBell from "@/Components/NotificationBell.vue";

dayjs.extend(relativeTime);

const props = defineProps({
    student: Object,
    aspirations: Array,
});

const showCommentModal = ref(false);
const activeAspiration = ref(null);
const commentListRef = ref(null);
const currentTime = ref(dayjs());
const sidebarOpen = ref(false);
const searchQuery = ref("");

let timerInterval;
onMounted(() => {
    timerInterval = setInterval(() => {
        currentTime.value = dayjs();
    }, 60000);
});

onUnmounted(() => {
    clearInterval(timerInterval);
});

const commentForm = useForm({
    body: "",
});

// --- Filtered Aspirations ---
const filteredAspirations = computed(() => {
    if (!searchQuery.value) return props.aspirations;
    const query = searchQuery.value.toLowerCase();
    return props.aspirations.filter(a => 
        a.description?.toLowerCase().includes(query) || 
        a.location?.toLowerCase().includes(query) ||
        a.student?.username?.toLowerCase().includes(query)
    );
});


// --- SweetAlert Toast ---
const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
});

// --- HELPERS ---
const getSLALabel = (aspiration) => {
    if (!aspiration.deadline) return null;
    const diff = dayjs(aspiration.deadline).diff(currentTime.value, "hour");
    if (diff < 0) return { text: "OVERDUE", class: "bg-rose-500 text-white" };
    if (diff <= 24) return { text: "EMERGENCY", class: "bg-amber-500 text-white" };
    if (diff <= 72) return { text: "URGENT", class: "bg-orange-400 text-white" };
    return { text: "ON TRACK", class: "bg-blue-100 text-blue-600" };
};

const getCountdown = (deadline) => {
    if (!deadline) return null;
    const target = dayjs(deadline);
    const diffDays = target.diff(currentTime.value, "day");
    if (diffDays < 1) return target.from(currentTime.value, true);
    return `${diffDays} hari lagi`;
};

const openComment = (aspiration) => {
    activeAspiration.value = aspiration;
    showCommentModal.value = true;
    nextTick(scrollToBottom);
};

const scrollToBottom = () => {
    if (commentListRef.value) {
        commentListRef.value.scrollTop = commentListRef.value.scrollHeight;
    }
};

const submitComment = () => {
    if (!commentForm.body.trim()) return;
    commentForm.post(`/student/aspirations/${activeAspiration.value.id_aspiration}/comments`, {
        preserveScroll: true,
        onSuccess: () => {
            const updated = props.aspirations.find(a => a.id_aspiration === activeAspiration.value.id_aspiration);
            if (updated) activeAspiration.value = updated;
            commentForm.reset();
            nextTick(scrollToBottom);
        }
    });
};

const handleVote = (id) => {
    router.post(`/student/aspirations/${id}/vote`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            Toast.fire({ icon: "success", title: "Dukungan diperbarui" });
        }
    });
};

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
</script>

<template>
    <Head title="Jelajah Aspirasi" />

    <div class="flex min-h-screen bg-[#F8FAFC] font-sans text-slate-900 selection:bg-blue-100 selection:text-blue-600">
        <!-- Modern Sidebar -->
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
                <Link href="/student/dashboard" class="group flex items-center gap-4 px-5 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 group-hover:bg-white flex items-center justify-center shadow-sm transition-all"><i class="fas fa-th-large text-base"></i></div>
                    <span>Beranda</span>
                </Link>
                <Link href="/student/input-aspirations" class="group flex items-center gap-4 px-5 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 group-hover:bg-white flex items-center justify-center shadow-sm transition-all"><i class="fas fa-paper-plane text-base"></i></div>
                    <span>Laporanku</span>
                </Link>
                <Link href="/student/global" class="group flex items-center gap-4 px-5 py-4 rounded-2xl font-black text-xs uppercase tracking-widest bg-slate-900 text-white shadow-2xl shadow-slate-200">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shadow-inner"><i class="fas fa-globe text-base text-blue-400"></i></div>
                    <span>Eksplorasi</span>
                </Link>
                <Link href="/student/profile" class="group flex items-center gap-4 px-5 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 group-hover:bg-white flex items-center justify-center shadow-sm transition-all"><i class="fas fa-user text-base"></i></div>
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
            <!-- Top Header -->
            <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 px-6 lg:px-10 py-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center">
                        <i class="fas fa-bars-staggered"></i>
                    </button>
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tighter uppercase italic">Eksplorasi <span class="text-blue-600">Suara</span></h2>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hidden sm:block">Temukan dan dukung aspirasi positif teman-temanmu</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex relative group">
                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                        <input v-model="searchQuery" type="text" placeholder="Cari aspirasi atau lokasi..." 
                               class="bg-slate-100 border-none rounded-2xl pl-12 pr-6 py-3.5 text-xs font-bold w-64 focus:ring-4 focus:ring-blue-100 focus:bg-white transition-all">
                    </div>
                    <NotificationBell />
                    <div class="h-12 w-12 rounded-2xl overflow-hidden ring-2 ring-slate-100 p-0.5 shadow-sm">
                        <div class="w-full h-full bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 font-black text-sm uppercase">
                            {{ student.username.substring(0, 2) }}
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 p-6 lg:p-10 flex flex-col lg:flex-row gap-8">
                <!-- Main Feed -->
                <div class="flex-1 max-w-4xl space-y-8">

                    <div v-if="filteredAspirations.length === 0" class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-[3rem] border-2 border-dashed border-slate-200">
                        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 text-slate-300">
                            <i class="fas fa-search-plus text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter">Aspirasi Tidak Ditemukan</h3>
                        <p class="text-slate-500 text-sm mt-2 max-w-xs">Coba cari dengan kata kunci lain seperti nama gedung atau kategori.</p>
                        <button @click="searchQuery = ''" class="mt-6 px-8 py-3 bg-slate-900 text-white rounded-2xl text-xs font-black uppercase tracking-widest">Reset Pencarian</button>
                    </div>

                    <!-- Post Cards -->
                    <div v-for="aspiration in filteredAspirations" :key="aspiration.id_aspiration" 
                         class="group bg-white rounded-[2.5rem] border border-slate-200/60 shadow-sm hover:shadow-2xl hover:shadow-blue-500/5 transition-all duration-500 overflow-hidden">
                        
                        <!-- Card Header -->
                        <div class="p-8 pb-4 flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <Link :href="`/student/students/${aspiration.student?.id_student}`" class="relative">
                                    <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-600 font-black text-lg border-2 border-white ring-4 ring-slate-50 shadow-lg">
                                        {{ aspiration.student?.username?.substring(0, 2).toUpperCase() }}
                                    </div>
                                    <div v-if="aspiration.student?.special_badges?.length" class="absolute -right-1 -bottom-1 w-6 h-6 bg-blue-600 rounded-lg flex items-center justify-center text-white ring-2 ring-white shadow-sm">
                                        <i class="fas fa-check text-[10px]"></i>
                                    </div>
                                </Link>
                                <div>
                                    <Link :href="`/student/students/${aspiration.student?.id_student}`" class="block font-black text-slate-800 uppercase tracking-tighter hover:text-blue-600 transition-colors">
                                        {{ aspiration.student?.username }}
                                    </Link>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded-lg uppercase tracking-widest">{{ aspiration.category?.name_category || 'General' }}</span>
                                        <span class="text-slate-300 text-xs">•</span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ aspiration.location }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="aspiration.deadline" class="hidden sm:flex flex-col items-end">
                                <span :class="['px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-[0.15em] shadow-sm mb-1.5', getSLALabel(aspiration)?.class]">
                                    {{ getSLALabel(aspiration)?.text }}
                                </span>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ getCountdown(aspiration.deadline) }}</p>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="px-8 py-4">
                            <p class="text-slate-600 text-base leading-relaxed font-medium line-clamp-4">{{ aspiration.description }}</p>
                            
                            <!-- Status Badge -->
                            <div class="mt-6 flex items-center gap-3">
                                <div :class="[
                                    'px-4 py-2 rounded-2xl text-[9px] font-black uppercase tracking-[0.2em] shadow-sm',
                                    aspiration.progress_status === 'Selesai' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-amber-50 text-amber-600 border border-amber-100'
                                ]">
                                    {{ aspiration.progress_status }}
                                </div>
                                <span v-if="aspiration.progress_status === 'Selesai'" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">🎉 Masalah Teratasi</span>
                            </div>

                            <!-- Post Image -->
                            <div v-if="aspiration.input?.image" class="mt-8 rounded-[2rem] overflow-hidden border border-slate-100 group-hover:shadow-lg transition-all">
                                <img :src="`/storage/${aspiration.input.image}`" class="w-full h-auto object-cover max-h-[500px] hover:scale-105 transition-transform duration-700" alt="Evidence">
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="p-8 pt-4 flex items-center justify-between border-t border-slate-50 mt-4 bg-slate-50/50">
                            <div class="flex items-center gap-3">
                                <button @click="handleVote(aspiration.id_aspiration)" 
                                        :class="[
                                            'flex items-center gap-2.5 px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest transition-all duration-300 active:scale-95',
                                            aspiration.user_has_voted ? 'bg-blue-600 text-white shadow-xl shadow-blue-500/30' : 'bg-white text-slate-400 hover:text-blue-600 hover:shadow-lg'
                                        ]">
                                    <i :class="aspiration.user_has_voted ? 'fas fa-thumbs-up' : 'far fa-thumbs-up'" class="text-base"></i>
                                    <span>{{ aspiration.votes_count }}</span>
                                </button>

                                <button @click="openComment(aspiration)" 
                                        class="flex items-center gap-2.5 px-6 py-3.5 rounded-2xl bg-white text-slate-400 font-black text-xs uppercase tracking-widest hover:text-slate-900 hover:shadow-lg transition-all duration-300 active:scale-95">
                                    <i class="far fa-comment-dots text-base"></i>
                                    <span>{{ aspiration.comments?.length || 0 }}</span>
                                </button>
                            </div>

                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ dayjs(aspiration.created_at).fromNow() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar (Extra Info) -->
                <div class="hidden xl:block w-80 space-y-8">
                    <div class="sticky top-[120px]">
                        <div class="p-8 bg-gradient-to-br from-slate-900 to-slate-800 rounded-[2.5rem] relative overflow-hidden shadow-2xl shadow-slate-200">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/20 rounded-full blur-2xl"></div>
                            <div class="relative z-10">
                                <h3 class="text-white font-black text-lg uppercase tracking-tighter italic leading-tight">Sampaikan <br> Aspirasimu!</h3>
                                <p class="text-slate-400 text-xs mt-3 mb-8 leading-relaxed">Punya keluhan soal fasilitas? Jangan dipendam sendiri cuy, suarakan sekarang!</p>
                                <Link href="/student/input-aspirations/create" class="block text-center py-4 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-blue-500/30 hover:scale-105 active:scale-95 transition-all">
                                    BUAT LAPORAN
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modern Comment Modal -->
        <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showCommentModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-6 bg-slate-900/60 backdrop-blur-md" @click.self="showCommentModal = false">
                <transition enter-active-class="transition duration-500 ease-out" enter-from-class="opacity-0 translate-y-full sm:translate-y-12 sm:scale-95" enter-to-class="opacity-100 translate-y-0 sm:scale-100" leave-active-class="transition duration-300 ease-in" leave-from-class="opacity-100 translate-y-0 sm:scale-100" leave-to-class="opacity-0 translate-y-full sm:translate-y-12 sm:scale-95">
                    <div class="bg-white w-full max-w-2xl h-[85vh] sm:h-[80vh] sm:rounded-[3rem] rounded-t-[3rem] flex flex-col shadow-2xl overflow-hidden relative">
                        <!-- Modal Header -->
                        <div class="bg-slate-900 px-8 py-6 flex items-center justify-between relative overflow-hidden">
                            <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-600/20 rounded-full blur-3xl"></div>
                            <div class="relative z-10 flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                                    <i class="fas fa-comments text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-white uppercase tracking-widest italic">Diskusi Forum</h3>
                                    <p class="text-blue-400 text-[10px] font-bold uppercase tracking-widest mt-0.5">{{ activeAspiration?.comments?.length || 0 }} Komentar Aktif</p>
                                </div>
                            </div>
                            <button @click="showCommentModal = false" class="relative z-10 w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Comment List -->
                        <div ref="commentListRef" class="flex-1 overflow-y-auto p-8 space-y-6 bg-slate-50/50 no-scrollbar">
                            <div v-for="comment in activeAspiration?.comments" :key="comment.id_comment" 
                                 class="flex gap-4" :class="Number(comment.student_id) === Number(student.id_student) ? 'flex-row-reverse' : 'flex-row'">
                                
                                <div class="h-10 w-10 shrink-0 rounded-xl flex items-center justify-center text-[10px] font-black shadow-sm ring-2 ring-white"
                                     :class="Number(comment.student_id) === Number(student.id_student) ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-500'">
                                    {{ comment.student?.username?.substring(0, 2).toUpperCase() }}
                                </div>

                                <div class="max-w-[75%] space-y-1" :class="Number(comment.student_id) === Number(student.id_student) ? 'items-end flex flex-col' : 'items-start flex flex-col'">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ comment.student?.username }}</span>
                                    </div>
                                    <div class="px-5 py-4 rounded-[1.5rem] text-sm font-medium shadow-sm leading-relaxed"
                                         :class="Number(comment.student_id) === Number(student.id_student) 
                                            ? 'bg-blue-600 text-white rounded-tr-none shadow-blue-500/10' 
                                            : 'bg-white text-slate-700 border border-slate-100 rounded-tl-none'">
                                        {{ comment.body }}
                                    </div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest pt-1">{{ dayjs(comment.created_at).fromNow() }}</span>
                                </div>
                            </div>

                            <div v-if="!activeAspiration?.comments?.length" class="h-full flex flex-col items-center justify-center text-center opacity-40 py-20">
                                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4"><i class="fas fa-comment-slash text-3xl"></i></div>
                                <p class="text-xs font-black uppercase tracking-widest">Belum ada diskusi</p>
                            </div>
                        </div>

                        <!-- Input area -->
                        <div class="p-6 bg-white border-t border-slate-100">
                            <form @submit.prevent="submitComment" class="flex items-center gap-3 bg-slate-100 rounded-[2rem] p-1.5 focus-within:ring-4 focus-within:ring-blue-100 transition-all">
                                <textarea v-model="commentForm.body" rows="1" @keydown.enter.exact.prevent="submitComment" 
                                          class="flex-1 bg-transparent border-none focus:ring-0 px-6 py-3 text-sm font-medium text-slate-700 resize-none no-scrollbar" 
                                          placeholder="Tulis pendapatmu..."></textarea>
                                <button type="submit" :disabled="!commentForm.body.trim() || commentForm.processing" 
                                        class="h-12 w-12 bg-blue-600 text-white rounded-full flex items-center justify-center shadow-lg shadow-blue-500/30 hover:scale-105 active:scale-95 disabled:bg-slate-300 disabled:shadow-none transition-all">
                                    <i class="fas fa-paper-plane text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.animate-feed {
    animation: slideUp 0.5s ease-out forwards;
}
</style>