<script setup>
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref, onMounted, watch, nextTick } from "vue";
import Swal from "sweetalert2";
import NotificationBell from "@/Components/NotificationBell.vue";

const props = defineProps({
    selectedAspiration: Object,
    aspirations: Array,
    categories: Array,
    locations: Array,
});

const sidebarOpen = ref(false);
const chatContainer = ref(null);
const commentInput = ref(null);
const isRejectingMode = ref(false);

const ratingForm = useForm({
    rating: props.selectedAspiration?.rating || 5,
    feedback: props.selectedAspiration?.feedback || "",
});

const scrollToBottom = (smooth = true) => {
    nextTick(() => {
        if (chatContainer.value) {
            chatContainer.value.scrollTo({
                top: chatContainer.value.scrollHeight,
                behavior: smooth ? "smooth" : "auto",
            });
        }
    });
};

onMounted(() => {
    scrollToBottom(false);
});

const normalizedSubmissionStatus = computed(() =>
    String(props.selectedAspiration?.submission_status || "")
        .trim()
        .toLowerCase(),
);

const normalizedProgressStatus = computed(() =>
    String(props.selectedAspiration?.aspiration?.progress_status || "")
        .trim()
        .toLowerCase()
);

const canSubmitRating = computed(() => {
    return (
        props.selectedAspiration &&
        normalizedSubmissionStatus.value === "diterima" &&
        normalizedProgressStatus.value === "selesai" &&
        !props.selectedAspiration.rating
    );
});



const ratingOptions = [1, 2, 3, 4, 5];

const selectedAspirationMeta = computed(() => {
    if (!props.selectedAspiration) return [];
    const asp = props.selectedAspiration;

    return [
        { label: 'Judul', value: asp.title || '-' },
        { label: 'Kategori', value: asp.category?.category_name || '-' },
        { label: 'Lokasi', value: asp.location || '-' },
        { label: 'Mode', value: asp.submission_mode || 'template' },
        { label: 'Status', value: asp.submission_status || '-' },
        { label: 'Dikirim', value: formatDateTime(asp.input_at || asp.created_at) },
    ];
});

const sortedFeedbacks = computed(() => {
    if (!props.selectedAspiration?.aspiration?.feedbacks) return [];
    return [...props.selectedAspiration.aspiration.feedbacks].sort(
        (a, b) => new Date(a.feedback_at) - new Date(b.feedback_at),
    );
});

const chatHistory = computed(() => {
    const h = [];
    const asp = props.selectedAspiration;
    if (!asp) return [];
    
    // 1. Initial Student Message
    h.push({
        id: 'user-init',
        sender: 'user',
        text: asp.description,
        type: 'text',
        image: asp.image,
        date: asp.created_at
    });

    // 2. Admin Approval Message
    if (asp.submission_status !== 'menunggu') {
        h.push({
            id: 'admin-action',
            sender: 'bot',
            isStaff: true,
            text: asp.submission_status === 'diterima' 
                ? (asp.admin_message || 'Aspirasi Anda telah disetujui dan akan segera kami proses.')
                : (asp.admin_message || 'Mohon maaf, aspirasi Anda ditolak.'),
            status: asp.submission_status,
            date: asp.aspiration?.validated_at || asp.updated_at
        });
    }

    // 3. Admin Feedbacks
    if (asp.aspiration?.feedbacks) {
        asp.aspiration.feedbacks.forEach((f, idx) => {
            h.push({
                id: `feedback-${f.id_feedback || idx}`,
                sender: 'bot',
                isStaff: true,
                text: f.message,
                date: f.created_at || f.feedback_at // Utamakan created_at untuk presisi detik
            });
        });
    }

    // 4. Student Comments
    if (asp.aspiration?.comments) {
        asp.aspiration.comments.forEach((c, idx) => {
            h.push({
                id: `comment-${c.id || idx}`,
                sender: 'user',
                text: c.body,
                date: c.created_at
            });
        });
    }

    // 5. Evidence Photo
    if (asp.aspiration?.progress_evidence_image) {
        h.push({
            id: 'progress-evidence',
            sender: 'bot',
            isStaff: true,
            text: 'Tim kami telah mengunggah bukti pengerjaan terbaru.',
            image: asp.aspiration.progress_evidence_image,
            type: 'file',
            date: asp.aspiration.updated_at
        });
    }

    // Sort EVERYTHING by date
    const sorted = h.sort((a, b) => new Date(a.date) - new Date(b.date));



    return sorted;
});

const formatDateTime = (value) => {
    if (!value) return '-';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '-';
    return date.toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const submitRating = () => {
    ratingForm.post(`/student/input-aspirations/${props.selectedAspiration.id_input}/rate`, {
        preserveScroll: true,
    });
};



const commentForm = useForm({
    body: ""
});

const submitComment = () => {
    if (!commentForm.body.trim()) return;
    
    // Ensure we have the correct aspiration ID
    const aspId = props.selectedAspiration?.aspiration?.id_aspiration;
    if (!aspId) return;

    commentForm.post(`/student/aspirations/${aspId}/comments`, {
        preserveScroll: true,
        onSuccess: () => {
            commentForm.reset();
            isRejectingMode.value = false;
            scrollToBottom();
        }
    });
};

const statusTheme = (status) => {
    const s = String(status || "").toLowerCase();
    if (s === "diterima") return "bg-emerald-50 text-emerald-600 border-emerald-100";
    if (s === "ditolak") return "bg-rose-50 text-rose-600 border-rose-100";
    return "bg-amber-50 text-amber-600 border-amber-100";
};

const handleLogout = () => {
    Swal.fire({
        title: "KELUAR SISTEM?",
        text: "Sesi Anda akan segera diakhiri.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5",
        cancelButtonColor: "#f43f5e",
        confirmButtonText: "YA, KELUAR",
        cancelButtonText: "BATAL",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post("/student/logout");
        }
    });
};
</script>

<template>
    <Head :title="'Aspirasi #' + selectedAspiration.id_input" />

    <div class="flex min-h-screen bg-[#fafafa] font-sans selection:bg-indigo-100 overflow-hidden">
        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="w-80 bg-white border-r border-slate-100 flex flex-col p-6 fixed lg:sticky top-0 h-screen z-[70] lg:translate-x-0 transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]"
        >
            <div class="mb-10 px-2 flex items-center gap-4">
                <div class="h-12 w-12 bg-gradient-to-tr from-indigo-600 to-violet-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-indigo-200 rotate-3">
                    <i class="fas fa-robot text-xl"></i>
                </div>
                <div>
                    <h2 class="font-black text-slate-800 tracking-tighter text-xl italic uppercase leading-none">
                        <i class="fas fa-robot text-blue-600 me-1"></i>Aspira<span class="text-blue-600">Chat</span>
                    </h2>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] mt-1">Premium Assistant</p>
                </div>
            </div>

            <Link href="/student/input-aspirations" class="mb-8 w-full group flex items-center justify-between bg-slate-900 text-white p-5 rounded-[2rem] hover:bg-indigo-600 transition-all duration-500 shadow-xl shadow-slate-900/10 hover:shadow-indigo-500/20 active:scale-[0.98]">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                        <i class="fas fa-plus text-[10px]"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest">New Chat</span>
                </div>
                <i class="fas fa-arrow-right text-[10px] opacity-30 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all"></i>
            </Link>

            <div class="flex-1 flex flex-col min-h-0">
                <p class="px-2 mb-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Riwayat Percakapan</p>
                <div class="space-y-2 overflow-y-auto pr-1 no-scrollbar flex-1 pb-10">
                    <Link
                        v-for="item in aspirations"
                        :key="item.id_input"
                        :href="'/student/input-aspirations/' + item.id_input"
                        class="w-full group text-left rounded-3xl border p-4 transition-all duration-500 block relative overflow-hidden"
                        :class="Number(selectedAspiration.id_input) === Number(item.id_input)
                            ? 'border-indigo-100 bg-indigo-50/50 shadow-sm'
                            : 'border-transparent hover:bg-slate-50'"
                    >
                        <div v-if="Number(selectedAspiration.id_input) === Number(item.id_input)" class="absolute left-0 top-0 w-1.5 h-full bg-indigo-600 rounded-r-full"></div>
                        
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <span :class="['text-[8px] font-black uppercase rounded-lg px-2 py-0.5 border', statusTheme(item.submission_status)]">
                                {{ item.submission_status }}
                            </span>
                            <span class="text-[9px] font-bold text-slate-400">{{ formatDateTime(item.created_at).split(',')[0] }}</span>
                        </div>
                        <p class="text-[13px] font-bold text-slate-700 truncate group-hover:text-indigo-600 transition-colors">{{ item.title || 'Tanpa Judul' }}</p>
                        <p class="text-[10px] font-medium text-slate-400 truncate mt-1 flex items-center gap-1.5">
                            <i class="fas fa-map-marker-alt opacity-40"></i> {{ item.location }}
                        </p>
                    </Link>
                </div>
            </div>

            <div class="mt-auto pt-6 border-t border-slate-100 grid grid-cols-2 gap-3">
                <Link href="/student/dashboard" class="flex flex-col items-center justify-center gap-2 p-4 rounded-3xl bg-slate-50 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all group">
                    <i class="fas fa-th-large text-sm group-hover:scale-110 transition-transform"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Dash</span>
                </Link>
                <button @click="handleLogout" class="flex flex-col items-center justify-center gap-2 p-4 rounded-3xl bg-rose-50 text-rose-500 hover:bg-rose-100 transition-all group">
                    <i class="fas fa-power-off text-sm group-hover:scale-110 transition-transform"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Exit</span>
                </button>
            </div>
        </aside>

        <main class="flex-1 flex flex-col relative h-screen">
            <!-- Top Bar -->
            <header class="h-24 hidden lg:flex items-center justify-between px-10 bg-white/80 backdrop-blur-xl border-b border-slate-100/50 z-40 sticky top-0">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                        <i class="fas fa-robot text-xs"></i>
                    </div>
                    <div>
                        <h1 class="text-sm font-black text-slate-800 tracking-widest uppercase">Detail Aspirasi</h1>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Workspace • #{{ selectedAspiration.id_input }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <NotificationBell />
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto no-scrollbar pt-20 lg:pt-0 pb-32" ref="chatContainer">
                <div class="p-8 lg:p-12 space-y-12 animate-in fade-in slide-in-from-bottom-6 duration-700">
                    <!-- Detail Header -->
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-slate-100">
                        <div class="space-y-4 max-w-2xl">
                            <div class="flex flex-wrap items-center gap-3">
                                <span :class="['text-[10px] font-black uppercase rounded-xl px-4 py-1.5 border tracking-widest shadow-sm', statusTheme(selectedAspiration.submission_status)]">
                                    {{ selectedAspiration.submission_status }}
                                </span>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-100 px-4 py-1.5 rounded-xl">
                                    ID #{{ selectedAspiration.id_input }}
                                </span>
                            </div>
                            <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase italic leading-[0.9]">{{ selectedAspiration.title }}</h2>
                            <p class="text-slate-500 font-medium leading-relaxed">{{ selectedAspiration.description }}</p>
                        </div>
                        <div class="flex-shrink-0 text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Terakhir Diperbarui</p>
                            <p class="text-sm font-bold text-slate-700">{{ formatDateTime(selectedAspiration.updated_at) }}</p>
                        </div>
                    </div>

                    <!-- Meta Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div v-for="meta in selectedAspirationMeta" :key="meta.label" class="bg-white rounded-[1.5rem] border border-slate-100 p-5 shadow-sm hover:shadow-md transition-shadow">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">{{ meta.label }}</p>
                            <p class="text-sm font-black text-slate-800 break-words">{{ meta.value }}</p>
                        </div>
                    </div>

                    <!-- Timeline / Messages Section -->
                    <div class="space-y-10 max-w-4xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                                <i class="fas fa-comment-dots text-xs"></i>
                            </div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight uppercase italic">Pesan & Progress</h3>
                        </div>

                        <div class="space-y-8 pl-4 border-l-2 border-slate-50">
                            <div v-for="msg in chatHistory" :key="msg.id" 
                                :class="['flex w-full', msg.sender === 'user' ? 'justify-end' : 'justify-start']">
                                
                                <div :class="['flex max-w-[90%] gap-4', msg.sender === 'user' ? 'flex-row-reverse' : 'flex-row']">
                                    <div v-if="msg.sender === 'bot'" :class="['flex-shrink-0 w-8 h-8 rounded-xl flex items-center justify-center shadow-sm self-end mb-1', msg.isStaff ? 'bg-indigo-600 text-white' : 'bg-indigo-50 border border-indigo-100 text-indigo-500']">
                                        <i :class="msg.isStaff ? 'fas fa-user-shield text-[10px]' : 'fas fa-robot text-[10px]'"></i>
                                    </div>

                                    <div class="space-y-1">
                                        <div v-if="msg.isStaff" class="px-2 mb-1">
                                            <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest">Administrator</span>
                                        </div>
                                        <div :class="[
                                            'text-[14px] leading-relaxed font-medium px-5 py-3 shadow-sm min-w-[3rem]',
                                            msg.sender === 'user' 
                                                ? 'bg-slate-900 text-white rounded-[1.5rem] rounded-tr-none' 
                                                : 'bg-white border border-slate-100 text-slate-800 rounded-[1.5rem] rounded-bl-none',
                                            msg.isStaff ? 'border-indigo-100' : ''
                                        ]">
                                            {{ msg.text }}
                                            
                                            <div v-if="msg.image" class="mt-4 rounded-xl overflow-hidden border border-slate-100 shadow-lg bg-slate-50">
                                                <img :src="`/storage/${msg.image}`" class="w-full h-auto object-cover max-h-80">
                                            </div>

                                            <div v-if="msg.type === 'interactive'" class="mt-4 flex flex-col sm:flex-row gap-2">
                                                <button v-for="action in msg.actions" :key="action.label" @click="action.handler" :class="['flex-1 py-3 rounded-xl text-white text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-indigo-500/10 active:scale-[0.98]', action.class]">
                                                    {{ action.label }}
                                                </button>
                                            </div>
                                        </div>
                                        <div v-if="msg.date" class="px-2 mt-1 text-[9px] text-slate-400 font-bold uppercase tracking-tighter">
                                            {{ formatDateTime(msg.date) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Hub -->
                    <div v-if="canSubmitRating || selectedAspiration.rating" class="animate-in fade-in slide-in-from-bottom-6 duration-1000">
                        <div class="bg-amber-50/50 border border-amber-100 rounded-[2.5rem] p-8 space-y-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600 shadow-sm">
                                    <i class="fas fa-star text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-0.5">Evaluasi Layanan</p>
                                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Rating & Feedback</h3>
                                </div>
                            </div>

                            <template v-if="canSubmitRating">
                                <div class="flex gap-2">
                                    <button v-for="star in ratingOptions" :key="star" @click="ratingForm.rating = star" class="w-12 h-12 rounded-2xl border-2 transition-all flex items-center justify-center" :class="ratingForm.rating >= star ? 'bg-white border-amber-400 text-amber-600' : 'bg-white border-slate-100 text-slate-200'">
                                        <i class="fa-star" :class="ratingForm.rating >= star ? 'fas' : 'far'"></i>
                                    </button>
                                </div>
                                <textarea v-model="ratingForm.feedback" rows="3" placeholder="Apa pendapat Anda tentang penyelesaian ini?" class="w-full bg-white rounded-3xl border border-slate-100 p-6 text-sm font-medium focus:border-amber-400 outline-none transition-all resize-none shadow-sm"></textarea>
                                <button @click="submitRating" :disabled="ratingForm.processing" class="px-10 py-4 rounded-2xl bg-slate-900 text-white text-[11px] font-black uppercase tracking-widest hover:bg-amber-600 transition-all shadow-xl shadow-slate-900/10">
                                    {{ ratingForm.processing ? 'Saving...' : 'Kirim Rating' }}
                                </button>
                            </template>

                            <template v-else>
                                <div class="flex gap-1.5 mb-4">
                                    <i v-for="i in 5" :key="i" class="fa-star text-lg" :class="i <= selectedAspiration.rating ? 'fas text-amber-400' : 'far text-slate-200'"></i>
                                </div>
                                <p class="text-slate-700 font-medium bg-white/50 p-6 rounded-3xl border border-amber-100/50 italic">"{{ selectedAspiration.feedback || 'Tidak ada komentar.' }}"</p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comment Input for Observation/Active status -->
            <div v-if="['dalam proses', 'belum dimulai'].includes(normalizedProgressStatus)" class="fixed bottom-10 left-1/2 lg:left-[calc(50%+160px)] -translate-x-1/2 w-[calc(100%-48px)] max-w-2xl z-[80] animate-in slide-in-from-bottom-2">
                <div class="bg-white rounded-[2.5rem] border-2 p-2 shadow-[0_20px_50px_rgba(0,0,0,0.1)] flex items-center gap-2 transition-all duration-300" :class="isRejectingMode ? 'border-rose-400 shadow-rose-500/10' : 'border-slate-200'">
                    <textarea 
                        ref="commentInput"
                        v-model="commentForm.body" 
                        rows="1" 
                        @input="(e) => { e.target.style.height = 'auto'; e.target.style.height = e.target.scrollHeight + 'px' }" 
                        @keydown.enter.exact.prevent="submitComment" 
                        :placeholder="isRejectingMode ? 'Tulis alasan penolakan di sini...' : 'Ketik pesan balasan...'" 
                        class="flex-1 bg-transparent border-none focus:ring-0 px-6 py-4 text-[15px] font-medium text-slate-700 resize-none max-h-40 overflow-y-auto no-scrollbar"
                    ></textarea>
                    <button @click="submitComment" :disabled="!commentForm.body.trim() || commentForm.processing" class="w-12 h-12 rounded-full text-white flex items-center justify-center hover:scale-105 active:scale-95 transition-all shadow-xl disabled:opacity-20" :class="isRejectingMode ? 'bg-rose-500' : 'bg-slate-900'">
                        <i class="fas fa-paper-plane text-xs"></i>
                    </button>
                </div>
            </div>
        </main>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');

body {
    font-family: 'Outfit', sans-serif;
    background-color: #fafafa;
}

.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
@keyframes slide-in-top { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@keyframes slide-in-bottom { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@keyframes slide-in-bottom-2 { from { transform: translateY(10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@keyframes zoom-in { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }

.animate-in { animation-duration: 0.4s; animation-fill-mode: forwards; animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1); }

.fade-in { animation-name: fade-in; }
.zoom-in { animation-name: zoom-in; }
.slide-in-from-top-4 { animation-name: slide-in-top; }
.slide-in-from-bottom-6 { animation-name: slide-in-bottom; }
.slide-in-from-bottom-2 { animation-name: slide-in-bottom-2; }

input, textarea, select { outline: none !important; }

::selection { background: #e0e7ff; color: #4338ca; }

::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
