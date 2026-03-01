<script setup>
import { ref, onMounted, onUnmounted } from "vue";
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
const currentTime = ref(dayjs());
const sidebarOpen = ref(false);

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

// --- SweetAlert Toast Config ---
const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
});

// --- SLA HELPER ---
const getSLALabel = (aspiration) => {
    if (!aspiration.deadline) return null;
    const diff = dayjs(aspiration.deadline).diff(currentTime.value, "hour");
    if (diff < 0)
        return {
            text: "OVERDUE",
            class: "bg-rose-500 text-white animate-pulse",
        };
    if (diff <= 24)
        return { text: "EMERGENCY", class: "bg-amber-500 text-white" };
    if (diff <= 72)
        return { text: "URGENT", class: "bg-orange-400 text-white" };
    return { text: "ON TRACK", class: "bg-blue-100 text-blue-600" };
};

const getCountdown = (deadline) => {
    if (!deadline) return null;
    const target = dayjs(deadline);
    const diffDays = target.diff(currentTime.value, "day");
    if (diffDays < 1) return target.from(currentTime.value, true);
    return `${diffDays} hari`;
};

// --- FUNCTIONS ---
const openComment = (aspiration) => {
    activeAspiration.value = aspiration;
    showCommentModal.value = true;
};

const submitComment = () => {
    if (!commentForm.body.trim()) return;
    commentForm.post(
        `/student/aspirations/${activeAspiration.value.id_aspiration}/comments`,
        {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    icon: "success",
                    title: "KOMENTAR TERKIRIM!",
                    text: "Suaramu sudah masuk ke forum diskusi.",
                    borderRadius: "2.5rem",
                    confirmButtonColor: "#0f172a",
                });
                const updated = props.aspirations.find(
                    (a) =>
                        a.id_aspiration ===
                        activeAspiration.value.id_aspiration,
                );
                if (updated) activeAspiration.value = updated;
                commentForm.reset();
            },
        },
    );
};

const handleVote = (id) => {
    router.post(
        `/student/aspirations/${id}/vote`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                Toast.fire({
                    icon: "success",
                    title: "Dukungan berhasil diperbarui",
                });
                const updated = props.aspirations.find(
                    (a) => a.id_aspiration === id,
                );
                if (updated && activeAspiration.value?.id_aspiration === id) {
                    activeAspiration.value = updated;
                }
            },
        },
    );
};

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
    <Head title="Jelajah Aspirasi" />

    <div class="flex min-h-screen bg-slate-50 font-sans text-slate-900">
        <!-- Mobile Header -->
        <div
            class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-white border-b border-slate-100 px-4 py-3 flex items-center justify-between shadow-sm"
        >
            <div class="flex items-center gap-2">
                <div
                    class="h-8 w-8 bg-blue-600 rounded-lg flex items-center justify-center text-white"
                >
                    <i class="fas fa-rocket text-xs"></i>
                </div>
                <span
                    class="font-black text-sm italic uppercase tracking-tighter"
                    >Command<span class="text-blue-600">Center</span></span
                >
            </div>
            <button
                @click="sidebarOpen = !sidebarOpen"
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600"
            >
                <i :class="sidebarOpen ? 'fas fa-times' : 'fas fa-bars'"></i>
            </button>
        </div>

        <!-- Overlay -->
        <div
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="lg:hidden fixed inset-0 bg-black/40 z-40 backdrop-blur-sm"
        ></div>

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
                    class="flex items-center gap-4 px-4 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-slate-400 hover:bg-slate-50 hover:text-blue-600 transition-all"
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
                    class="flex items-center gap-4 px-4 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all bg-slate-900 text-white shadow-xl shadow-slate-200"
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

        <main class="flex-1 p-6 lg:p-10 pt-20 lg:pt-6">
            <div class="max-w-2xl mx-auto">
                <div class="mb-10 text-center relative">
                    <div class="absolute right-0 top-0">
                        <NotificationBell />
                    </div>
                    <h1
                        class="text-4xl font-black tracking-tighter text-slate-800 uppercase italic"
                    >
                        Jelajah <span class="text-blue-600">Suara</span> 🌍
                    </h1>
                    <p
                        class="text-slate-500 font-medium mt-2 uppercase text-[10px] tracking-widest font-black"
                    >
                        Lihat dan dukung aspirasi teman-temanmu
                    </p>
                </div>

                <div
                    v-for="aspiration in aspirations"
                    :key="aspiration.id_aspiration"
                    class="bg-white rounded-[3rem] border border-slate-100 shadow-sm mb-8 overflow-hidden hover:shadow-xl transition-all duration-500 relative"
                >
                    <div
                        v-if="aspiration.deadline"
                        class="absolute top-6 right-8 flex flex-col items-end gap-1"
                    >
                        <span
                            :class="[
                                'px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest shadow-sm',
                                getSLALabel(aspiration)?.class,
                            ]"
                        >
                            {{ getSLALabel(aspiration)?.text }}
                        </span>
                        <p
                            class="text-[10px] font-bold text-slate-400 uppercase"
                        >
                            Estimasi:
                            {{ getCountdown(aspiration.deadline) }} lagi
                        </p>
                    </div>

                    <div class="p-8">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center gap-4">
                                <div
                                    class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-sm border border-blue-100"
                                >
                                    {{
                                        aspiration.student?.username
                                            ?.substring(0, 2)
                                            .toUpperCase()
                                    }}
                                </div>
                                <div>
                                    <h4
                                        class="font-black text-xs text-slate-800 uppercase tracking-tighter"
                                    >
                                        {{ aspiration.student?.username }}
                                    </h4>
                                    <p
                                        class="text-[10px] text-blue-500 font-black uppercase tracking-widest mt-0.5"
                                    >
                                        {{
                                            aspiration.category
                                                ?.name_category || "Kategori"
                                        }}
                                        • {{ aspiration.location }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <p
                            class="text-slate-600 font-medium text-sm leading-relaxed mb-6"
                        >
                            {{ aspiration.description }}
                        </p>

                        <div class="flex items-center gap-2 mb-6">
                            <span
                                class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest shadow-sm"
                                :class="
                                    aspiration.progress_status === 'Selesai'
                                        ? 'bg-emerald-50 text-emerald-600'
                                        : 'bg-amber-50 text-amber-600'
                                "
                            >
                                {{ aspiration.progress_status }}
                            </span>
                        </div>

                        <div
                            v-if="aspiration.input?.image"
                            class="mb-6 rounded-[2rem] overflow-hidden border border-slate-100 bg-slate-50"
                        >
                            <img
                                :src="`/storage/${aspiration.input.image}`"
                                class="w-full h-auto object-cover max-h-96"
                                alt="Evidence"
                            />
                        </div>

                        <div
                            class="flex items-center gap-4 pt-6 border-t border-slate-50"
                        >
                            <button
                                @click="handleVote(aspiration.id_aspiration)"
                                class="flex items-center gap-2 px-6 py-3 rounded-2xl transition-all"
                                :class="
                                    aspiration.user_has_voted
                                        ? 'bg-blue-600 text-white shadow-lg'
                                        : 'bg-slate-50 text-slate-400 hover:bg-blue-50 hover:text-blue-600'
                                "
                            >
                                <i
                                    class="fas fa-heart text-lg"
                                    :class="
                                        aspiration.user_has_voted
                                            ? 'fas'
                                            : 'far'
                                    "
                                ></i>
                                <span
                                    class="text-xs font-black uppercase tracking-widest"
                                    >{{ aspiration.votes_count }}</span
                                >
                            </button>

                            <button
                                @click="openComment(aspiration)"
                                class="flex items-center gap-2 px-6 py-3 rounded-2xl bg-slate-50 text-slate-400 hover:bg-slate-100 transition-all"
                            >
                                <i class="far fa-comment-dots text-lg"></i>
                                <span
                                    class="text-xs font-black uppercase tracking-widest"
                                    >{{
                                        aspiration.comments?.length || 0
                                    }}
                                    Diskusi</span
                                >
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Comment Modal -->
        <transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
        <div
            v-if="showCommentModal"
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-sm"
            @click.self="showCommentModal = false"
        >
            <transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="opacity-0 translate-y-8 sm:scale-95"
                enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                leave-to-class="opacity-0 translate-y-8 sm:scale-95"
            >
            <div
                v-if="showCommentModal"
                class="bg-white w-full sm:max-w-lg sm:rounded-[2.5rem] rounded-t-[2.5rem] max-h-[85vh] sm:max-h-[80vh] flex flex-col shadow-2xl overflow-hidden"
            >
                <!-- Header with context -->
                <div class="bg-slate-900 p-6 relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-28 h-28 bg-blue-600/20 rounded-full blur-2xl"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                                    <i class="fas fa-comments text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-sm text-white uppercase tracking-widest">Forum Diskusi</h3>
                                    <p class="text-blue-400 text-[10px] font-bold uppercase tracking-wider mt-0.5">
                                        {{ activeAspiration?.comments?.length || 0 }} komentar
                                    </p>
                                </div>
                            </div>
                            <button
                                @click="showCommentModal = false"
                                class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all"
                            >
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        <!-- Aspiration preview -->
                        <div class="bg-white/5 rounded-2xl p-3 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-[10px] font-black">
                                {{ activeAspiration?.student?.username?.substring(0, 2).toUpperCase() }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-white text-xs font-bold truncate">{{ activeAspiration?.location }}</p>
                                <p class="text-slate-400 text-[10px] truncate">{{ activeAspiration?.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="flex-1 overflow-y-auto p-5 space-y-4 bg-gradient-to-b from-slate-50/80 to-white">
                    <!-- Empty state -->
                    <div v-if="!activeAspiration?.comments?.length" class="flex flex-col items-center justify-center py-12">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                            <i class="far fa-comment-dots text-2xl text-slate-300"></i>
                        </div>
                        <p class="font-black text-[10px] text-slate-400 uppercase tracking-widest mb-1">Belum ada diskusi</p>
                        <p class="text-slate-400 text-xs">Jadi yang pertama berkomentar!</p>
                    </div>

                    <!-- Comments list -->
                    <div
                        v-for="(comment, index) in activeAspiration?.comments"
                        :key="comment.id_comment"
                        class="group"
                    >
                        <!-- Own comment (right aligned) -->
                        <div
                            v-if="comment.student_id === student.id_student"
                            class="flex justify-end gap-2"
                        >
                            <div class="max-w-[80%]">
                                <div class="bg-blue-600 px-5 py-3.5 rounded-[1.5rem] rounded-br-lg shadow-sm shadow-blue-200/30">
                                    <p class="text-sm text-white leading-relaxed">{{ comment.body }}</p>
                                </div>
                                <p class="text-[9px] font-bold text-slate-400 mt-1.5 text-right mr-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{ comment.created_at ? new Date(comment.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : '' }}
                                </p>
                            </div>
                            <div class="w-8 h-8 shrink-0 rounded-xl bg-blue-600 text-white flex items-center justify-center text-[9px] font-black shadow-sm">
                                {{ comment.student?.username?.substring(0, 2).toUpperCase() }}
                            </div>
                        </div>

                        <!-- Other's comment (left aligned) -->
                        <div v-else class="flex gap-2">
                            <div class="w-8 h-8 shrink-0 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center text-[9px] font-black border border-slate-200">
                                {{ comment.student?.username?.substring(0, 2).toUpperCase() }}
                            </div>
                            <div class="max-w-[80%]">
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-wider mb-1 ml-2">
                                    {{ comment.student?.username }}
                                </p>
                                <div class="bg-white px-5 py-3.5 rounded-[1.5rem] rounded-tl-lg border border-slate-100 shadow-sm">
                                    <p class="text-sm text-slate-700 leading-relaxed">{{ comment.body }}</p>
                                </div>
                                <p class="text-[9px] font-bold text-slate-400 mt-1.5 ml-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{ comment.created_at ? new Date(comment.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input area -->
                <div class="p-4 bg-white border-t border-slate-100">
                    <form @submit.prevent="submitComment" class="flex items-end gap-3">
                        <div class="flex-1 relative">
                            <textarea
                                v-model="commentForm.body"
                                class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-3.5 text-sm font-medium focus:ring-2 focus:ring-blue-600/10 focus:border-blue-300 resize-none outline-none transition-all placeholder:text-slate-400"
                                placeholder="Tulis komentar..."
                                rows="1"
                                @input="$event.target.style.height = 'auto'; $event.target.style.height = Math.min($event.target.scrollHeight, 100) + 'px'"
                                @keydown.enter.exact.prevent="submitComment"
                            ></textarea>
                        </div>
                        <button
                            type="submit"
                            :disabled="commentForm.processing || !commentForm.body.trim()"
                            class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200 hover:bg-slate-900 disabled:bg-slate-200 disabled:shadow-none transition-all active:scale-90 shrink-0"
                        >
                            <i class="fas fa-paper-plane text-xs" :class="{ 'animate-pulse': commentForm.processing }"></i>
                        </button>
                    </form>
                    <p class="text-[9px] text-slate-400 font-medium mt-2 text-center">
                        Tekan <span class="font-black">Enter</span> untuk kirim
                    </p>
                </div>
            </div>
            </transition>
        </div>
        </transition>
    </div>
</template>

<style scoped>
/* Hide scrollbar tapi tetap bisa scroll */
:deep(.overflow-y-auto),
:deep(textarea) {
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}
:deep(.overflow-y-auto)::-webkit-scrollbar,
:deep(textarea)::-webkit-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}
</style>