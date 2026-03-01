<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import Swal from "sweetalert2";
import NotificationBell from "@/Components/NotificationBell.vue";

const props = defineProps({
    aspiration: Object,
});

// --- LOGIC RATING ---
const hoverRating = ref(0);
const ratingForm = useForm({
    rating: props.aspiration.rating || 0,
    feedback: props.aspiration.feedback || "",
});

const submitRating = () => {
    ratingForm.post(
        `/student/input-aspirations/${props.aspiration.id_input}/rate`,
        {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    icon: "success",
                    title: "MANTAP CUY!",
                    text: "Rating lo ngebantu banget buat evaluasi tim sarpras.",
                    borderRadius: "2rem",
                    confirmButtonColor: "#2563eb",
                });
            },
        },
    );
};

// Helper warna status
const statusTheme = (status) => {
    switch (status) {
        case "menunggu":
            return "bg-amber-500 shadow-amber-200 text-white";
        case "diterima":
            return "bg-blue-600 shadow-blue-200 text-white";
        case "ditolak":
            return "bg-rose-600 shadow-rose-200 text-white";
        default:
            return "bg-slate-500 text-white";
    }
};

// SLA helpers
const remainingDays = computed(() => {
    if (!props.aspiration.aspiration?.end_at) return null;
    const now = new Date();
    const end = new Date(props.aspiration.aspiration.end_at);
    const diff = Math.ceil((end - now) / (1000 * 60 * 60 * 24));
    return diff;
});

const priorityBadge = computed(() => {
    const level = props.aspiration.aspiration?.priority_level;
    switch (level) {
        case "Emergency":
            return { text: "EMERGENCY", class: "bg-rose-500 text-white animate-pulse" };
        case "Urgent":
            return { text: "URGENT", class: "bg-amber-500 text-white" };
        default:
            return { text: "NORMAL", class: "bg-blue-100 text-blue-600" };
    }
});
</script>

<template>
    <Head title="Detail Aspirasi" />

    <div class="min-h-screen bg-slate-50/50 p-6 lg:p-10 font-sans">
        <div class="max-w-6xl mx-auto">
            <div
                class="flex flex-col md:flex-row items-center justify-between mb-10 gap-4"
            >
                <Link
                    href="/student/input-aspirations"
                    class="flex items-center gap-3 text-slate-500 hover:text-blue-600 font-black transition group uppercase text-xs tracking-widest"
                >
                    <div
                        class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-all"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </div>
                    Kembali ke Riwayat
                </Link>
                <div class="flex items-center gap-3">
                    <NotificationBell />
                    <div
                        :class="[
                            'px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl transition-all',
                            statusTheme(aspiration.submission_status),
                        ]"
                    >
                        Status Pengajuan: {{ aspiration.submission_status }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    <div
                        class="bg-white rounded-[3rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden"
                    >
                        <div class="relative h-80 w-full bg-slate-100">
                            <img
                                v-if="aspiration.image"
                                :src="`/storage/${aspiration.image}`"
                                class="w-full h-full object-cover"
                            />
                            <div
                                v-else
                                class="w-full h-full flex flex-col items-center justify-center text-slate-300"
                            >
                                <i class="fas fa-image text-6xl mb-4"></i>
                                <p
                                    class="text-[10px] font-black uppercase tracking-widest"
                                >
                                    No Image Attached
                                </p>
                            </div>
                            <div
                                class="absolute top-6 right-6 px-5 py-2 bg-white/90 backdrop-blur-md rounded-2xl shadow-sm border border-white/50"
                            >
                                <p
                                    class="text-[10px] font-black text-blue-600 uppercase tracking-widest"
                                >
                                    Kategori
                                </p>
                                <p
                                    class="text-sm font-black text-slate-800 uppercase italic"
                                >
                                    {{ aspiration.category?.category_name }}
                                </p>
                            </div>
                        </div>

                        <div class="p-10 space-y-8">
                            <div>
                                <h3
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3"
                                >
                                    📍 Lokasi Kejadian
                                </h3>
                                <p
                                    class="text-2xl font-black text-slate-800 tracking-tighter uppercase italic"
                                >
                                    {{ aspiration.location }}
                                </p>
                            </div>

                            <div
                                class="p-8 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200 relative"
                            >
                                <i
                                    class="fas fa-quote-left absolute top-4 left-4 text-slate-200 text-2xl"
                                ></i>
                                <h3
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4"
                                >
                                    Deskripsi Masalah
                                </h3>
                                <p
                                    class="text-slate-600 leading-relaxed font-bold italic text-lg text-center md:text-left"
                                >
                                    "{{ aspiration.description }}"
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="aspiration.admin_message"
                        class="bg-slate-900 rounded-[2.5rem] p-10 text-white shadow-2xl shadow-slate-300 relative overflow-hidden"
                    >
                        <div
                            class="absolute -right-4 -top-4 w-24 h-24 bg-blue-600/20 rounded-full blur-2xl"
                        ></div>
                        <div class="flex items-start gap-6 relative z-10">
                            <div
                                class="w-14 h-14 rounded-2xl bg-blue-600 flex items-center justify-center text-2xl shadow-lg shadow-blue-500/20"
                            >
                                <i class="fas fa-comment-dots"></i>
                            </div>
                            <div class="flex-1">
                                <h4
                                    class="font-black uppercase tracking-[0.2em] text-[10px] text-blue-400 mb-2"
                                >
                                    Tanggapan Admin
                                </h4>
                                <p
                                    class="text-base leading-relaxed font-medium text-slate-300"
                                >
                                    {{ aspiration.admin_message }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback Timeline from Admin/Ketua -->
                    <div v-if="aspiration.aspiration?.feedbacks?.length" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 p-10">
                        <h3 class="font-black text-slate-800 uppercase italic tracking-tighter text-xl mb-2 flex items-center gap-3">
                            <i class="fas fa-comments text-blue-600"></i> Update dari Tim
                        </h3>
                        <p class="text-slate-500 text-xs font-medium mb-8 uppercase tracking-widest">
                            Progress &amp; arahan terbaru dari admin/ketua
                        </p>

                        <div class="relative space-y-6 before:absolute before:left-5 before:top-0 before:h-full before:w-0.5 before:bg-slate-100">
                            <div v-for="fb in aspiration.aspiration.feedbacks" :key="fb.id_feedback" class="relative flex items-start pl-14">
                                <div
                                    :class="['absolute left-0 flex h-10 w-10 items-center justify-center rounded-2xl shadow-lg',
                                        fb.user?.roles?.[0]?.name === 'ketua_yayasan'
                                            ? 'bg-amber-500 text-white shadow-amber-200'
                                            : 'bg-blue-600 text-white shadow-blue-200']"
                                >
                                    <i :class="fb.user?.roles?.[0]?.name === 'ketua_yayasan' ? 'fas fa-crown' : 'fas fa-user-shield'" class="text-sm"></i>
                                </div>
                                <div class="flex-1 bg-slate-50 p-6 rounded-[2rem] border border-slate-100">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-black text-slate-800 uppercase">{{ fb.user?.username || 'System' }}</span>
                                            <span
                                                :class="['px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider',
                                                    fb.user?.roles?.[0]?.name === 'ketua_yayasan'
                                                        ? 'bg-amber-100 text-amber-700'
                                                        : 'bg-blue-100 text-blue-700']"
                                            >
                                                {{ fb.user?.roles?.[0]?.name === 'ketua_yayasan' ? 'KETUA' : 'ADMIN' }}
                                            </span>
                                        </div>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                                            {{ new Date(fb.feedback_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                                        </span>
                                    </div>
                                    <p class="text-slate-600 font-bold italic text-sm leading-relaxed">"{{ fb.message }}"</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rating section (when progress = Selesai) -->
                    <div
                        v-if="aspiration.aspiration?.progress_status === 'Selesai'"
                        class="bg-white rounded-[2.5rem] border-4 border-blue-50 p-10 shadow-xl shadow-slate-200/50"
                    >
                        <h3 class="font-black text-slate-800 uppercase italic tracking-tighter text-xl mb-2">
                            Kasih Rating Perbaikan 🌟
                        </h3>
                        <p class="text-slate-500 text-xs font-medium mb-8 uppercase tracking-widest">
                            Feedback anda penting buat evaluasi sarpras
                        </p>

                        <div v-if="!aspiration.rating" class="space-y-8">
                            <div
                                class="flex gap-4 bg-slate-50 w-fit p-4 rounded-3xl border border-slate-100"
                            >
                                <button
                                    v-for="star in 5"
                                    :key="star"
                                    type="button"
                                    @click="ratingForm.rating = star"
                                    @mouseenter="hoverRating = star"
                                    @mouseleave="hoverRating = 0"
                                    class="text-4xl transition-all duration-200 transform hover:scale-125 active:scale-90"
                                >
                                    <i
                                        :class="[
                                            (hoverRating ||
                                                ratingForm.rating) >= star
                                                ? 'fas fa-star text-amber-400 drop-shadow-md'
                                                : 'far fa-star text-slate-200',
                                        ]"
                                    ></i>
                                </button>
                            </div>

                            <div class="space-y-3">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4"
                                    >Komentar Feedback</label
                                >
                                <textarea
                                    v-model="ratingForm.feedback"
                                    rows="3"
                                    class="w-full bg-slate-50 border border-slate-100 rounded-[2rem] p-6 text-sm font-bold focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 transition-all outline-none"
                                    placeholder="Contoh: Perbaikannya cepet banget, mantap tim sarpras!"
                                ></textarea>
                            </div>

                            <button
                                @click="submitRating"
                                :disabled="
                                    ratingForm.rating === 0 ||
                                    ratingForm.processing
                                "
                                class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-900 transition-all shadow-xl shadow-blue-200 disabled:opacity-30 disabled:grayscale"
                            >
                                <span v-if="ratingForm.processing"
                                    >MENGIRIM...</span
                                >
                                <span v-else>Kirim Feedback Sekarang</span>
                            </button>
                        </div>

                        <div
                            v-else
                            class="flex flex-col md:flex-row items-center gap-8 bg-slate-50 p-8 rounded-[2rem] border border-slate-200"
                        >
                            <div
                                class="text-center md:border-r md:pr-8 border-slate-200"
                            >
                                <div
                                    class="text-5xl font-black text-slate-800 mb-1"
                                >
                                    {{ aspiration.rating }}
                                </div>
                                <div class="flex text-amber-400 gap-1">
                                    <i
                                        v-for="s in 5"
                                        :key="s"
                                        :class="
                                            s <= aspiration.rating
                                                ? 'fas fa-star'
                                                : 'far fa-star'
                                        "
                                    ></i>
                                </div>
                            </div>
                            <div class="flex-1 text-center md:text-left">
                                <p
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2"
                                >
                                    Feedback dari anda:
                                </p>
                                <p
                                    class="text-slate-700 italic font-black text-lg"
                                >
                                    "{{
                                        aspiration.feedback ||
                                        "Gak ada komentar tambahan."
                                    }}"
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div
                        class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-xl shadow-slate-200/50 sticky top-10"
                    >
                        <h3
                            class="font-black text-slate-800 uppercase tracking-tighter text-lg mb-8 italic flex items-center gap-3"
                        >
                            <i class="fas fa-stream text-blue-600 text-sm"></i>
                            Timeline Progress
                        </h3>

                        <div
                            v-if="!aspiration.aspiration"
                            class="text-center py-12"
                        >
                            <div
                                class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 animate-spin-slow"
                            >
                                <i
                                    class="fas fa-hourglass-half text-slate-300 text-3xl"
                                ></i>
                            </div>
                            <p
                                class="text-[10px] font-black text-slate-400 uppercase leading-relaxed px-6 tracking-widest"
                            >
                                Menunggu verifikasi tim sarana prasarana.
                            </p>
                        </div>

                        <!-- Priority Badge (outside timeline) -->
                        <div v-if="aspiration.aspiration?.priority_level" class="flex items-center gap-3 mb-6 ml-1">
                            <span
                                :class="['px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm', priorityBadge.class]"
                            >
                                {{ priorityBadge.text }}
                            </span>
                            <span v-if="remainingDays !== null" class="text-[10px] font-black uppercase tracking-wider"
                                :class="remainingDays <= 0 ? 'text-rose-500' : remainingDays <= 3 ? 'text-amber-500' : 'text-slate-500'"
                            >
                                {{ remainingDays <= 0 ? 'OVERDUE' : remainingDays + ' hari lagi' }}
                            </span>
                        </div>

                        <div
                            v-if="aspiration.aspiration"
                            class="relative space-y-10 before:absolute before:inset-0 before:ml-5 before:h-full before:w-0.5 before:bg-slate-100"
                        >
                            <div class="relative flex items-start">
                                <div
                                    class="absolute left-0 flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-lg shadow-emerald-200"
                                >
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="ml-16">
                                    <h4
                                        class="text-xs font-black text-slate-800 uppercase italic"
                                    >
                                        Validasi Selesai
                                    </h4>
                                    <p
                                        class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter mb-2"
                                    >
                                        {{
                                            new Date(
                                                aspiration.aspiration
                                                    .validated_at,
                                            ).toLocaleDateString("id-ID", {
                                                day: "numeric",
                                                month: "long",
                                                year: "numeric",
                                            })
                                        }}
                                    </p>
                                    <div
                                        class="text-[10px] text-slate-500 font-bold bg-slate-50 p-4 rounded-2xl border border-slate-100"
                                    >
                                        "Laporan telah disetujui untuk
                                        diperbaiki."
                                    </div>
                                </div>
                            </div>

                            <div class="relative flex items-start">
                                <div
                                    :class="[
                                        'absolute left-0 flex h-10 w-10 items-center justify-center rounded-2xl shadow-lg transition-all',
                                        aspiration.aspiration
                                            .progress_status === 'Selesai'
                                            ? 'bg-emerald-500 text-white shadow-emerald-200'
                                            : 'bg-blue-600 text-white animate-pulse shadow-blue-200',
                                    ]"
                                >
                                    <i
                                        :class="
                                            aspiration.aspiration
                                                .progress_status === 'Selesai'
                                                ? 'fas fa-check-double'
                                                : 'fas fa-tools'
                                        "
                                    ></i>
                                </div>
                                <div class="ml-16">
                                    <h4
                                        class="text-xs font-black text-slate-800 uppercase italic"
                                    >
                                        Status:
                                        {{
                                            aspiration.aspiration
                                                .progress_status
                                        }}
                                    </h4>
                                    <div class="mt-4 space-y-2">
                                        <div
                                            v-if="
                                                aspiration.aspiration.start_at
                                            "
                                            class="flex items-center gap-2"
                                        >
                                            <span
                                                class="w-1.5 h-1.5 rounded-full bg-blue-600"
                                            ></span>
                                            <p
                                                class="text-[9px] font-black text-slate-400 uppercase"
                                            >
                                                Mulai:
                                                {{
                                                    new Date(aspiration.aspiration.start_at).toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" })
                                                }}
                                            </p>
                                        </div>
                                        <div
                                            v-if="aspiration.aspiration.end_at"
                                            class="flex items-center gap-2"
                                        >
                                            <span
                                                class="w-1.5 h-1.5 rounded-full bg-emerald-500"
                                            ></span>
                                            <p
                                                class="text-[9px] font-black uppercase tracking-widest"
                                                :class="remainingDays <= 0 ? 'text-rose-500' : 'text-emerald-500'"
                                            >
                                                Deadline:
                                                {{
                                                    new Date(aspiration.aspiration.end_at).toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" })
                                                }}
                                            </p>
                                        </div>
                                        <!-- SLA Countdown Bar -->
                                        <div v-if="aspiration.aspiration.end_at && aspiration.aspiration.start_at && aspiration.aspiration.progress_status !== 'Selesai'" class="mt-4 pt-3 border-t border-slate-100">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Estimasi Sisa</span>
                                                <span class="text-[10px] font-black"
                                                    :class="remainingDays <= 0 ? 'text-rose-500' : remainingDays <= 3 ? 'text-amber-500' : 'text-blue-600'"
                                                >
                                                    {{ remainingDays <= 0 ? 'Terlambat!' : remainingDays + ' hari' }}
                                                </span>
                                            </div>
                                            <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                                <div
                                                    class="h-full rounded-full transition-all duration-1000"
                                                    :class="remainingDays <= 0 ? 'bg-rose-500' : remainingDays <= 3 ? 'bg-amber-500' : 'bg-blue-600'"
                                                    :style="{ width: Math.max(0, Math.min(100, (remainingDays / ((new Date(aspiration.aspiration.end_at) - new Date(aspiration.aspiration.start_at)) / (1000*60*60*24))) * 100)) + '%' }"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        v-if="
                                            aspiration.aspiration
                                                .progress_status === 'Selesai'
                                        "
                                        class="mt-6 bg-emerald-50 text-emerald-600 px-4 py-3 rounded-2xl border border-emerald-100 text-[9px] font-black uppercase text-center tracking-widest shadow-sm"
                                    >
                                        Fixed & Ready to Use! ✅
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.animate-spin-slow {
    animation: spin 3s linear infinite;
}
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
